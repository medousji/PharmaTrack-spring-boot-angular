import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';

import { problemDetail } from '@core/http/http-error.util';
import { PaginationComponent } from '@shared/components/pagination/pagination.component';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  MouvementResponse,
  MouvementType,
  mouvementTypeTone,
} from '@features/catalog/models/catalog.models';
import { CatalogService } from '@features/catalog/services/catalog.service';

@Component({
  selector: 'app-mouvements',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, StatusPillComponent, PaginationComponent],
  templateUrl: './mouvements.component.html',
  styleUrl: './mouvements.component.css',
})
export class MouvementsComponent implements OnInit {
  items: MouvementResponse[] = [];
  page = 0;
  size = 20;
  totalElements = 0;
  totalPages = 0;
  error = '';

  lotId = '';
  type: '' | MouvementType = '';
  from = '';
  to = '';

  readonly mouvementTone = mouvementTypeTone;

  constructor(
    private catalog: CatalogService,
    private route: ActivatedRoute,
  ) {}

  ngOnInit(): void {
    this.lotId = this.route.snapshot.queryParamMap.get('lotId') ?? '';
    this.reload();
  }

  reload(): void {
    this.error = '';
    this.catalog
      .listMouvements({
        lotId: this.lotId || undefined,
        type: this.type || undefined,
        from: this.from || undefined,
        to: this.to || undefined,
        page: this.page,
        size: this.size,
      })
      .subscribe({
        next: (res) => {
          this.items = res.content;
          this.page = res.page;
          this.totalElements = res.totalElements;
          this.totalPages = res.totalPages;
        },
        error: (err: unknown) =>
          (this.error = problemDetail(err, 'Erreur de chargement des mouvements.')),
      });
  }

  go(p: number): void {
    this.page = p;
    this.reload();
  }
}