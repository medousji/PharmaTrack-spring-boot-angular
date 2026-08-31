import { Component, inject, OnInit } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';

import { problemDetail } from '@core/http/http-error.util';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  commandeStatutLabel,
  commandeStatutTone,
  type CommandeResponse,
  type CommandeStatut,
} from '@features/fournisseur/models/fournisseur.models';
import { FournisseurService } from '@features/fournisseur/services/fournisseur.service';

@Component({
  selector: 'app-fournisseur-commande-detail',
  standalone: true,
  imports: [RouterLink, StatusPillComponent],
  templateUrl: './fournisseur-commande-detail.component.html',
  styleUrl: './fournisseur-commande-detail.component.css',
})
export class FournisseurCommandeDetailComponent implements OnInit {
  commande: CommandeResponse | null = null;
  error = '';
  message = '';
  loading = true;

  private readonly route = inject(ActivatedRoute);
  private readonly service = inject(FournisseurService);

  ngOnInit(): void {
    this.load();
  }

  statutLabel = commandeStatutLabel;
  statutTone = commandeStatutTone;

  load(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) {
      this.error = 'Commande introuvable.';
      this.loading = false;
      return;
    }
    this.loading = true;
    this.error = '';
    this.service.getCommande(id).subscribe({
      next: (c) => {
        this.commande = c;
        this.loading = false;
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, 'Erreur lors du chargement de la commande.');
        this.loading = false;
      },
    });
  }

  canExpedier(statut: CommandeStatut): boolean {
    return statut === 'en_attente' || statut === 'confirmee' || statut === 'preparation';
  }

  expedier(): void {
    const c = this.commande;
    if (!c) {
      return;
    }
    if (!confirm(`Expédier la commande « ${c.numeroCommande} » ?`)) {
      return;
    }
    this.error = '';
    this.message = '';
    this.service.expedier(c.id).subscribe({
      next: (updated) => {
        this.commande = updated;
        this.message = `Commande « ${updated.numeroCommande} » expédiée. Livraison prévue le ${this.date(updated.dateLivraisonPrevue ?? '')}.`;
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, "Erreur lors de l'expédition.");
      },
    });
  }

  date(iso: string): string {
    const d = new Date(`${iso}T00:00:00`);
    if (!Number.isNaN(d.getTime())) {
      return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
    }
    return iso;
  }
}