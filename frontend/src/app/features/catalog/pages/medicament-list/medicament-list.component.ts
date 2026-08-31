import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { WRITE_ROLES } from '@core/constants/app.constants';
import { problemDetail } from '@core/http/http-error.util';
import { AuthService } from '@core/services/auth.service';
import { PaginationComponent } from '@shared/components/pagination/pagination.component';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  MedicamentDetailResponse,
  MedicamentStatut,
  medicamentStatutTone,
} from '@features/catalog/models/catalog.models';
import { CatalogService } from '@features/catalog/services/catalog.service';

@Component({
  selector: 'app-medicament-list',
  standalone: true,
  imports: [RouterLink, FormsModule, StatusPillComponent, PaginationComponent],
  templateUrl: './medicament-list.component.html',
  styleUrl: './medicament-list.component.css',
})
export class MedicamentListComponent implements OnInit {
  items: MedicamentDetailResponse[] = [];
  page = 0;
  /** Toute la liste (pas de pagination tant que le catalogue reste < 500). */
  size = 500;
  totalElements = 0;
  totalPages = 0;
  error = '';

  search = '';
  statut: '' | MedicamentStatut = '';
  enRupture = false;
  prochePeremption = false;
  canWrite = false;

  private searchTimer: ReturnType<typeof setTimeout> | null = null;

  constructor(
    private catalog: CatalogService,
    private auth: AuthService,
  ) {}

  ngOnInit(): void {
    const role = this.auth.currentUser()?.role;
    this.canWrite = role !== undefined && (WRITE_ROLES as readonly string[]).includes(role);
    this.reload();
  }

  readonly medicamentTone = medicamentStatutTone;

  get counts(): { actifs: number; rupture: number; peremption: number } {
    let actifs = 0;
    let rupture = 0;
    let peremption = 0;
    for (const m of this.items) {
      if (m.statut === 'actif') actifs++;
      if (m.estEnRupture) rupture++;
      if (m.estProchePeremption) peremption++;
    }
    return { actifs, rupture, peremption };
  }

  stockPct(m: MedicamentDetailResponse): number {
    const max = m.stockMax > 0 ? m.stockMax : 100;
    return Math.min(100, Math.round(((m.stockActif ?? 0) / max) * 100));
  }

  onSearchChange(_value: string): void {
    if (this.searchTimer) clearTimeout(this.searchTimer);
    this.searchTimer = setTimeout(() => {
      this.page = 0;
      this.reload();
    }, 350);
  }

  reload(): void {
    this.error = '';
    this.catalog
      .listMedicaments({
        search: this.search || undefined,
        statut: this.statut || undefined,
        enRupture: this.enRupture ? true : undefined,
        prochePeremption: this.prochePeremption ? true : undefined,
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
        error: (err: unknown) => {
          this.items = [];
          this.error = problemDetail(err, 'Erreur lors du chargement du catalogue.');
        },
      });
  }

  go(p: number): void {
    this.page = p;
    this.reload();
  }
}