import { Component, inject, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { problemDetail } from '@core/http/http-error.util';
import type { AuthUser, UserRole, UserStatus } from '@core/models/auth.models';
import { AuthService } from '@core/services/auth.service';
import { PaginationComponent } from '@shared/components/pagination/pagination.component';
import type { AdminUserStats } from '@features/users/models/admin-user.model';
import { AdminUsersService } from '@features/users/services/admin-users.service';

const ROLE_LABELS: Readonly<Record<UserRole, string>> = {
  admin: 'Administrateur',
  pharmacien: 'Pharmacien',
  fournisseur: 'Fournisseur',
  visiteur: 'Visiteur',
};

const STATUS_LABELS: Readonly<Record<UserStatus, string>> = {
  active: 'Actif',
  inactive: 'Inactif',
  suspended: 'Suspendu',
};

@Component({
  selector: 'app-users-list',
  standalone: true,
  imports: [RouterLink, FormsModule, PaginationComponent],
  templateUrl: './users-list.component.html',
  styleUrl: './users-list.component.css',
})
export class UsersListComponent implements OnInit {
  users: AuthUser[] = [];
  stats: AdminUserStats | null = null;
  page = 0;
  size = 15;
  totalElements = 0;
  totalPages = 0;
  error = '';
  message = '';

  search = '';
  role: '' | UserRole = '';
  statut: '' | UserStatus = '';

  private searchTimer: ReturnType<typeof setTimeout> | null = null;

  private readonly service = inject(AdminUsersService);
  private readonly auth = inject(AuthService);

  readonly currentUserId = this.auth.currentUser()?.id ?? null;

  ngOnInit(): void {
    this.loadStats();
    this.reload();
  }

  roleLabel(role: UserRole): string {
    return ROLE_LABELS[role] ?? role;
  }

  statusLabel(status: string): string {
    return STATUS_LABELS[status as UserStatus] ?? status;
  }

  createdDate(iso: string | undefined): string {
    return iso ? this.date(iso) : '—';
  }

  onSearchChange(_value: string): void {
    if (this.searchTimer) clearTimeout(this.searchTimer);
    this.searchTimer = setTimeout(() => {
      this.page = 0;
      this.reload();
    }, 350);
  }

  reload(): void {
    this.error = '';
    this.service
      .listUsers({
        search: this.search || undefined,
        role: this.role || undefined,
        statut: this.statut || undefined,
        page: this.page,
        size: this.size,
      })
      .subscribe({
        next: (res) => {
          this.users = res.content;
          this.page = res.page;
          this.totalElements = res.totalElements;
          this.totalPages = res.totalPages;
        },
        error: (err: unknown) => {
          this.users = [];
          this.error = problemDetail(err, 'Erreur lors du chargement des utilisateurs.');
        },
      });
  }

  loadStats(): void {
    this.service.stats().subscribe({
      next: (s) => (this.stats = s),
      error: () => (this.stats = null),
    });
  }

  remove(user: AuthUser): void {
    const label = String(user.name ?? user.email);
    if (!confirm(`Supprimer définitivement l'utilisateur « ${label} » ?`)) {
      return;
    }
    this.message = '';
    this.error = '';
    this.service.deleteUsers(user.id).subscribe({
      next: () => {
        this.message = `Utilisateur « ${label} » supprimé.`;
        this.loadStats();
        this.reload();
      },
      error: (err: unknown) => {
        this.error = problemDetail(err, "Erreur lors de la suppression.");
      },
    });
  }

  go(p: number): void {
    this.page = p;
    this.reload();
  }

  private date(iso: string): string {
    const d = new Date(iso);
    if (!Number.isNaN(d.getTime())) {
      return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
    }
    return iso;
  }
}