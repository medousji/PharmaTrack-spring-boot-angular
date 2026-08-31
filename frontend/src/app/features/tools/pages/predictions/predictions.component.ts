import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import { MedicamentDetailResponse } from '@features/catalog/models/catalog.models';
import { CatalogService } from '@features/catalog/services/catalog.service';

interface Suggestion {
  medicament: MedicamentDetailResponse;
  stock: number;
  besoin: number;
  motif: string;
}

@Component({
  selector: 'app-predictions',
  standalone: true,
  imports: [CommonModule, RouterLink, StatusPillComponent],
  templateUrl: './predictions.component.html',
  styleUrl: './predictions.component.css',
})
export class PredictionsComponent {
  loading = true;
  error = '';
  suggestions: Suggestion[] = [];
  objectifs: Array<{ medicament: MedicamentDetailResponse; objectif: string }> = [];

  private readonly catalog = inject(CatalogService);

  constructor() {
    this.catalog.listMedicaments({ page: 0, size: 1000 }).subscribe({
      next: (res) => {
        this.compute(res.content);
        this.loading = false;
      },
      error: () => {
        this.error = 'Erreur de chargement du catalogue.';
        this.loading = false;
      },
    });
  }

  private compute(list: MedicamentDetailResponse[]): void {
    const sugg: Suggestion[] = [];
    const obj: Array<{ medicament: MedicamentDetailResponse; objectif: string }> = [];

    for (const m of list) {
      const stock = m.stockActif ?? 0;
      if (m.estEnRupture) {
        sugg.push({ medicament: m, stock, besoin: Math.max(m.stockMax, m.stockMin + 1), motif: 'Rupture de stock' });
      } else if (stock < m.stockMin) {
        sugg.push({ medicament: m, stock, besoin: m.stockMax - stock, motif: 'Sous le seuil minimum' });
      } else if (stock <= m.seuilAlerte) {
        sugg.push({ medicament: m, stock, besoin: m.stockMax - stock, motif: 'Proche du seuil d’alerte' });
      }
      if (m.estProchePeremption) {
        obj.push({ medicament: m, objectif: 'Prioriser la sortie — péremption proche' });
      }
      if (m.estTherLourde) {
        obj.push({ medicament: m, objectif: 'Vérifier la traçabilité (thérapie lourde)' });
      }
    }

    sugg.sort((a, b) => b.besoin - a.besoin);
    this.suggestions = sugg.slice(0, 8);
    this.objectifs = obj.slice(0, 6);
  }
}