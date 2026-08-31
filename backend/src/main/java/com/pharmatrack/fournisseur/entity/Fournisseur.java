package com.pharmatrack.fournisseur.entity;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UpdateTimestamp;
import org.hibernate.annotations.UuidGenerator;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.UUID;

/**
 * Supplier record. Mirrors the legacy {@code fournisseurs} table, including
 * the detail columns (pays_origine, ville, ..., contact_poste) and the relance
 * tracker used by the relance feature.
 */
@Entity
@Table(name = "fournisseurs")
public class Fournisseur {

    @Id
    @GeneratedValue
    @UuidGenerator
    private UUID id;

    @Column(name = "user_id")
    private UUID userId;

    @Column(name = "raison_sociale", nullable = false)
    private String raisonSociale;

    @Column(name = "matricule_fiscal")
    private String matriculeFiscal;

    @Column(name = "pays_origine")
    private String paysOrigine;

    @Column(name = "specialite")
    private String specialite;

    @Column(name = "fax")
    private String fax;

    @Column(name = "code_postal")
    private String codePostal;

    @Column(name = "ville")
    private String ville;

    @Column(name = "gouvernorat")
    private String gouvernorat;

    @Column(name = "contact_poste")
    private String contactPoste;

    @Column(name = "adresse")
    private String adresse;

    @Column(name = "telephone")
    private String telephone;

    @Column(name = "email_pro")
    private String emailPro;

    @Column(name = "contact_nom")
    private String contactNom;

    @Column(name = "contact_telephone")
    private String contactTelephone;

    @Column(name = "site_web")
    private String siteWeb;

    @Column(name = "delai_livraison_moyen", nullable = false)
    private int delaiLivraisonMoyen = 7;

    @Column(name = "frais_livraison", nullable = false, precision = 10, scale = 3)
    private BigDecimal fraisLivraison = BigDecimal.ZERO;

    @Column(name = "note", precision = 3, scale = 2)
    private BigDecimal note;

    @Column(name = "est_actif", nullable = false)
    private boolean estActif = true;

    @Column(name = "relance_active", nullable = false)
    private boolean relanceActive = true;

    @Column(name = "derniere_relance")
    private Instant derniereRelance;

    @Column(name = "nb_relances", nullable = false)
    private int nbRelances;

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

    public UUID getUserId() {
        return userId;
    }

    public void setUserId(UUID userId) {
        this.userId = userId;
    }

    public String getRaisonSociale() {
        return raisonSociale;
    }

    public void setRaisonSociale(String raisonSociale) {
        this.raisonSociale = raisonSociale;
    }

    public String getMatriculeFiscal() {
        return matriculeFiscal;
    }

    public void setMatriculeFiscal(String matriculeFiscal) {
        this.matriculeFiscal = matriculeFiscal;
    }

    public String getPaysOrigine() {
        return paysOrigine;
    }

    public void setPaysOrigine(String paysOrigine) {
        this.paysOrigine = paysOrigine;
    }

    public String getSpecialite() {
        return specialite;
    }

    public void setSpecialite(String specialite) {
        this.specialite = specialite;
    }

    public String getFax() {
        return fax;
    }

    public void setFax(String fax) {
        this.fax = fax;
    }

    public String getCodePostal() {
        return codePostal;
    }

    public void setCodePostal(String codePostal) {
        this.codePostal = codePostal;
    }

    public String getVille() {
        return ville;
    }

    public void setVille(String ville) {
        this.ville = ville;
    }

    public String getGouvernorat() {
        return gouvernorat;
    }

    public void setGouvernorat(String gouvernorat) {
        this.gouvernorat = gouvernorat;
    }

    public String getContactPoste() {
        return contactPoste;
    }

    public void setContactPoste(String contactPoste) {
        this.contactPoste = contactPoste;
    }

    public String getAdresse() {
        return adresse;
    }

    public void setAdresse(String adresse) {
        this.adresse = adresse;
    }

    public String getTelephone() {
        return telephone;
    }

    public void setTelephone(String telephone) {
        this.telephone = telephone;
    }

    public String getEmailPro() {
        return emailPro;
    }

    public void setEmailPro(String emailPro) {
        this.emailPro = emailPro;
    }

    public String getContactNom() {
        return contactNom;
    }

    public void setContactNom(String contactNom) {
        this.contactNom = contactNom;
    }

    public String getContactTelephone() {
        return contactTelephone;
    }

    public void setContactTelephone(String contactTelephone) {
        this.contactTelephone = contactTelephone;
    }

    public String getSiteWeb() {
        return siteWeb;
    }

    public void setSiteWeb(String siteWeb) {
        this.siteWeb = siteWeb;
    }

    public int getDelaiLivraisonMoyen() {
        return delaiLivraisonMoyen;
    }

    public void setDelaiLivraisonMoyen(int delaiLivraisonMoyen) {
        this.delaiLivraisonMoyen = delaiLivraisonMoyen;
    }

    public BigDecimal getFraisLivraison() {
        return fraisLivraison;
    }

    public void setFraisLivraison(BigDecimal fraisLivraison) {
        this.fraisLivraison = fraisLivraison;
    }

    public BigDecimal getNote() {
        return note;
    }

    public void setNote(BigDecimal note) {
        this.note = note;
    }

    public boolean isEstActif() {
        return estActif;
    }

    public void setEstActif(boolean estActif) {
        this.estActif = estActif;
    }

    public boolean isRelanceActive() {
        return relanceActive;
    }

    public void setRelanceActive(boolean relanceActive) {
        this.relanceActive = relanceActive;
    }

    public Instant getDerniereRelance() {
        return derniereRelance;
    }

    public void setDerniereRelance(Instant derniereRelance) {
        this.derniereRelance = derniereRelance;
    }

    public int getNbRelances() {
        return nbRelances;
    }

    public void setNbRelances(int nbRelances) {
        this.nbRelances = nbRelances;
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