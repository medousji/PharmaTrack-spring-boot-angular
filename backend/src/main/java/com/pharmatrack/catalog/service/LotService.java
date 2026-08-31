package com.pharmatrack.catalog.service;

import com.pharmatrack.catalog.dto.LotCreateRequest;
import com.pharmatrack.catalog.dto.LotResponse;
import com.pharmatrack.catalog.dto.LotStatutDto;
import com.pharmatrack.catalog.dto.LotUpdateRequest;
import com.pharmatrack.catalog.dto.MouvementTypeDto;
import com.pharmatrack.catalog.dto.StockAdjustmentRequest;
import com.pharmatrack.catalog.dto.StockAdjustmentResponse;
import com.pharmatrack.catalog.entity.Lot;
import com.pharmatrack.catalog.entity.LotStatut;
import com.pharmatrack.catalog.entity.Medicament;
import com.pharmatrack.catalog.entity.Mouvement;
import com.pharmatrack.catalog.entity.MouvementType;
import com.pharmatrack.catalog.mapper.LotMapper;
import com.pharmatrack.catalog.mapper.MouvementMapper;
import com.pharmatrack.catalog.repository.LotRepository;
import com.pharmatrack.catalog.repository.MedicamentRepository;
import com.pharmatrack.catalog.repository.MouvementRepository;
import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.api.PageUtils;
import com.pharmatrack.common.error.ConflictException;
import com.pharmatrack.common.error.ResourceNotFoundException;
import com.pharmatrack.common.security.CurrentUser;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.util.List;
import java.util.UUID;

@Service
@Transactional
public class LotService {

    private static final int MAX_FEFO = 1;

    private final LotRepository lotRepository;
    private final MedicamentRepository medicamentRepository;
    private final MouvementRepository mouvementRepository;
    private final LotMapper lotMapper;
    private final MouvementMapper mouvementMapper;
    private final CurrentUser currentUser;

    public LotService(LotRepository lotRepository,
                      MedicamentRepository medicamentRepository,
                      MouvementRepository mouvementRepository,
                      LotMapper lotMapper,
                      MouvementMapper mouvementMapper,
                      CurrentUser currentUser) {
        this.lotRepository = lotRepository;
        this.medicamentRepository = medicamentRepository;
        this.mouvementRepository = mouvementRepository;
        this.lotMapper = lotMapper;
        this.mouvementMapper = mouvementMapper;
        this.currentUser = currentUser;
    }

    @Transactional(readOnly = true)
    public PagedResponse<LotResponse> listByMedicament(UUID medicamentId, LotStatut statut,
                                                        Integer page, Integer size) {
        PageableHolder holder = new PageableHolder(page, size);
        Page<Lot> result = statut == null
                ? lotRepository.findByMedicamentId(medicamentId, holder.pageable())
                : lotRepository.findByMedicamentIdAndStatut(medicamentId, statut, holder.pageable());
        return toPaged(result);
    }

    @Transactional(readOnly = true)
    public PagedResponse<LotResponse> listAll(Integer prochePeremptionJours, LotStatut statut,
                                              Integer page, Integer size) {
        PageableHolder holder = new PageableHolder(page, size);
        Page<Lot> result;
        if (prochePeremptionJours != null && prochePeremptionJours > 0) {
            result = lotRepository.findByExpiryBefore(
                    LocalDate.now().plusDays(prochePeremptionJours), statut, holder.pageable());
        } else {
            result = statut == null
                    ? lotRepository.findAll(holder.pageable())
                    : lotRepository.findByStatut(statut, holder.pageable());
        }
        return toPaged(result);
    }

    @Transactional(readOnly = true)
    public LotResponse get(UUID id) {
        return lotMapper.toResponse(getEntity(id));
    }

    /**
     * Receive a lot into stock. Creates the Lot and records the initial
     * "entree" Mouvement atomically.
     */
    public LotResponse receive(LotCreateRequest request) {
        Medicament medicament = medicamentRepository.findById(request.medicamentId())
                .orElseThrow(() -> new ResourceNotFoundException("medicament", request.medicamentId()));

        Lot lot = lotMapper.toEntity(request);
        lot.setMedicament(medicament);
        lot.setQuantiteActuelle(request.quantiteInitiale());
        lot.setStatut(LotStatut.actif);
        if (lot.getDateReception() == null) {
            lot.setDateReception(LocalDate.now());
        }
        lot = lotRepository.save(lot);

        recordMouvement(lot, MouvementType.entree, request.quantiteInitiale(),
                0, request.quantiteInitiale(), "Réception du lot " + request.numeroLot(),
                request.numeroFacture() != null ? request.numeroFacture() : null);

        return lotMapper.toResponse(lot);
    }

    /**
     * Update lot metadata only. Quantity is never mutable here - use
     * adjustStock so every change is audited in the ledger.
     */
    public LotResponse update(UUID id, LotUpdateRequest request) {
        Lot lot = getEntity(id);
        lotMapper.update(request, lot);
        if (request.statut() != null) {
            lot.setStatut(LotStatut.valueOf(request.statut().name()));
        }
        return lotMapper.toResponse(lotRepository.save(lot));
    }

    /**
     * Explicit, validated, auditable stock adjustment replacing the legacy
     * {@code Lot::modifierQuantite()}. Rejects any change that would make
     * resulting stock negative (409), including a decrement beyond on-hand.
     */
    public StockAdjustmentResponse adjustStock(UUID id, StockAdjustmentRequest request) {
        Lot lot = getEntity(id);

        int current = lot.getQuantiteActuelle();
        int after;
        if (request.type() == MouvementTypeDto.sortie) {
            after = current - request.quantite();
            if (after < 0) {
                throw new ConflictException("Quantité insuffisante en stock : "
                        + current + " disponible, " + request.quantite() + " demandée.");
            }
        } else {
            after = current + request.quantite();
        }

        lot.setQuantiteActuelle(after);
        if (after == 0 && lot.getStatut() == LotStatut.actif) {
            lot.setStatut(LotStatut.epuise);
        } else if (after > 0 && lot.getStatut() == LotStatut.epuise) {
            lot.setStatut(LotStatut.actif);
        }
        lotRepository.save(lot);

        MouvementType type = MouvementType.valueOf(request.type().name());
        recordMouvement(lot, type, request.quantite(), current, after,
                request.motif(), request.reference());
        return new StockAdjustmentResponse(lotMapper.toResponse(lot),
                mouvementMapper.toResponse(lastMouvement(lot)));
    }

    /**
     * FEFO: the single next lot to dispense for a medicament - the
     * earliest-expiring active lot with stock on hand.
     */
    public LotResponse nextToDispense(UUID medicamentId) {
        List<Lot> lots = lotRepository.findNextToDispense(
                medicamentId, LotStatut.actif, PageRequest.of(0, MAX_FEFO));
        if (lots.isEmpty()) {
            throw new ResourceNotFoundException("lot disponible", medicamentId);
        }
        return lotMapper.toResponse(lots.get(0));
    }

    private void recordMouvement(Lot lot, MouvementType type, int quantite,
                                 int avant, int apres, String motif, String reference) {
        Mouvement mouvement = new Mouvement();
        mouvement.setLot(lot);
        mouvement.setType(type);
        mouvement.setQuantite(quantite);
        mouvement.setQuantiteAvant(avant);
        mouvement.setQuantiteApres(apres);
        mouvement.setMotif(motif);
        mouvement.setReference(reference);
        currentUser.id().ifPresent(mouvement::setUserId);
        mouvementRepository.save(mouvement);
    }

    private Mouvement lastMouvement(Lot lot) {
        return mouvementRepository.findByLotId(lot.getId(),
                        PageRequest.of(0, 1, org.springframework.data.domain.Sort.Direction.DESC, "createdAt"))
                .getContent().stream().findFirst().orElse(null);
    }

    private Lot getEntity(UUID id) {
        return lotRepository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("lot", id));
    }

    private PagedResponse<LotResponse> toPaged(Page<Lot> page) {
        List<LotResponse> content = page.getContent().stream().map(lotMapper::toResponse).toList();
        return new PagedResponse<>(content, page.getNumber(), page.getSize(),
                page.getTotalElements(), page.getTotalPages());
    }

    private record PageableHolder(int page, int size) {
        private org.springframework.data.domain.Pageable pageable() {
            return PageRequest.of(PageUtils.clampPage(page), PageUtils.clampSize(size));
        }
    }
}
