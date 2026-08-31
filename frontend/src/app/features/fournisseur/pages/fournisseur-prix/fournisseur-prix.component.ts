import { Component, inject, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { problemDetail } from '@core/http/http-error.util';
import { PaginationComponent } from '@shared/components/pagination/pagination.component';
import type {
  FournisseurMedicamentResponse,
  UpdatePrixItem,
} from '@features/fournisseur/models/fournisseur.models';
import { FournisseurService } from '@features/fournisseur/services/fournisseur.service';

interface EditableRow extends UpdatePrixItem {
  medicamentNom: string;
  dci?: string;
  forme?: string;
  dosage?: string;
  reference?: string;
  stockMinimum: number;
  derniereMiseAJour?: string;
  original: UpdatePrixItem;
  dirty: boolean;
}

@Component({
  selector: 'app-fournisseur-prix',
  standalone: true,
  imports: [RouterLink, FormsModule, PaginationComponent],
  templateUrl: './fournisseur-prix.component.html',
  styleUrl: './fournisseur-prix.component.css',
})
export class FournisseurPrixComponent implements OnInit {
  rows: EditableRow[] = [];
  page = 0;
  size = 15;
  totalElements = 0;
  totalPages = 0;
  error = '';
  message = '';
  saving = false;

  private readonly service = inject(FournisseurService);

  ngOnInit(): void {
    this.reload();
  }

  reload(): void {
    this.error = '';
    this.service.prix(this.page, this.size).subscribe({
      next: (res) => {
        this.rows = res.content.map((fm) => ({
          id: fm.id,
          prixAchat: fm.prixAchat,
          stockDisponible: fm.stockDisponible,
          disponible: fm.disponible,
          medicamentNom: fm.medicamentNom,
          dci: fm.dci,
          forme: fm.formePharmaceutique,
          dosage: fm.dosage,
          reference: fm.referenceFournisseur,
          stockMinimum: fm.stockMinimum,
          derniereMiseAJour: fm.derniereMiseAJour,
          original: { id: fm.id, prixAchat: fm.prixAchat, stockDisponible: fm.stockDisponible, disponible: fm.disponible },
          dirty: false,
        }));
        this.page = res.page;
        this.totalElements = res.totalElements;
        this.totalPages = res.totalPages;
      },
      error: (err: unknown) => {
        this.rows = [];
        this.error = problemDetail(err, 'Erreur lors du chargement du catalogue.');
      },
    });
  }

  markDirty(r: EditableRow): void {
    r.dirty =
      r.prixAchat !== r.original.prixAchat ||
      r.stockDisponible !== r.original.stockDisponible ||
      r.disponible !== r.original.disponible;
  }

  save(): void {
    const changed = this.rows.filter((r) => r.dirty).map((r) => ({ id: r.id, prixAchat: r.prixAchat, stockDisponible: r.stockDisponible, disponible: r.disponible }));
    if (changed.length === 0) {
      this.message = 'Aucune modification à enregistrer.';
      return;
    }
    this.saving = true;
    this.error = '';
    this.message = '';
    this.service.updatePrix(changed as UpdatePrixItem[]).subscribe({
      next: () => {
        this.saving = false;
        this.message = `${changed.length} ligne(s) mise(s) à jour.`;
        this.reload();
      },
      error: (err: unknown) => {
        this.saving = false;
        this.error = problemDetail(err, 'Erreur lors de la mise à jour.');
      },
    });
  }

  go(p: number): void {
    this.page = p;
    this.reload();
  }
}