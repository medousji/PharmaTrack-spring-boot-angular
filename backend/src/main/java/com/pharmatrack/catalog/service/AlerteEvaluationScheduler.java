package com.pharmatrack.catalog.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.scheduling.annotation.Scheduled;
import org.springframework.stereotype.Component;

/**
 * Hourly (configurable) run of the alert evaluation engine. The evaluation is
 * idempotent, so overlapping schedules only ever create missing alerts.
 */
@Component
public class AlerteEvaluationScheduler {

    private static final Logger log = LoggerFactory.getLogger(AlerteEvaluationScheduler.class);

    private final AlerteEvaluationService evaluationService;

    public AlerteEvaluationScheduler(AlerteEvaluationService evaluationService) {
        this.evaluationService = evaluationService;
    }

    @Scheduled(cron = "${pharmatrack.alerts.cron:0 0 * * * *}")
    public void evaluerAlertes() {
        var summary = evaluationService.reEvaluate();
        log.info("Alertes évaluées : {} rupture(s), {} expiration(s), {} stock(s) faible(s) (total {}).",
                summary.rupturesCreees(), summary.expirationsCreees(),
                summary.stocksFaiblesCrees(), summary.total());
    }
}