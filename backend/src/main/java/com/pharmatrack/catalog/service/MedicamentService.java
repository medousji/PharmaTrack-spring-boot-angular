package com.pharmatrack.catalog.service;

import com.pharmatrack.catalog.dto.MedicamentCreateRequest;
import com.pharmatrack.catalog.dto.MedicamentDetailResponse;
import com.pharmatrack.catalog.dto.MedicamentResponse;
import com.pharmatrack.catalog.dto.MedicamentUpdateRequest;
import com.pharmatrack.catalog.dto.LotExpirySummary;
import com.pharmatrack.catalog.dto.StockSummary;
import com.pharmatrack.catalog.entity.LotStatut;
import com.pharmatrack.catalog.entity.Medicament;
import com.pharmatrack.catalog.entity.MedicamentStatut;
import com.pharmatrack.catalog.mapper.MedicamentMapper;
import com.pharmatrack.catalog.repository.MedicamentRepository;
import com.pharmatrack.catalog.repository.MedicamentSpecifications;
import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.api.PageUtils;
import com.pharmatrack.common.error.ConflictException;
import com.pharmatrack.common.error.ResourceNotFoundException;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.UUID;
import java.util.stream.Collectors;

/**
 * Catalog service. Computed stock fields are produced from a small number of
 * SQL GROUP BY queries batched over the page - never by looping lots in
 * application memory.
 */
@Service
@Transactional
public class MedicamentService {

    static final int NEAR_EXPIRY_HORIZON_DAYS = 30;

    private final MedicamentRepository repository;
    private final MedicamentMapper mapper;

    public MedicamentService(MedicamentRepository repository, MedicamentMapper mapper) {
        this.repository = repository;
        this.mapper = mapper;
    }

    @Transactional(readOnly = true)
    public PagedResponse<MedicamentDetailResponse> list(
            String search, String classeTherapeutique, MedicamentStatut statut,
            Boolean enRupture, Boolean prochePeremption, Integer page, Integer size) {

        Specification<Medicament> spec = Specification
                .where(MedicamentSpecifications.search(search))
                .and(MedicamentSpecifications.classeTherapeutique(classeTherapeutique))
                .and(MedicamentSpecifications.statut(statut))
                .and(MedicamentSpecifications.enRupture(enRupture, LotStatut.actif))
                .and(MedicamentSpecifications.prochePeremption(
                        prochePeremption, LotStatut.actif,
                        LocalDate.now().plusDays(NEAR_EXPIRY_HORIZON_DAYS)));

        Pageable pageable = PageRequest.of(PageUtils.clampPage(page), PageUtils.clampSize(size));
        Page<Medicament> result = repository.findAll(spec, pageable);

        Map<UUID, StockSummary> stocks = loadStock(result.getContent());
        Map<UUID, LocalDate> nearExpiry = loadNearExpiry(result.getContent());

        List<MedicamentDetailResponse> content = result.getContent().stream()
                .map(m -> toDetail(m, stocks.get(m.getId()), nearExpiry.get(m.getId())))
                .toList();

        return new PagedResponse<>(content, result.getNumber(), result.getSize(),
                result.getTotalElements(), result.getTotalPages());
    }

    @Transactional(readOnly = true)
    public MedicamentDetailResponse getDetail(UUID id) {
        Medicament medicament = getEntity(id);
        Map<UUID, StockSummary> stocks = loadStock(List.of(medicament));
        Map<UUID, LocalDate> nearExpiry = loadNearExpiry(List.of(medicament));
        return toDetail(medicament, stocks.get(id), nearExpiry.get(id));
    }

    @Transactional(readOnly = true)
    public MedicamentResponse get(UUID id) {
        return mapper.toResponse(getEntity(id));
    }

    public MedicamentResponse create(MedicamentCreateRequest request) {
        if (repository.existsByCodeCip(request.codeCip())) {
            throw new ConflictException("A medicament with codeCip '" + request.codeCip() + "' already exists.");
        }
        Medicament medicament = mapper.toEntity(request);
        medicament.setStatut(MedicamentStatut.actif);
        medicament.setMedicamentReference(resolveReference(request.medicamentReferenceId()));
        return mapper.toResponse(repository.save(medicament));
    }

    public MedicamentResponse update(UUID id, MedicamentUpdateRequest request) {
        Medicament medicament = getEntity(id);
        if (repository.existsByCodeCip(request.codeCip())
                && !medicament.getCodeCip().equals(request.codeCip())) {
            throw new ConflictException("codeCip '" + request.codeCip() + "' is already in use.");
        }
        mapper.update(request, medicament);
        medicament.setStatut(request.statut() != null
                ? MedicamentStatut.valueOf(request.statut().name()) : medicament.getStatut());
        medicament.setMedicamentReference(resolveReference(request.medicamentReferenceId()));
        return mapper.toResponse(repository.save(medicament));
    }

    public void retire(UUID id) {
        Medicament medicament = getEntity(id);
        medicament.setStatut(MedicamentStatut.retire);
        repository.save(medicament);
    }

    private Medicament getEntity(UUID id) {
        return repository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("medicament", id));
    }

    private Medicament resolveReference(UUID referenceId) {
        if (referenceId == null) {
            return null;
        }
        return repository.findById(referenceId)
                .orElseThrow(() -> new ResourceNotFoundException("medicament reference", referenceId));
    }

    /**
     * Batched SQL aggregate over exactly the page's medicament ids.
     */
    private Map<UUID, StockSummary> loadStock(List<Medicament> medicaments) {
        if (medicaments.isEmpty()) {
            return Map.of();
        }
        Set<UUID> ids = medicaments.stream().map(Medicament::getId).collect(Collectors.toSet());
        return repository.aggregateStock(ids, LotStatut.actif, LotStatut.perime).stream()
                .collect(Collectors.toMap(StockSummary::medicamentId, s -> s, (a, b) -> a));
    }

    /**
     * Batched SQL query for the earliest near-expiry date per medicament.
     */
    private Map<UUID, LocalDate> loadNearExpiry(List<Medicament> medicaments) {
        if (medicaments.isEmpty()) {
            return Map.of();
        }
        Set<UUID> ids = medicaments.stream().map(Medicament::getId).collect(Collectors.toSet());
        LocalDate horizon = LocalDate.now().plusDays(NEAR_EXPIRY_HORIZON_DAYS);
        Map<UUID, LocalDate> earliest = new HashMap<>();
        for (LotExpirySummary row : repository.findNearExpiry(ids, LotStatut.actif, LocalDate.now(), horizon)) {
            earliest.putIfAbsent(row.medicamentId(), row.datePeremption());
        }
        return earliest;
    }

    private MedicamentDetailResponse toDetail(Medicament m, StockSummary stock, LocalDate expiring) {
        long stockActif = stock == null ? 0 : stock.stockActif();
        long stockTotal = stock == null ? 0 : stock.stockTotal();
        int stockMin = m.getStockMin() == null ? 0 : m.getStockMin();
        boolean enRupture = stockActif < stockMin;
        boolean prochePeremption = expiring != null;
        BigDecimal valeur = stock == null ? BigDecimal.ZERO : stock.valeurStock();
        return mapper.toDetail(m, (int) stockActif, (int) stockTotal, enRupture,
                prochePeremption, expiring, valeur);
    }
}
