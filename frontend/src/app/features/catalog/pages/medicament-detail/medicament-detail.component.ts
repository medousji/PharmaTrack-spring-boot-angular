import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';

import { WRITE_ROLES, ROLES } from '@core/constants/app.constants';
import { problemDetail } from '@core/http/http-error.util';
import { AuthService } from '@core/services/auth.service';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  LotResponse,
  MedicamentDetailResponse,
  lotStatutTone,
  medicamentStatutTone,
} from '@features/catalog/models/catalog.models';
import { CatalogService } from '@features/catalog/services/catalog.service';

@Component({
  selector: 'app-medicament-detail',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink, StatusPillComponent],
  templateUrl: './medicament-detail.component.html',
  styleUrl: './medicament-detail.component.css',
})
export class MedicamentDetailComponent implements OnInit {
  m: MedicamentDetailResponse | null = null;
  lots: LotResponse[] = [];
  nextLot: LotResponse | null = null;
  error = '';
  message = '';
  isAdmin = false;
  canWrite = false;
  adjusting = false;

  readonly lotTone = lotStatutTone;
  readonly medicamentTone = medicamentStatutTone;

  private readonly fb = inject(FormBuilder);

  readonly adjustForm = this.fb.group({
    lotId: ['', Validators.required],
    type: ['sortie', Validators.required],
    quantite: ['', Validators.required],
    motif: [''],
    reference: [''],
  });

  constructor(
    private catalog: CatalogService,
    private auth: AuthService,
    private route: ActivatedRoute,
  ) {}

  ngOnInit(): void {
    const role = this.auth.currentUser()?.role;
    this.isAdmin = role === ROLES.ADMIN;
    this.canWrite = role !== undefined && (WRITE_ROLES as readonly string[]).includes(role);
    const id = this.route.snapshot.paramMap.get('id');
    if (id) this.loadAll(id);
  }

  private loadAll(id: string): void {
    this.error = '';
    this.catalog.getMedicament(id).subscribe({
      next: (m) => (this.m = m),
      error: (err: unknown) => (this.error = problemDetail(err, 'Erreur de chargement.')),
    });
    this.catalog.listLotsByMedicament(id, { page: 0, size: 50 }).subscribe({
      next: (res) => (this.lots = res.content),
      error: (err: unknown) =>
        (this.error = problemDetail(err, 'Erreur de chargement des lots.')),
    });
    this.catalog.nextLotToDispense(id).subscribe({
      next: (l) => (this.nextLot = l),
      error: () => (this.nextLot = null),
    });
  }

  onAdjust(): void {
    if (this.adjustForm.invalid || !this.m) return;
    this.adjusting = true;
    this.message = '';
    this.error = '';
    const v = this.adjustForm.value;
    this.catalog
      .adjustStock(v.lotId ?? '', {
        type: (v.type as 'sortie') ?? 'sortie',
        quantite: Number(v.quantite),
        motif: v.motif || undefined,
        reference: v.reference || undefined,
      })
      .subscribe({
        next: (res) => {
          this.message = `Stock ajusté : ${res.mouvement.quantiteApres} unités restantes.`;
          this.adjusting = false;
          this.loadAll(this.m!.id);
        },
        error: (err: unknown) => {
          this.adjusting = false;
          this.error = problemDetail(err, "Erreur lors de l'ajustement.");
        },
      });
  }

  retirer(): void {
    if (!this.m) return;
    if (!confirm(`Retirer ${this.m.nomCommercialFr} du catalogue ?`)) return;
    this.catalog.retireMedicament(this.m.id).subscribe({
      next: () => this.loadAll(this.m!.id),
      error: (err: unknown) => (this.error = problemDetail(err, 'Erreur.')),
    });
  }
}