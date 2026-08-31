package com.pharmatrack.fournisseur.entity;

import com.pharmatrack.catalog.entity.Medicament;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;
import jakarta.persistence.UniqueConstraint;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UpdateTimestamp;
import org.hibernate.annotations.UuidGenerator;

import java.math.BigDecimal;
import java.time.Instant;
import java.time.LocalDate;
import java.util.UUID;

/**
 * Catalogue line: a medicament offered by a supplier, with its purchase price
 * and the supplier-side stock ledger used to fulfil purchase orders.
 */
@Entity
@Table(name = "fournisseur_medicaments", uniqueConstraints = {
        @UniqueConstraint(name = "uq_fm_fournisseur_medicament",
                columnNames = {"fournisseur_id", "medicament_id"})
})
public class FournisseurMedicament {

    @Id
    @GeneratedValue
    @UuidGenerator
    private UUID id;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "fournisseur_id", nullable = false)
    private Fournisseur fournisseur;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "medicament_id", nullable = false)
    private Medicament medicament;

    @Column(name = "reference_fournisseur")
    private String referenceFournisseur;

    @Column(name = "prix_achat", nullable = false, precision = 10, scale = 3)
    private BigDecimal prixAchat;

    @Column(name = "prix_public", precision = 10, scale = 3)
    private BigDecimal prixPublic;

    @Column(name = "stock_disponible", nullable = false)
    private int stockDisponible;

    @Column(name = "stock_minimum", nullable = false)
    private int stockMinimum = 10;

    @Column(name = "stock_maximum")
    private Integer stockMaximum;

    @Column(name = "seuil_reapprovisionnement", nullable = false)
    private int seuilReapprovisionnement = 20;

    @Column(name = "delai_livraison")
    private Integer delaiLivraison;

    @Column(name = "disponible", nullable = false)
    private boolean disponible = true;

    @Column(name = "derniere_mise_a_jour")
    private LocalDate derniereMiseAJour;

    @CreationTimestamp
    @Column(name = "created_at", updatable = false)
    private Instant createdAt;

    @UpdateTimestamp
    @Column(name = "updated_at")
    private Instant updatedAt;

    public UUID getId() {
        return id;
    }

    public Fournisseur getFournisseur() {
        return fournisseur;
    }

    public void setFournisseur(Fournisseur fournisseur) {
        this.fournisseur = fournisseur;
    }

    public Medicament getMedicament() {
        return medicament;
    }

    public void setMedicament(Medicament medicament) {
        this.medicament = medicament;
    }

    public String getReferenceFournisseur() {
        return referenceFournisseur;
    }

    public void setReferenceFournisseur(String referenceFournisseur) {
        this.referenceFournisseur = referenceFournisseur;
    }

    public BigDecimal getPrixAchat() {
        return prixAchat;
    }

    public void setPrixAchat(BigDecimal prixAchat) {
        this.prixAchat = prixAchat;
    }

    public BigDecimal getPrixPublic() {
        return prixPublic;
    }

    public void setPrixPublic(BigDecimal prixPublic) {
        this.prixPublic = prixPublic;
    }

    public int getStockDisponible() {
        return stockDisponible;
    }

    public void setStockDisponible(int stockDisponible) {
        this.stockDisponible = stockDisponible;
    }

    public int getStockMinimum() {
        return stockMinimum;
    }

    public void setStockMinimum(int stockMinimum) {
        this.stockMinimum = stockMinimum;
    }

    public Integer getStockMaximum() {
        return stockMaximum;
    }

    public void setStockMaximum(Integer stockMaximum) {
        this.stockMaximum = stockMaximum;
    }

    public int getSeuilReapprovisionnement() {
        return seuilReapprovisionnement;
    }

    public void setSeuilReapprovisionnement(int seuilReapprovisionnement) {
        this.seuilReapprovisionnement = seuilReapprovisionnement;
    }

    public Integer getDelaiLivraison() {
        return delaiLivraison;
    }

    public void setDelaiLivraison(Integer delaiLivraison) {
        this.delaiLivraison = delaiLivraison;
    }

    public boolean isDisponible() {
        return disponible;
    }

    public void setDisponible(boolean disponible) {
        this.disponible = disponible;
    }

    public LocalDate getDerniereMiseAJour() {
        return derniereMiseAJour;
    }

    public void setDerniereMiseAJour(LocalDate derniereMiseAJour) {
        this.derniereMiseAJour = derniereMiseAJour;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }

    public Instant getUpdatedAt() {
        return updatedAt;
    }

    /** Stock that can actually be sold, keeping the minimum reserved. */
    public int stockDisponiblePourVente() {
        return stockDisponible - stockMinimum;
    }
}