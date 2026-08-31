import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { of } from 'rxjs';
import { map, switchMap } from 'rxjs/operators';

import { ROLES } from '@core/constants/app.constants';
import { AuthService } from '@core/services/auth.service';
import { AlerteService } from '@features/alertes/services/alerte.service';
import { AdminUsersService } from '@features/users/services/admin-users.service';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './app-header.component.html',
  styleUrl: './app-header.component.css',
})
export class AppHeaderComponent {
  readonly adminRole = ROLES.ADMIN;
  readonly pharmacienRole = ROLES.PHARMACIEN;
  readonly fournisseurRole = ROLES.FOURNISSEUR;
  readonly visiteurRole = ROLES.VISITEUR;

  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);
  private readonly alertes = inject(AlerteService);
  private readonly adminUsers = inject(AdminUsersService);

  readonly user$ = this.auth.user$;

  /** Approbations en attente (admin uniquement). */
  readonly pendingCount$ = this.user$.pipe(
    switchMap((u) =>
      u?.role === this.adminRole
        ? this.adminUsers.listPending(0, 1).pipe(map((p) => p.totalElements))
        : of(0),
    ),
  );

  /** Alertes non lues (tous les comptes actifs). */
  readonly unreadAlertes$ = this.user$.pipe(
    switchMap((u) =>
      u && u.role !== this.visiteurRole
        ? this.alertes.list({ estLue: false, page: 0, size: 1 }).pipe(map((a) => a.totalElements))
        : of(0),
    ),
  );

  roleLabel(role: string): string {
    switch (role) {
      case this.adminRole:
        return 'Administrateur';
      case this.pharmacienRole:
        return 'Pharmacien';
      case this.fournisseurRole:
        return 'Fournisseur';
      default:
        return 'Visiteur';
    }
  }

  logout(): void {
    this.auth.logout().subscribe(() => void this.router.navigate(['/login']));
  }
}