package com.pharmatrack.catalog.dto;

/**
 * A simple label/total pair for dashboard distributions
 * (categories, laboratoires, formes...).
 */
public record LabelCount(String label, long total) {
}