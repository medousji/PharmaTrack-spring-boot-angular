package com.pharmatrack.fournisseur.dto;

import java.util.List;

/**
 * Bulk catalogue update (legacy {@code POST /fournisseur/prix}).
 */
public record UpdatePrixRequest(List<UpdatePrixItem> prix) {
}