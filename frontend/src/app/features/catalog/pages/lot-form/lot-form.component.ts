import { Component, inject, OnInit } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';

import { problemDetail } from '@core/http/http-error.util';
import { CatalogService } from '@features/catalog/services/catalog.service';
import type { MedicamentDetailResponse } from '@features/catalog/models/catalog.models';

@Component({
  selector: 'app-lot-form',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, CommonModule],
  templateUrl: './lot-form.component.html',
  styleUrl: './lot-form.component.css',
})
export class LotFormComponent implements OnInit {
  medicamentId = '';
  backLink = '/lots';
  medicaments: MedicamentDetailResponse[] = [];
  loadingMedicaments = true;
  saving = false;
  error = '';

  private readonly fb = inject(FormBuilder);

  readonly form = this.fb.group({
    medicamentId: ['', Validators.required],
    numeroLot: ['', Validators.required],
    fournisseurNom: [''],
    dateReception: [''],
    dateFabrication: [''],
    datePeremption: ['', Validators.required],
    quantiteInitiale: ['', Validators.required],
    prixAchat: ['', Validators.required],
    prixVente: ['', Validators.required],
    numeroFacture: [''],
    emplacement: [''],
    observations: [''],
  });

  constructor(
    private catalog: CatalogService,
    private route: ActivatedRoute,
    private router: Router,
  ) {}

  get medicamentLabel(): string {
    const m = this.medicaments.find((x) => x.id === this.medicamentId);
    return m ? m.nomCommercialFr : '—';
  }

  ngOnInit(): void {
    this.medicamentId = this.route.snapshot.paramMap.get('id') ?? '';
    if (this.medicamentId) {
      this.backLink = `/medicaments/${this.medicamentId}`;
      this.form.controls.medicamentId.setValue(this.medicamentId);
    }
    this.catalog.listMedicaments({ page: 0, size: 500 }).subscribe({
      next: (res) => {
        this.medicaments = res.content;
        this.loadingMedicaments = false;
        if (!this.medicamentId) this.form.controls.medicamentId.setValue('');
      },
      error: () => {
        this.loadingMedicaments = false;
      },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) return;
    this.saving = true;
    this.error = '';
    const v = this.form.value;
    const link = this.backLink;
    this.catalog
      .createLot({
        medicamentId: v.medicamentId ?? '',
        numeroLot: v.numeroLot ?? '',
        fournisseurNom: v.fournisseurNom || undefined,
        dateReception: v.dateReception || undefined,
        dateFabrication: v.dateFabrication || undefined,
        datePeremption: v.datePeremption ?? '',
        quantiteInitiale: Number(v.quantiteInitiale),
        prixAchat: Number(v.prixAchat),
        prixVente: Number(v.prixVente),
        numeroFacture: v.numeroFacture || undefined,
        emplacement: v.emplacement || undefined,
        observations: v.observations || undefined,
      })
      .subscribe({
        next: () => void this.router.navigate([link]),
        error: (err: unknown) => {
          this.saving = false;
          this.error = problemDetail(err, 'Erreur lors de la réception du lot.');
        },
      });
  }
}
