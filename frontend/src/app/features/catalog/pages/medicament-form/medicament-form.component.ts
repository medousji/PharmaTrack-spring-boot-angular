import { Component, inject, OnInit } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';

import { problemDetail } from '@core/http/http-error.util';
import {
  MedicamentDetailResponse,
  MedicamentStatut,
} from '@features/catalog/models/catalog.models';
import { CatalogService } from '@features/catalog/services/catalog.service';

function num(v: unknown): number | undefined {
  if (v === null || v === undefined || v === '') return undefined;
  const n = Number(v);
  return Number.isFinite(n) ? n : undefined;
}

@Component({
  selector: 'app-medicament-form',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './medicament-form.component.html',
  styleUrl: './medicament-form.component.css',
})
export class MedicamentFormComponent implements OnInit {
  editId: string | null = null;
  loading = false;
  saving = false;
  error = '';
  medicaments: Array<{ id: string; nomCommercialFr: string }> = [];

  private readonly fb = inject(FormBuilder);

  readonly form = this.fb.group({
    codeCip: ['', Validators.required],
    nomCommercialFr: ['', Validators.required],
    nomCommercialAr: [''],
    dci: ['', Validators.required],
    formePharmaceutique: ['', Validators.required],
    dosage: ['', Validators.required],
    conditionnement: [''],
    ppv: [''],
    ph: [''],
    prixBr: [''],
    prixPublic: [''],
    tauxRemboursement: [''],
    laboratoire: [''],
    paysOrigine: [''],
    stockMin: [0, Validators.required],
    stockMax: [0, Validators.required],
    seuilAlerte: [0, Validators.required],
    classeTherapeutique: [''],
    voieAdministration: [''],
    contreIndications: [''],
    effetsIndesirables: [''],
    interactionsMedicamenteuses: [''],
    conditionsConservation: [''],
    codeAtc: [''],
    estPsychotrope: [false],
    estTherLourde: [false],
    estRenouvelable: [false],
    delaiRenouvellement: [''],
    codeBarre: [''],
    estGenerique: [false],
    medicamentReferenceId: [''],
    statut: ['actif'],
  });

  constructor(
    private catalog: CatalogService,
    private route: ActivatedRoute,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.editId = this.route.snapshot.paramMap.get('id');
    if (this.editId) {
      this.loading = true;
      this.catalog.getMedicament(this.editId).subscribe({
        next: (m) => this.form.patchValue(this.toForm(m)),
        error: (err: unknown) => {
          this.error = problemDetail(err, 'Impossible de charger le médicament.');
        },
        complete: () => (this.loading = false),
      });
    }
    this.catalog
      .listMedicaments({ page: 0, size: 100 })
      .subscribe((res) => (this.medicaments = res.content));
  }

  private toForm(m: MedicamentDetailResponse): Record<string, unknown> {
    return {
      codeCip: m.codeCip,
      nomCommercialFr: m.nomCommercialFr,
      nomCommercialAr: m.nomCommercialAr ?? '',
      dci: m.dci,
      formePharmaceutique: m.formePharmaceutique,
      dosage: m.dosage,
      conditionnement: m.conditionnement ?? '',
      ppv: m.ppv ?? '',
      ph: m.ph ?? '',
      prixBr: m.prixBr ?? '',
      prixPublic: m.prixPublic ?? '',
      tauxRemboursement: m.tauxRemboursement ?? '',
      laboratoire: m.laboratoire ?? '',
      paysOrigine: m.paysOrigine ?? '',
      stockMin: m.stockMin,
      stockMax: m.stockMax,
      seuilAlerte: m.seuilAlerte,
      classeTherapeutique: m.classeTherapeutique ?? '',
      voieAdministration: m.voieAdministration ?? '',
      contreIndications: m.contreIndications ?? '',
      effetsIndesirables: m.effetsIndesirables ?? '',
      interactionsMedicamenteuses: m.interactionsMedicamenteuses ?? '',
      conditionsConservation: m.conditionsConservation ?? '',
      codeAtc: m.codeAtc ?? '',
      estPsychotrope: m.estPsychotrope,
      estTherLourde: m.estTherLourde,
      estRenouvelable: m.estRenouvelable,
      delaiRenouvellement: m.delaiRenouvellement ?? '',
      codeBarre: m.codeBarre ?? '',
      estGenerique: m.estGenerique,
      medicamentReferenceId: m.medicamentReferenceId ?? '',
      statut: m.statut,
    };
  }

  private toPayload(v: MedicamentFormComponent['form']['value']): {
    codeCip: string;
    nomCommercialFr: string;
    nomCommercialAr?: string;
    dci: string;
    formePharmaceutique: string;
    dosage: string;
    conditionnement?: string;
    ppv?: number;
    ph?: number;
    prixBr?: number;
    prixPublic?: number;
    tauxRemboursement?: number;
    laboratoire?: string;
    paysOrigine?: string;
    stockMin: number;
    stockMax: number;
    seuilAlerte: number;
    classeTherapeutique?: string;
    voieAdministration?: string;
    contreIndications?: string;
    effetsIndesirables?: string;
    interactionsMedicamenteuses?: string;
    conditionsConservation?: string;
    codeAtc?: string;
    estPsychotrope: boolean;
    estTherLourde: boolean;
    estRenouvelable: boolean;
    delaiRenouvellement?: number;
    codeBarre?: string;
    estGenerique: boolean;
    medicamentReferenceId?: string;
  } {
    return {
      codeCip: v.codeCip ?? '',
      nomCommercialFr: v.nomCommercialFr ?? '',
      nomCommercialAr: v.nomCommercialAr || undefined,
      dci: v.dci ?? '',
      formePharmaceutique: v.formePharmaceutique ?? '',
      dosage: v.dosage ?? '',
      conditionnement: v.conditionnement || undefined,
      ppv: num(v.ppv),
      ph: num(v.ph),
      prixBr: num(v.prixBr),
      prixPublic: num(v.prixPublic),
      tauxRemboursement: num(v.tauxRemboursement),
      laboratoire: v.laboratoire || undefined,
      paysOrigine: v.paysOrigine || undefined,
      stockMin: num(v.stockMin) ?? 0,
      stockMax: num(v.stockMax) ?? 0,
      seuilAlerte: num(v.seuilAlerte) ?? 0,
      classeTherapeutique: v.classeTherapeutique || undefined,
      voieAdministration: v.voieAdministration || undefined,
      contreIndications: v.contreIndications || undefined,
      effetsIndesirables: v.effetsIndesirables || undefined,
      interactionsMedicamenteuses: v.interactionsMedicamenteuses || undefined,
      conditionsConservation: v.conditionsConservation || undefined,
      codeAtc: v.codeAtc || undefined,
      estPsychotrope: !!v.estPsychotrope,
      estTherLourde: !!v.estTherLourde,
      estRenouvelable: !!v.estRenouvelable,
      delaiRenouvellement: num(v.delaiRenouvellement),
      codeBarre: v.codeBarre || undefined,
      estGenerique: !!v.estGenerique,
      medicamentReferenceId: v.medicamentReferenceId || undefined,
    };
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.error = 'Veuillez renseigner tous les champs obligatoires.';
      return;
    }
    this.saving = true;
    this.error = '';
    const payload = this.toPayload(this.form.value);

    if (this.editId) {
      this.catalog
        .updateMedicament(this.editId, {
          ...payload,
          statut: (this.form.value.statut ?? 'actif') as MedicamentStatut,
        })
        .subscribe({
          next: () => void this.router.navigate(['/medicaments']),
          error: (err: unknown) => {
            this.saving = false;
            this.error = problemDetail(err, "Erreur pendant l'enregistrement.");
          },
        });
      return;
    }

    this.catalog.createMedicament(payload).subscribe({
      next: () => void this.router.navigate(['/medicaments']),
      error: (err: unknown) => {
        this.saving = false;
        this.error = problemDetail(err, "Erreur pendant l'enregistrement.");
      },
    });
  }
}