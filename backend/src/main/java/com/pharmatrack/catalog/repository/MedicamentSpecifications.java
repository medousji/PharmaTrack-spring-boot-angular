package com.pharmatrack.catalog.repository;

import com.pharmatrack.catalog.entity.Lot;
import com.pharmatrack.catalog.entity.LotStatut;
import com.pharmatrack.catalog.entity.Medicament;
import com.pharmatrack.catalog.entity.MedicamentStatut;
import jakarta.persistence.criteria.Root;
import jakarta.persistence.criteria.Subquery;
import org.springframework.data.jpa.domain.Specification;

import java.time.LocalDate;

/**
 * SQL-backed (criteria/subquery) filters for medicament listing - no loading
 * whole tables into application memory to filter.
 */
public final class MedicamentSpecifications {

    private MedicamentSpecifications() {
    }

    public static Specification<Medicament> search(String term) {
        if (term == null || term.isBlank()) {
            return null;
        }
        String like = "%" + term.trim().toLowerCase() + "%";
        return (root, query, cb) -> cb.or(
                cb.like(cb.lower(root.get("codeCip")), like),
                cb.like(cb.lower(root.get("nomCommercialFr")), like),
                cb.like(cb.lower(root.get("nomCommercialAr")), like),
                cb.like(cb.lower(root.get("dci")), like));
    }

    public static Specification<Medicament> classeTherapeutique(String classe) {
        if (classe == null || classe.isBlank()) {
            return null;
        }
        return (root, query, cb) ->
                cb.equal(cb.lower(root.get("classeTherapeutique")), classe.trim().toLowerCase());
    }

    public static Specification<Medicament> statut(MedicamentStatut statut) {
        return statut == null ? null : (root, query, cb) -> cb.equal(root.get("statut"), statut);
    }

    /**
     * Medicaments whose SQL-aggregated active stock is below stock_min.
     */
    public static Specification<Medicament> enRupture(Boolean enRupture, LotStatut activeStatut) {
        if (enRupture == null || !enRupture) {
            return null;
        }
        return (root, query, cb) -> {
            Subquery<Long> sub = query.subquery(Long.class);
            Root<Lot> lot = sub.from(Lot.class);
            sub.select(cb.sum(lot.get("quantiteActuelle")));
            sub.where(cb.and(
                    cb.equal(lot.get("medicament"), root),
                    cb.equal(lot.get("statut"), activeStatut)));
            return cb.lessThan(cb.coalesce(sub, 0L), root.get("stockMin").as(Long.class));
        };
    }

    /**
     * Medicaments that have at least one lot expiring within the horizon with
     * stock still on hand.
     */
    public static Specification<Medicament> prochePeremption(Boolean prochePeremption,
                                                             LotStatut activeStatut,
                                                             LocalDate horizon) {
        if (prochePeremption == null || !prochePeremption) {
            return null;
        }
        return (root, query, cb) -> {
            Subquery<Lot> sub = query.subquery(Lot.class);
            Root<Lot> lot = sub.from(Lot.class);
            sub.select(lot);
            sub.where(cb.and(
                    cb.equal(lot.get("medicament"), root),
                    cb.equal(lot.get("statut"), activeStatut),
                    cb.greaterThan(lot.get("quantiteActuelle"), 0),
                    cb.lessThanOrEqualTo(lot.get("datePeremption"), horizon)));
            return cb.exists(sub);
        };
    }
}
