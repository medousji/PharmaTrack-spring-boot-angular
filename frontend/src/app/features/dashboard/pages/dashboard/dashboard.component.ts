import { Component, computed, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { toSignal } from '@angular/core/rxjs-interop';
import { map } from 'rxjs/operators';

import { ROLES, WRITE_ROLES } from '@core/constants/app.constants';
import { problemDetail } from '@core/http/http-error.util';
import { AuthService } from '@core/services/auth.service';
import type { AlerteNiveau, AlerteResponse } from '@features/alertes/models/alerte.models';
import { ALERTE_TYPE_LABELS, type AlerteType } from '@features/alertes/models/alerte.models';
import { AlerteService } from '@features/alertes/services/alerte.service';
import type {
  DashboardStats,
  DateCount,
  TopMedicament,
} from '@features/dashboard/models/dashboard.models';
import { DashboardService } from '@features/dashboard/services/dashboard.service';
import type { ChartItem } from '@shared/components/bar-chart/bar-chart.component';
import { DonutChartComponent } from '@shared/components/donut-chart/donut-chart.component';

const NIVEAU_SHORT_LABELS: Readonly<Record<AlerteNiveau, string>> = {
  critique: 'critique',
  eleve: 'élevé',
  moyen: 'moyen',
  faible: 'faible',
};

const LOT_STATUT_LABELS: Readonly<Record<string, string>> = {
  actif: 'Actif',
  epuise: 'Épuisé',
  perime: 'Périmé',
  bloque: 'Bloqué',
};

const MEDICAMENT_STATUT_LABELS: Readonly<Record<string, string>> = {
  actif: 'Actif',
  inactif: 'Inactif',
  retire: 'Retiré',
};

/** Palette du tableau de bord Laravel (doughnut, barres). */
export const DASHBOARD_PALETTE = ['#d4af37', '#9caf88', '#e6a57e', '#8b7355', '#c4b5a0'];

const DATE_FULL = new Intl.DateTimeFormat('fr-FR', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric',
});

const LEGACY_LEVEL_COLORS: Readonly<Record<AlerteNiveau, string>> = {
  critique: '#e6a57e',
  eleve: '#c4b5a0',
  moyen: '#8b7355',
  faible: '#9caf88',
};

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [RouterLink, DonutChartComponent],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css',
})
export class DashboardComponent {
  private readonly auth = inject(AuthService);
  private readonly alerte = inject(AlerteService);
  private readonly dashboard = inject(DashboardService);

  readonly user = toSignal(this.auth.user$, { initialValue: null });
  readonly userName = computed(() => this.user()?.name ?? 'invité');
  readonly roleLabel = computed(() => this.user()?.role ?? '—');
  readonly isAdmin = computed(() => this.user()?.role === ROLES.ADMIN);
  readonly canWrite = computed(() =>
    this.user() !== null && WRITE_ROLES.includes(this.user()!.role),
  );
  readonly canAssistant = computed(() => {
    const role = this.user()?.role;
    return role === ROLES.ADMIN || role === ROLES.PHARMACIEN;
  });
  readonly todayLabel = computed(() => {
    const d = new Date();
    return DATE_FULL.format(d).replace(/^\w/, (c) => c.toUpperCase());
  });

  /** Palette du tableau de bord Laravel (or, vert, pêche, brun). */
  readonly palette = DASHBOARD_PALETTE;

  readonly alertCounts = toSignal(this.alerte.countUnreadByLevel(), { initialValue: [] });
  readonly recentAlertes = toSignal(
    this.alerte.list({ estLue: false, page: 0, size: 5 }).pipe(map((r) => r.content)),
    { initialValue: [] },
  );
  readonly stats = signal<DashboardStats | null>(null);
  readonly loading = signal(true);
  readonly error = signal('');

  constructor() {
    this.load();
  }

  readonly statutLots = computed(() => this.entries(this.stats()?.statutLots ?? {}, LOT_STATUT_LABELS));
  readonly medStatuts = computed(() =>
    this.entries(this.stats()?.statutMedicaments ?? {}, MEDICAMENT_STATUT_LABELS),
  );
  readonly alertesParType = computed(() =>
    this.entries(this.stats()?.alertesParType ?? {}, ALERTE_TYPE_LABELS),
  );

  load(): void {
    this.loading.set(true);
    this.error.set('');
    this.dashboard.getStats().subscribe({
      next: (s) => {
        this.stats.set(s);
        this.loading.set(false);
      },
      error: (err: unknown) => {
        this.error.set(problemDetail(err, 'Erreur de chargement des statistiques.'));
        this.loading.set(false);
      },
    });
  }

  niveauLabel(niveau: AlerteNiveau): string {
    return NIVEAU_SHORT_LABELS[niveau] ?? niveau;
  }

  niveauColor(niveau: AlerteNiveau): string {
    return LEGACY_LEVEL_COLORS[niveau] ?? '#8b7355';
  }

  alerteTypeLabel(type: AlerteType): string {
    return ALERTE_TYPE_LABELS[type] ?? type;
  }

  rankingPct(list: TopMedicament[], stock: number): number {
    const max = Math.max(1, ...list.map((m) => m.stock));
    return Math.round((stock / max) * 100);
  }

  timeAgo(createdAt: string): string {
    const age = Date.now() - new Date(createdAt).getTime();
    const minutes = Math.floor(age / 60_000);
    if (minutes < 1) {
      return "à l'instant";
    }
    if (minutes < 60) {
      return `il y a ${minutes} min`;
    }
    const hours = Math.floor(minutes / 60);
    if (hours < 24) {
      return `il y a ${hours} h`;
    }
    const days = Math.floor(hours / 24);
    return `il y a ${days} j`;
  }

  private entries(
    record: Record<string, number>,
    labels: Readonly<Record<string, string>>,
  ): ChartItem[] {
    return Object.entries(record).map(([key, total]) => ({
      label: labels[key] ?? key,
      total,
    }));
  }

  linePoints(series: DateCount[]): string {
    if (series.length === 0) {
      return '';
    }
    const max = Math.max(1, ...series.map((d) => d.total));
    return series
      .map((d, i) => {
        const x = series.length === 1 ? 50 : (i / (series.length - 1)) * 100;
        const y = 28 - (d.total / max) * 26;
        return `${x.toFixed(2)},${y.toFixed(2)}`;
      })
      .join(' ');
  }

  areaPoints(series: DateCount[]): string {
    if (series.length === 0) {
      return '';
    }
    const points = this.linePoints(series);
    return `${points} 100,32 0,32`;
  }

  /** Recent date formatted as dd/mm. */
  dateLabel(date: string): string {
    const d = new Date(`${date}T00:00:00`);
    if (!Number.isNaN(d.getTime())) {
      return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}`;
    }
    return date;
  }

  maxExpiration(series: DateCount[]): number {
    return Math.max(1, ...series.map((d) => d.total));
  }
}