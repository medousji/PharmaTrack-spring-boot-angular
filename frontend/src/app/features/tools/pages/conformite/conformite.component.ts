import { Component, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';

import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import { MedicamentDetailResponse } from '@features/catalog/models/catalog.models';
import { CatalogService } from '@features/catalog/services/catalog.service';

interface CheckResult {
  medicament: MedicamentDetailResponse;
  conformance: number;
  total: number;
  missing: string[];
}

@Component({
  selector: 'app-conformite',
  standalone: true,
  imports: [FormsModule, RouterLink, StatusPillComponent],
  templateUrl: './conformite.component.html',
  styleUrl: './conformite.component.css',
})
export class ConformiteComponent {
  results: CheckResult[] = [];
  filter = 'tous';
  loading = true;
  error = '';

  private readonly catalog = inject(CatalogService);

  constructor() {
    this.catalog.listMedicaments({ page: 0, size: 1000 }).subscribe({
      next: (res) => {
        this.results = res.content.map((m) => ({
          medicament: m,
          ...this.evaluate(m),
        }));
        this.loading = false;
      },
      error: () => {
        this.error = 'Erreur de chargement du catalogue.';
        this.loading = false;
      },
    });
  }

  get total(): number {
    return this.results.length;
  }

  get conformes(): number {
    return this.results.filter((r) => r.missing.length === 0).length;
  }

  get score(): number {
    return this.total ? Math.round((this.conformes / this.total) * 100) : 0;
  }

  get shown(): CheckResult[] {
    if (this.filter === 'conforme') return this.results.filter((r) => r.missing.length === 0);
    if (this.filter === 'non-conforme') return this.results.filter((r) => r.missing.length > 0);
    return this.results;
  }

  private evaluate(m: MedicamentDetailResponse): { conformance: number; total: number; missing: string[] } {
    const checks: Array<[boolean, string]> = [
      [!!m.codeCip && m.codeCip.length >= 7, 'Code CIP'],
      [!!m.dci?.trim(), 'DCI'],
      [!!m.formePharmaceutique?.trim(), 'Forme pharmaceutique'],
      [!!m.dosage?.trim(), 'Dosage'],
      [(m.prixPublic ?? 0) > 0, 'Prix public'],
      [(m.ppv ?? 0) > 0 || (m.ph ?? 0) > 0 || (m.prixBr ?? 0) > 0, 'Prix de référence'],
      [m.tauxRemboursement != null && m.tauxRemboursement >= 0 && m.tauxRemboursement <= 100, 'Taux de remboursement'],
      [m.stockMin <= m.stockMax, 'Stock min / max'],
      [!!m.conditionsConservation?.trim(), 'Conditions de conservation'],
      [!!m.codeAtc?.trim(), 'Code ATC'],
    ];
    const missing = checks.filter(([ok]) => !ok).map(([, label]) => label);
    return { conformance: checks.length - missing.length, total: checks.length, missing };
  }
}