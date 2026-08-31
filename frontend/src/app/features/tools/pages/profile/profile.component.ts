import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';

import { AuthService } from '@core/services/auth.service';
import { roleLabel } from '@features/chat/models/chat.models';
import type { AuthUser } from '@core/models/auth.models';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.css',
})
export class ProfileComponent implements OnInit {
  user: AuthUser | null = null;

  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);

  ngOnInit(): void {
    this.user = this.auth.currentUser();
    this.auth.me().subscribe({
      next: (u) => (this.user = u),
      error: () => {},
    });
  }

  roleLabel(): string {
    return roleLabel(this.user?.role ?? 'visiteur');
  }

  logout(): void {
    this.auth.logout().subscribe({ error: () => {} });
    void this.router.navigate(['/login']);
  }
}