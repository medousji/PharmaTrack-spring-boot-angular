import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';

import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  LotResponse,
  lotStatutTone,
} from '@features/catalog/models/catalog.models';
import { CatalogService } from '@features/catalog/services/catalog.service';

@Component({
  selector: 'app-lots',
  standalone: true,
  imports: [CommonModule, RouterLink, StatusPillComponent],
  templateUrl: './lots.component.html',
  styleUrl: './lots.component.css',
})
export class LotsComponent implements OnInit {
  lots: LotResponse[] = [];
  totalElements = 0;
  loading = true;
  error = '';

  private readonly nameById = new Map<string, string>();

  readonly lotTone = lotStatutTone;

  private readonly catalog = inject(CatalogService);
  private readonly route = inject(ActivatedRoute);

  ngOnInit(): void {
    this.route.queryParams.subscribe((q) => this.load(q['statut'] ?? undefined));
  }

  medName(id: string): string {
    return this.nameById.get(id) ?? '—';
  }

  private load(statut?: string): void {
    this.loading = true;
    this.error = '';
    this.catalog.listMedicaments({ page: 0, size: 500 }).subscribe({
      next: (all) => {
        this.nameById.clear();
        for (const m of all.content) this.nameById.set(m.id, m.nomCommercialFr);
      },
      error: () => {},
    });
    this.catalog
      .listLots({ statut: statut as never, page: 0, size: 500 })
      .subscribe({
        next: (res) => {
          this.lots = res.content;
          this.totalElements = res.totalElements;
          this.loading = false;
        },
        error: () => {
          this.error = 'Erreur de chargement des lots.';
          this.loading = false;
        },
      });
  }
}