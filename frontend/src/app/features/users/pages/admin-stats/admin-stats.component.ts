import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';

import { problemDetail } from '@core/http/http-error.util';
import { BarChartComponent, type ChartItem } from '@shared/components/bar-chart/bar-chart.component';
import { DonutChartComponent } from '@shared/components/donut-chart/donut-chart.component';
import type { AdminUserStats } from '@features/users/models/admin-user.model';
import { AdminUsersService } from '@features/users/services/admin-users.service';

const ROLE_PALETTE = ['#d4af37', '#9caf88', '#e6a57e', '#b8aa9a'];
const STATUT_PALETTE = ['#9caf88', '#e6a57e', '#9c8a78'];

const ROLE_LABELS: Readonly<Record<string, string>> = {
  admin: 'Administrateur',
  pharmacien: 'Pharmacien',
  fournisseur: 'Fournisseur',
  visiteur: 'Visiteur',
};

const STATUT_LABELS: Readonly<Record<string, string>> = {
  active: 'Actif',
  inactive: 'Inactif',
  suspended: 'Suspendu',
};

@Component({
  selector: 'app-admin-stats',
  standalone: true,
  imports: [RouterLink, DonutChartComponent, BarChartComponent],
  templateUrl: './admin-stats.component.html',
  styleUrl: './admin-stats.component.css',
})
export class AdminStatsComponent implements OnInit {
  readonly stats = signal<AdminUserStats | null>(null);
  readonly error = signal('');
  readonly loading = signal(true);

  private readonly service = inject(AdminUsersService);

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.error.set('');
    this.service.stats().subscribe({
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

  roleItems(stats: AdminUserStats): ChartItem[] {
    return [
      { label: ROLE_LABELS['admin'], total: stats.admins },
      { label: ROLE_LABELS['pharmacien'], total: stats.pharmaciens },
      { label: ROLE_LABELS['fournisseur'], total: stats.fournisseurs },
      { label: ROLE_LABELS['visiteur'], total: stats.visiteurs },
    ];
  }

  statutItems(stats: AdminUserStats): ChartItem[] {
    return Object.entries(stats.parStatut).map(([key, total]) => ({
      label: STATUT_LABELS[key] ?? key,
      total,
    }));
  }

  readonly rolePalette = ROLE_PALETTE;
  readonly statutPalette = STATUT_PALETTE;
}