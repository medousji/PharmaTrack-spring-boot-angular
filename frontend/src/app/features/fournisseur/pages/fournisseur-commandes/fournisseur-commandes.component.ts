import { Component, inject, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { problemDetail } from '@core/http/http-error.util';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import { PaginationComponent } from '@shared/components/pagination/pagination.component';
import {
  COMMANDE_STATUTS,
  commandeStatutLabel,
  commandeStatutTone,
  type CommandeResponse,
  type CommandeStatut,
} from '@features/fournisseur/models/fournisseur.models';
import { FournisseurService } from '@features/fournisseur/services/fournisseur.service';

@Component({
  selector: 'app-fournisseur-commandes',
  standalone: true,
  imports: [RouterLink, FormsModule, StatusPillComponent, PaginationComponent],
  templateUrl: './fournisseur-commandes.component.html',
  styleUrl: './fournisseur-commandes.component.css',
})
export class FournisseurCommandesComponent implements OnInit {
  commandes: CommandeResponse[] = [];
  page = 0;
  size = 15;
  totalElements = 0;
  totalPages = 0;
  error = '';
  message = '';
  loading = true;

  statut: '' | CommandeStatut = '';
  readonly commandeStatuts = COMMANDE_STATUTS;

  private readonly service = inject(FournisseurService);

  ngOnInit(): void {
    this.reload();
  }

  statutLabel = commandeStatutLabel;
  statutTone = commandeStatutTone;

  reload(): void {
    this.loading = true;
    this.error = '';
    this.service
      .commandes({ statut: this.statut || undefined, page: this.page, size: this.size })
      .subscribe({
        next: (res) => {
          this.commandes = res.content;
          this.page = res.page;
          this.totalElements = res.totalElements;
          this.totalPages = res.totalPages;
          this.loading = false;
        },
        error: (err: unknown) => {
          this.commandes = [];
          this.error = problemDetail(err, 'Erreur lors du chargement des commandes.');
          this.loading = false;
        },
      });
  }

  canExpedier(statut: CommandeStatut): boolean {
    return statut === 'en_attente' || statut === 'confirmee' || statut === 'preparation';
  }

  expedier(c: CommandeResponse): void {
    if (!confirm(`Expédier la commande « ${c.numeroCommande} » ?`)) {
      return;
    }
    this.error = '';
    this.message = '';
    this.service.expedier(c.id).subscribe({
      next: () => {
        this.message = `Commande « ${c.numeroCommande} » expédiée.`;
        this.reload();
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, "Erreur lors de l'expédition.");
      },
    });
  }

  go(p: number): void {
    this.page = p;
    this.reload();
  }

  date(iso: string): string {
    const d = new Date(`${iso}T00:00:00`);
    if (!Number.isNaN(d.getTime())) {
      return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
    }
    return iso;
  }
}