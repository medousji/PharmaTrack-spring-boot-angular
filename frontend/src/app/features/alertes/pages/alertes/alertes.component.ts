import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { ROLES } from '@core/constants/app.constants';
import { problemDetail } from '@core/http/http-error.util';
import { AuthService } from '@core/services/auth.service';
import { PaginationComponent } from '@shared/components/pagination/pagination.component';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  AlerteNiveau,
  AlerteResponse,
  AlerteType,
  alerteNiveauLabel,
  alerteNiveauTone,
  alerteTypeLabel,
  alerteTypeTone,
} from '@features/alertes/models/alerte.models';
import { AlerteService } from '@features/alertes/services/alerte.service';

@Component({
  selector: 'app-alertes',
  standalone: true,
  imports: [CommonModule, FormsModule, StatusPillComponent, PaginationComponent],
  templateUrl: './alertes.component.html',
  styleUrl: './alertes.component.css',
})
export class AlertesComponent implements OnInit {
  items: AlerteResponse[] = [];
  page = 0;
  size = 20;
  totalElements = 0;
  totalPages = 0;
  error = '';
  message = '';
  isAdmin = false;

  type: '' | AlerteType = '';
  niveau: '' | AlerteNiveau = '';
  estLue = '';

  counts = { critique: 0, eleve: 0, moyen: 0, faible: 0 };

  constructor(
    private alerte: AlerteService,
    private auth: AuthService,
  ) {}

  ngOnInit(): void {
    this.isAdmin = this.auth.currentUser()?.role === ROLES.ADMIN;
    this.reload();
    this.loadCounts();
  }

  readonly typeLabel = alerteTypeLabel;
  readonly niveauLabel = alerteNiveauLabel;
  readonly typeTone = alerteTypeTone;
  readonly niveauTone = alerteNiveauTone;

  loadCounts(): void {
    this.alerte.countUnreadByLevel().subscribe({
      next: (levels) => {
        for (const entry of levels) {
          this.counts[entry.niveau] = entry.total;
        }
      },
      error: () => undefined,
    });
  }

  reload(): void {
    this.error = '';
    this.alerte
      .list({
        type: this.type || undefined,
        niveau: this.niveau || undefined,
        estLue: this.estLue === '' ? undefined : this.estLue === 'true',
        page: this.page,
        size: this.size,
      })
      .subscribe({
        next: (res) => {
          this.items = res.content;
          this.page = res.page;
          this.totalElements = res.totalElements;
          this.totalPages = res.totalPages;
        },
        error: (err: unknown) =>
          (this.error = problemDetail(err, 'Erreur de chargement des alertes.')),
      });
  }

  reEvaluate(): void {
    this.alerte.reEvaluate().subscribe({
      next: (s) => {
        this.message = `Évaluation (re)lancée : ${s.total} alerte(s) créée(s) — ${s.rupturesCreees} rupture(s), ${s.expirationsCreees} expiration(s), ${s.stocksFaiblesCrees} stock(s) faible(s).`;
        this.reload();
        this.loadCounts();
      },
      error: (err: unknown) =>
        (this.error = problemDetail(err, 'Erreur lors de la ré-évaluation.')),
    });
  }

  markRead(a: AlerteResponse): void {
    this.alerte.markRead(a.id).subscribe({
      next: () => this.reload(),
      error: (err: unknown) => (this.error = problemDetail(err, 'Erreur.')),
    });
  }

  resolve(a: AlerteResponse): void {
    this.alerte.resolve(a.id).subscribe({
      next: () => {
        this.reload();
        this.loadCounts();
      },
      error: (err: unknown) => (this.error = problemDetail(err, 'Erreur.')),
    });
  }

  go(p: number): void {
    this.page = p;
    this.reload();
  }
}