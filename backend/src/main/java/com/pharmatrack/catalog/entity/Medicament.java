package com.pharmatrack.catalog.entity;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.OneToMany;
import jakarta.persistence.Table;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UuidGenerator;
import org.hibernate.annotations.UpdateTimestamp;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.HashSet;
import java.util.Set;
import java.util.UUID;

/**
 * Medicament catalog entry. Normalized single-schema (no legacy dual
 * {@code nom}/{@code forme}/{@code quantite}/{@code presentation} columns),
 * mirroring the ERD field set exactly.
 */
@Entity
@Table(name = "medicaments")
public class Medicament {

    @Id
    @GeneratedValue
    @UuidGenerator
    private UUID id;

    @Column(name = "code_cip", nullable = false, unique = true, length = 64)
    private String codeCip;

    @Column(name = "nom_commercial_fr")
    private String nomCommercialFr;

    @Column(name = "nom_commercial_ar")
    private String nomCommercialAr;

    @Column(name = "dci")
    private String dci;

    @Column(name = "forme_pharmaceutique")
    private String formePharmaceutique;

    @Column(name = "dosage")
    private String dosage;

    @Column(name = "conditionnement")
    private String conditionnement;

    @Column(name = "ppv", precision = 12, scale = 3)
    private BigDecimal ppv;

    @Column(name = "ph", precision = 12, scale = 3)
    private BigDecimal ph;

    @Column(name = "prix_br", precision = 12, scale = 3)
    private BigDecimal prixBr;

    @Column(name = "prix_public", precision = 12, scale = 3)
    private BigDecimal prixPublic;

    @Column(name = "taux_remboursement", precision = 5, scale = 2)
    private BigDecimal tauxRemboursement;

    @Column(name = "laboratoire")
    private String laboratoire;

    @Column(name = "pays_origine")
    private String paysOrigine;

    @Column(name = "stock_min")
    private Integer stockMin;

    @Column(name = "stock_max")
    private Integer stockMax;

    @Column(name = "seuil_alerte")
    private Integer seuilAlerte;

    @Column(name = "classe_therapeutique")
    private String classeTherapeutique;

    @Column(name = "voie_administration")
    private String voieAdministration;

    @Column(name = "contre_indications", columnDefinition = "text")
    private String contreIndications;

    @Column(name = "effets_indesirables", columnDefinition = "text")
    private String effetsIndesirables;

    @Column(name = "interactions_medicamenteuses", columnDefinition = "text")
    private String interactionsMedicamenteuses;

    @Column(name = "conditions_conservation")
    private String conditionsConservation;

    @Column(name = "code_atc")
    private String codeAtc;

    @Column(name = "est_psychotrope", nullable = false)
    private boolean estPsychotrope = false;

    @Column(name = "est_ther_lourde", nullable = false)
    private boolean estTherLourde = false;

    @Column(name = "est_renouvelable", nullable = false)
    private boolean estRenouvelable = true;

    @Column(name = "delai_renouvellement")
    private Integer delaiRenouvellement;

    @Column(name = "code_barre")
    private String codeBarre;

    @Column(name = "est_generique", nullable = false)
    private boolean estGenerique = false;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "medicament_reference_id")
    private Medicament medicamentReference;

    @OneToMany(mappedBy = "medicamentReference")
    private Set<Medicament> generiques = new HashSet<>();

    @Enumerated(EnumType.STRING)
    @Column(name = "statut", nullable = false, length = 16)
    private MedicamentStatut statut = MedicamentStatut.actif;

    @CreationTimestamp
    @Column(name = "created_at", updatable = false)
    private Instant createdAt;

    @UpdateTimestamp
    @Column(name = "updated_at")
    private Instant updatedAt;

    public UUID getId() {
        return id;
    }

    public String getCodeCip() {
        return codeCip;
    }

    public void setCodeCip(String codeCip) {
        this.codeCip = codeCip;
    }

    public String getNomCommercialFr() {
        return nomCommercialFr;
    }

    public void setNomCommercialFr(String nomCommercialFr) {
        this.nomCommercialFr = nomCommercialFr;
    }

    public String getNomCommercialAr() {
        return nomCommercialAr;
    }

    public void setNomCommercialAr(String nomCommercialAr) {
        this.nomCommercialAr = nomCommercialAr;
    }

    public String getDci() {
        return dci;
    }

    public void setDci(String dci) {
        this.dci = dci;
    }

    public String getFormePharmaceutique() {
        return formePharmaceutique;
    }

    public void setFormePharmaceutique(String formePharmaceutique) {
        this.formePharmaceutique = formePharmaceutique;
    }

    public String getDosage() {
        return dosage;
    }

    public void setDosage(String dosage) {
        this.dosage = dosage;
    }

    public String getConditionnement() {
        return conditionnement;
    }

    public void setConditionnement(String conditionnement) {
        this.conditionnement = conditionnement;
    }

    public BigDecimal getPpv() {
        return ppv;
    }

    public void setPpv(BigDecimal ppv) {
        this.ppv = ppv;
    }

    public BigDecimal getPh() {
        return ph;
    }

    public void setPh(BigDecimal ph) {
        this.ph = ph;
    }

    public BigDecimal getPrixBr() {
        return prixBr;
    }

    public void setPrixBr(BigDecimal prixBr) {
        this.prixBr = prixBr;
    }

    public BigDecimal getPrixPublic() {
        return prixPublic;
    }

    public void setPrixPublic(BigDecimal prixPublic) {
        this.prixPublic = prixPublic;
    }

    public BigDecimal getTauxRemboursement() {
        return tauxRemboursement;
    }

    public void setTauxRemboursement(BigDecimal tauxRemboursement) {
        this.tauxRemboursement = tauxRemboursement;
    }

    public String getLaboratoire() {
        return laboratoire;
    }

    public void setLaboratoire(String laboratoire) {
        this.laboratoire = laboratoire;
    }

    public String getPaysOrigine() {
        return paysOrigine;
    }

    public void setPaysOrigine(String paysOrigine) {
        this.paysOrigine = paysOrigine;
    }

    public Integer getStockMin() {
        return stockMin;
    }

    public void setStockMin(Integer stockMin) {
        this.stockMin = stockMin;
    }

    public Integer getStockMax() {
        return stockMax;
    }

    public void setStockMax(Integer stockMax) {
        this.stockMax = stockMax;
    }

    public Integer getSeuilAlerte() {
        return seuilAlerte;
    }

    public void setSeuilAlerte(Integer seuilAlerte) {
        this.seuilAlerte = seuilAlerte;
    }

    public String getClasseTherapeutique() {
        return classeTherapeutique;
    }

    public void setClasseTherapeutique(String classeTherapeutique) {
        this.classeTherapeutique = classeTherapeutique;
    }

    public String getVoieAdministration() {
        return voieAdministration;
    }

    public void setVoieAdministration(String voieAdministration) {
        this.voieAdministration = voieAdministration;
    }

    public String getContreIndications() {
        return contreIndications;
    }

    public void setContreIndications(String contreIndications) {
        this.contreIndications = contreIndications;
    }

    public String getEffetsIndesirables() {
        return effetsIndesirables;
    }

    public void setEffetsIndesirables(String effetsIndesirables) {
        this.effetsIndesirables = effetsIndesirables;
    }

    public String getInteractionsMedicamenteuses() {
        return interactionsMedicamenteuses;
    }

    public void setInteractionsMedicamenteuses(String interactionsMedicamenteuses) {
        this.interactionsMedicamenteuses = interactionsMedicamenteuses;
    }

    public String getConditionsConservation() {
        return conditionsConservation;
    }

    public void setConditionsConservation(String conditionsConservation) {
        this.conditionsConservation = conditionsConservation;
    }

    public String getCodeAtc() {
        return codeAtc;
    }

    public void setCodeAtc(String codeAtc) {
        this.codeAtc = codeAtc;
    }

    public boolean isEstPsychotrope() {
        return estPsychotrope;
    }

    public void setEstPsychotrope(boolean estPsychotrope) {
        this.estPsychotrope = estPsychotrope;
    }

    public boolean isEstTherLourde() {
        return estTherLourde;
    }

    public void setEstTherLourde(boolean estTherLourde) {
        this.estTherLourde = estTherLourde;
    }

    public boolean isEstRenouvelable() {
        return estRenouvelable;
    }

    public void setEstRenouvelable(boolean estRenouvelable) {
        this.estRenouvelable = estRenouvelable;
    }

    public Integer getDelaiRenouvellement() {
        return delaiRenouvellement;
    }

    public void setDelaiRenouvellement(Integer delaiRenouvellement) {
        this.delaiRenouvellement = delaiRenouvellement;
    }

    public String getCodeBarre() {
        return codeBarre;
    }

    public void setCodeBarre(String codeBarre) {
        this.codeBarre = codeBarre;
    }

    public boolean isEstGenerique() {
        return estGenerique;
    }

    public void setEstGenerique(boolean estGenerique) {
        this.estGenerique = estGenerique;
    }

    public Medicament getMedicamentReference() {
        return medicamentReference;
    }

    public void setMedicamentReference(Medicament medicamentReference) {
        this.medicamentReference = medicamentReference;
    }

    public Set<Medicament> getGeneriques() {
        return generiques;
    }

    public MedicamentStatut getStatut() {
        return statut;
    }

    public void setStatut(MedicamentStatut statut) {
        this.statut = statut;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }

    public Instant getUpdatedAt() {
        return updatedAt;
    }
}
