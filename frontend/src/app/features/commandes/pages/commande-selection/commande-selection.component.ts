import { Component, inject, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { problemDetail } from '@core/http/http-error.util';
import { PaginationComponent } from '@shared/components/pagination/pagination.component';
import type { MedicamentSelectionResponse } from '@features/fournisseur/models/fournisseur.models';
import { CommandeService } from '@features/commandes/services/commande.service';

@Component({
  selector: 'app-commande-selection',
  standalone: true,
  imports: [RouterLink, FormsModule, PaginationComponent],
  templateUrl: './commande-selection.component.html',
  styleUrl: './commande-selection.component.css',
})
export class CommandeSelectionComponent implements OnInit {
  medicaments: MedicamentSelectionResponse[] = [];
  page = 0;
  size = 15;
  totalElements = 0;
  totalPages = 0;
  error = '';
  loading = true;

  search = '';

  private searchTimer: ReturnType<typeof setTimeout> | null = null;

  private readonly service = inject(CommandeService);

  ngOnInit(): void {
    this.reload();
  }

  onSearchChange(_value: string): void {
    if (this.searchTimer) clearTimeout(this.searchTimer);
    this.searchTimer = setTimeout(() => {
      this.page = 0;
      this.reload();
    }, 350);
  }

  reload(): void {
    this.loading = true;
    this.error = '';
    this.service
      .medicaments({ search: this.search || undefined, page: this.page, size: this.size })
      .subscribe({
        next: (res) => {
          this.medicaments = res.content;
          this.page = res.page;
          this.totalElements = res.totalElements;
          this.totalPages = res.totalPages;
          this.loading = false;
        },
        error: (err: unknown) => {
          this.medicaments = [];
          this.error = problemDetail(err, 'Erreur lors du chargement des médicaments.');
          this.loading = false;
        },
      });
  }

  go(p: number): void {
    this.page = p;
    this.reload();
  }
}