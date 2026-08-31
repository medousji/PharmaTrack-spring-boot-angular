import { Component, inject, OnInit } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';

import { problemDetail } from '@core/http/http-error.util';
import type { UserRole, UserStatus } from '@core/models/auth.models';
import type { Pharmacie } from '@features/users/models/admin-user.model';
import { AdminUsersService } from '@features/users/services/admin-users.service';

@Component({
  selector: 'app-user-form',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './user-form.component.html',
  styleUrl: './user-form.component.css',
})
export class UserFormComponent implements OnInit {
  editId: string | null = null;
  loading = false;
  saving = false;
  error = '';
  pharmaciess: Pharmacie[] = [];

  private readonly fb = inject(FormBuilder);

  readonly form = this.fb.group({
    name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    password: [''],
    role: ['pharmacien' as UserRole, Validators.required],
    status: ['active' as UserStatus],
    pharmacieId: [''],
  });

  constructor(
    private users: AdminUsersService,
    private route: ActivatedRoute,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.editId = this.route.snapshot.paramMap.get('id');
    const password = this.form.get('password');
    if (this.editId) {
      password?.clearValidators();
    } else {
      password?.setValidators([Validators.required, Validators.minLength(8)]);
    }
    password?.updateValueAndValidity();
    this.users.listPharmacies().subscribe({
      next: (p) => (this.pharmaciess = p),
      error: () => (this.pharmaciess = []),
    });
    if (this.editId) {
      this.loading = true;
      this.users.getUser(this.editId).subscribe({
        next: (u) =>
          this.form.patchValue({
            name: u.name,
            email: u.email,
            password: '',
            role: u.role,
            status: u.status as UserStatus,
            pharmacieId: u.pharmacieId ?? '',
          }),
        error: (err: unknown) => {
          this.error = problemDetail(err, 'Impossible de charger l\'utilisateur.');
        },
        complete: () => (this.loading = false),
      });
    }
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.error = 'Veuillez renseigner tous les champs obligatoires.';
      return;
    }
    this.saving = true;
    this.error = '';
    const v = this.form.value;
    const payload = {
      name: v.name ?? '',
      email: v.email ?? '',
      role: v.role as UserRole,
      status: (v.status ?? 'active') as UserStatus,
      pharmacieId: v.pharmacieId || undefined,
    };

    if (this.editId) {
      const password = v.password && v.password.length >= 8 ? v.password : undefined;
      this.users.updateUser(this.editId, { ...payload, password }).subscribe({
        next: () => void this.router.navigate(['/admin/users']),
        error: (err: unknown) => {
          this.saving = false;
          this.error = problemDetail(err, 'Erreur pendant l\'enregistrement.');
        },
      });
      return;
    }

    this.users
      .createUser({ ...payload, password: v.password ?? '' })
      .subscribe({
        next: () => void this.router.navigate(['/admin/users']),
        error: (err: unknown) => {
          this.saving = false;
          this.error = problemDetail(err, 'Erreur pendant l\'enregistrement.');
        },
      });
  }
}