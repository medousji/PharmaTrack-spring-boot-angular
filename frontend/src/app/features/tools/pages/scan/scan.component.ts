import { Component, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';

import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import { MedicamentDetailResponse, medicamentStatutTone } from '@features/catalog/models/catalog.models';
import { CatalogService } from '@features/catalog/services/catalog.service';

@Component({
  selector: 'app-scan',
  standalone: true,
  imports: [FormsModule, RouterLink, StatusPillComponent],
  templateUrl: './scan.component.html',
  styleUrl: './scan.component.css',
})
export class ScanComponent {
  code = '';
  busy = false;
  notFound = false;
  medicament: MedicamentDetailResponse | null = null;

  readonly medicamentTone = medicamentStatutTone;

  private readonly all = new Map<string, string>();

  private readonly catalog = inject(CatalogService);

  constructor() {
    this.catalog.listMedicaments({ page: 0, size: 1000 }).subscribe({
      next: (res) => {
        this.all.clear();
        for (const m of res.content) {
          this.all.set(m.id, m.codeCip);
          this.all.set((m.codeBarre ?? '').trim(), m.id);
        }
        this.all.delete('');
      },
      error: () => {},
    });
  }

  search(): void {
    const q = this.code.trim();
    if (!q) return;
    this.busy = true;
    this.notFound = false;
    this.medicament = null;

    const hit = this.all.get(q) ?? this.all.get(q.toLowerCase());
    if (!hit) {
      this.lookupByCode(q);
      return;
    }
    this.catalog.getMedicament(hit).subscribe({
      next: (m) => {
        this.medicament = m;
        this.busy = false;
      },
      error: () => this.checkNone(),
    });
  }

  private lookupByCode(q: string): void {
    this.catalog.listMedicaments({ search: q, page: 0, size: 1 }).subscribe({
      next: (res) => {
        const m = res.content[0];
        this.medicament = m ?? null;
        this.busy = false;
        if (!m) this.notFound = true;
      },
      error: () => this.checkNone(),
    });
  }

  private checkNone(): void {
    this.busy = false;
    this.notFound = true;
  }
}