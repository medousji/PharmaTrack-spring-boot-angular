import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { DEFAULT_PAGE_SIZE } from '@core/constants/app.constants';
import { toHttpParams } from '@core/http/http-params.util';
import type { PagedResponse } from '@core/models/api.models';
import type { AuthUser } from '@core/models/auth.models';
import { environment } from '@env/environment';
import type {
  AdminUserStats,
  ApproveRequest,
  CreateUserRequest,
  Pharmacie,
  PendingUser,
  UpdateUserRequest,
  UserListParams,
} from '../models/admin-user.model';

@Injectable({ providedIn: 'root' })
export class AdminUsersService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl}/admin/users`;
  private readonly pharmacies = `${environment.apiUrl}/pharmacies`;

  listPending(page = 0, size = DEFAULT_PAGE_SIZE): Observable<PagedResponse<PendingUser>> {
    return this.http.get<PagedResponse<PendingUser>>(`${this.base}/pending`, {
      params: { page, size },
    });
  }

  listUsers(params: UserListParams): Observable<PagedResponse<AuthUser>> {
    return this.http.get<PagedResponse<AuthUser>>(`${this.base}`, {
      params: toHttpParams(params),
    });
  }

  stats(): Observable<AdminUserStats> {
    return this.http.get<AdminUserStats>(`${this.base}/stats`);
  }

  getUser(id: string): Observable<AuthUser> {
    return this.http.get<AuthUser>(`${this.base}/${id}`);
  }

  createUser(req: CreateUserRequest): Observable<AuthUser> {
    return this.http.post<AuthUser>(`${this.base}`, req);
  }

  updateUser(id: string, req: UpdateUserRequest): Observable<AuthUser> {
    return this.http.put<AuthUser>(`${this.base}/${id}`, req);
  }

  deleteUsers(id: string): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  rejectUser(id: string): Observable<void> {
    return this.http.post<void>(`${this.base}/${id}/reject`, {});
  }

  approve(userId: string, request: ApproveRequest): Observable<AuthUser> {
    return this.http.post<AuthUser>(`${this.base}/${userId}/approve`, request);
  }

  listPharmacies(): Observable<Pharmacie[]> {
    return this.http.get<Pharmacie[]>(this.pharmacies);
  }
}