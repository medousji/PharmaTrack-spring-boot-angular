import { Component, inject, OnInit } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { problemDetail } from '@core/http/http-error.util';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  commandeStatutLabel,
  commandeStatutTone,
  type CommandeResult,
  type DisponibiliteResponse,
  type FournisseurMedicamentResponse,
} from '@features/fournisseur/models/fournisseur.models';
import { CommandeService } from '@features/commandes/services/commande.service';

@Component({
  selector: 'app-commande-creer',
  standalone: true,
  imports: [RouterLink, FormsModule, StatusPillComponent],
  templateUrl: './commande-creer.component.html',
  styleUrl: './commande-creer.component.css',
})
export class CommandeCreerComponent implements OnInit {
  medicamentId = '';
  fournisseurs: FournisseurMedicamentResponse[] = [];
  selectedId = '';
  quantite = 1;

  medicamentNom = '—';

  loading = true;
  error = '';
  message = '';

  verif: DisponibiliteResponse | null = null;
  verifLoading = false;
  verifError = '';

  result: CommandeResult | null = null;
  passLoading = false;
  passError = '';

  private readonly route = inject(ActivatedRoute);
  private readonly service = inject(CommandeService);

  statutLabel = commandeStatutLabel;
  statutTone = commandeStatutTone;

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('medicamentId');
    if (!id) {
      this.error = 'Médicament introuvable.';
      this.loading = false;
      return;
    }
    this.medicamentId = id;
    this.loadFournisseurs();
  }

  loadFournisseurs(): void {
    this.loading = true;
    this.error = '';
    this.message = '';
    this.service.fournisseurs(this.medicamentId).subscribe({
      next: (fms) => {
        this.fournisseurs = fms;
        if (fms.length > 0) {
          this.selectedId = fms[0].id;
          this.medicamentNom = fms[0].medicamentNom ?? 'Médicament';
        } else {
          this.message = 'Aucun fournisseur ne propose ce médicament pour le moment.';
          this.medicamentNom = 'Médicament';
        }
        this.loading = false;
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, 'Erreur lors du chargement des fournisseurs.');
        this.loading = false;
      },
    });
  }

  selectedFm(): FournisseurMedicamentResponse | null {
    return this.fournisseurs.find((f) => f.id === this.selectedId) ?? null;
  }

  checkDisponibilite(): void {
    if (!this.selectedId || this.quantite < 1) {
      this.verifError = 'Sélectionnez un fournisseur et saisissez une quantité valide.';
      return;
    }
    this.verifLoading = true;
    this.verifError = '';
    this.verif = null;
    this.service.verifierDisponibilite(this.selectedId, this.quantite).subscribe({
      next: (v) => {
        this.verif = v;
        this.verifLoading = false;
      },
      error: (err: unknown) => {
        this.verifError = problemDetail(err, 'Erreur lors de la vérification.');
        this.verifLoading = false;
      },
    });
  }

  passer(): void {
    if (!this.selectedId || this.quantite < 1) {
      this.passError = 'Sélectionnez un fournisseur et saisissez une quantité valide.';
      return;
    }
    this.passLoading = true;
    this.passError = '';
    this.result = null;
    this.service.passer(this.selectedId, this.quantite).subscribe({
      next: (r) => {
        this.result = r;
        this.passLoading = false;
      },
      error: (err: unknown) => {
        this.passError = problemDetail(err, "Erreur lors de la commande.");
        this.passLoading = false;
      },
    });
  }

  choisirAlternatif(id: string): void {
    this.selectedId = id;
    this.verif = null;
    this.result = null;
    this.checkDisponibilite();
  }

  reset(): void {
    this.verif = null;
    this.result = null;
    this.passError = '';
    this.verifError = '';
    this.message = '';
    this.quantite = 1;
  }
}