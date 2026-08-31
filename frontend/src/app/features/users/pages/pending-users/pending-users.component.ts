import { Component, OnInit } from '@angular/core';

import { problemDetail } from '@core/http/http-error.util';
import type { PagedResponse } from '@core/models/api.models';
import type { PendingUser } from '@features/users/models/admin-user.model';
import { AdminUsersService } from '@features/users/services/admin-users.service';

@Component({
  selector: 'app-pending-users',
  standalone: true,
  imports: [],
  templateUrl: './pending-users.component.html',
  styleUrl: './pending-users.component.css',
})
export class PendingUsersComponent implements OnInit {
  pending: PendingUser[] = [];
  message = '';
  error = '';

  constructor(private users: AdminUsersService) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.message = '';
    this.error = '';
    this.users.listPending().subscribe({
      next: (res: PagedResponse<PendingUser>) => {
        this.pending = res.content;
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, 'Erreur lors du chargement.');
      },
    });
  }

  approve(u: PendingUser, role: 'pharmacien' | 'fournisseur'): void {
    this.users.approve(u.id, { approved: true, role }).subscribe({
      next: () => {
        this.pending = this.pending.filter((x) => x.id !== u.id);
        this.message = `${u.name} approuvé(e).`;
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, "Erreur à l'approbation.");
      },
    });
  }

  reject(u: PendingUser): void {
    this.users.approve(u.id, { approved: false }).subscribe({
      next: () => {
        this.pending = this.pending.filter((x) => x.id !== u.id);
        this.message = `${u.name} rejeté(e).`;
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, 'Erreur au rejet.');
      },
    });
  }
}