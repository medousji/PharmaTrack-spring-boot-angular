import { Component, inject, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';

import { problemDetail } from '@core/http/http-error.util';
import type { AuthUser } from '@core/models/auth.models';
import { AuthService } from '@core/services/auth.service';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  commandeStatutLabel,
  commandeStatutTone,
  type FournisseurDashboard,
} from '@features/fournisseur/models/fournisseur.models';
import { FournisseurService } from '@features/fournisseur/services/fournisseur.service';

@Component({
  selector: 'app-fournisseur-dashboard',
  standalone: true,
  imports: [RouterLink, StatusPillComponent],
  templateUrl: './fournisseur-dashboard.component.html',
  styleUrl: './fournisseur-dashboard.component.css',
})
export class FournisseurDashboardComponent implements OnInit {
  data: FournisseurDashboard | null = null;
  error = '';
  loading = true;

  user: AuthUser | null = null;

  private readonly service = inject(FournisseurService);
  private readonly auth = inject(AuthService);

  ngOnInit(): void {
    this.user = this.auth.currentUser();
    this.load();
  }

  statutLabel = commandeStatutLabel;
  statutTone = commandeStatutTone;

  load(): void {
    this.loading = true;
    this.error = '';
    this.service.dashboard().subscribe({
      next: (res) => {
        this.data = res;
        this.loading = false;
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, 'Erreur lors du chargement du tableau de bord.');
        this.loading = false;
      },
    });
  }

  date(iso: string): string {
    const d = new Date(`${iso}T00:00:00`);
    if (!Number.isNaN(d.getTime())) {
      return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
    }
    return iso;
  }
}