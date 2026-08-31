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
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UpdateTimestamp;
import org.hibernate.annotations.UuidGenerator;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.UUID;

/**
 * Order line of a {@link CommandeFournisseur}. Tracks both the ordered quantity
 * ({@code quantite}) and the initial demand ({@code quantiteDemandee}) plus the
 * supplier-side stock before fulfilment ({@code stockAvant}).
 */
@Entity
@Table(name = "commande_fournisseur_lignes")
public class CommandeFournisseurLigne {

    @Id
    @GeneratedValue
    @UuidGenerator
    private UUID id;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "commande_id", nullable = false)
    private CommandeFournisseur commande;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "medicament_id", nullable = false)
    private Medicament medicament;

    @Column(name = "quantite", nullable = false)
    private int quantite;

    @Column(name = "quantite_demandee", nullable = false)
    private int quantiteDemandee;

    @Column(name = "stock_avant", nullable = false)
    private int stockAvant;

    @Column(name = "prix_unitaire", nullable = false, precision = 10, scale = 3)
    private BigDecimal prixUnitaire;

    @Column(name = "total_ligne", nullable = false, precision = 12, scale = 3)
    private BigDecimal totalLigne;

    @Column(name = "notes", columnDefinition = "text")
    private String notes;

    @CreationTimestamp
    @Column(name = "created_at", updatable = false)
    private Instant createdAt;

    @UpdateTimestamp
    @Column(name = "updated_at")
    private Instant updatedAt;

    public UUID getId() {
        return id;
    }

    public CommandeFournisseur getCommande() {
        return commande;
    }

    public void setCommande(CommandeFournisseur commande) {
        this.commande = commande;
    }

    public Medicament getMedicament() {
        return medicament;
    }

    public void setMedicament(Medicament medicament) {
        this.medicament = medicament;
    }

    public int getQuantite() {
        return quantite;
    }

    public void setQuantite(int quantite) {
        this.quantite = quantite;
    }

    public int getQuantiteDemandee() {
        return quantiteDemandee;
    }

    public void setQuantiteDemandee(int quantiteDemandee) {
        this.quantiteDemandee = quantiteDemandee;
    }

    public int getStockAvant() {
        return stockAvant;
    }

    public void setStockAvant(int stockAvant) {
        this.stockAvant = stockAvant;
    }

    public BigDecimal getPrixUnitaire() {
        return prixUnitaire;
    }

    public void setPrixUnitaire(BigDecimal prixUnitaire) {
        this.prixUnitaire = prixUnitaire;
    }

    public BigDecimal getTotalLigne() {
        return totalLigne;
    }

    public void setTotalLigne(BigDecimal totalLigne) {
        this.totalLigne = totalLigne;
    }

    public String getNotes() {
        return notes;
    }

    public void setNotes(String notes) {
        this.notes = notes;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }

    public Instant getUpdatedAt() {
        return updatedAt;
    }
}