import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, of, throwError } from 'rxjs';
import { catchError, tap } from 'rxjs/operators';

import { STORAGE_KEYS } from '@core/constants/app.constants';
import {
  AuthUser,
  LoginRequest,
  RefreshRequest,
  RegisterRequest,
  TokenResponse,
  UserRole,
} from '@core/models/auth.models';
import { environment } from '@env/environment';

/**
 * Session d'authentification : token pair + utilisateur courant.
 * L'état est conservé dans localStorage et exposé via `user$`.
 */
@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly http = inject(HttpClient);

  private readonly currentUser$ = new BehaviorSubject<AuthUser | null>(
    this.loadStoredUser(),
  );

  readonly user$: Observable<AuthUser | null> = this.currentUser$.asObservable();

  login(req: LoginRequest): Observable<TokenResponse> {
    return this.http.post<TokenResponse>(`${environment.apiUrl}/auth/login`, req).pipe(
      tap((res) => this.setSession(res)),
    );
  }

  register(req: RegisterRequest): Observable<AuthUser> {
    return this.http.post<AuthUser>(`${environment.apiUrl}/auth/register`, req);
  }

  refresh(refreshToken: string): Observable<TokenResponse> {
    const body: RefreshRequest = { refreshToken };
    return this.http.post<TokenResponse>(`${environment.apiUrl}/auth/refresh`, body);
  }

  logout(): Observable<void> {
    const refreshToken = this.getRefreshToken();
    if (!refreshToken) {
      this.resetSession();
      return of(undefined);
    }
    return this.http
      .post<void>(`${environment.apiUrl}/auth/logout`, { refreshToken })
      .pipe(
        catchError(() => of(undefined)),
        tap(() => this.resetSession()),
      );
  }

  me(): Observable<AuthUser> {
    return this.http.get<AuthUser>(`${environment.apiUrl}/auth/me`).pipe(
      tap((user) => {
        this.currentUser$.next(user);
        this.persistStoredUser(user);
      }),
      catchError((err) => {
        this.resetSession();
        return throwError(() => err);
      }),
    );
  }

  /** Persist un couple de tokens et diffuse l'utilisateur. */
  setSession(res: TokenResponse): void {
    localStorage.setItem(STORAGE_KEYS.accessToken, res.accessToken);
    localStorage.setItem(STORAGE_KEYS.refreshToken, res.refreshToken);
    this.currentUser$.next(res.user);
    this.persistStoredUser(res.user);
  }

  /** Remplace le couple de tokens après rotation (garde la session). */
  updateTokens(accessToken: string, refreshToken: string): void {
    localStorage.setItem(STORAGE_KEYS.accessToken, accessToken);
    localStorage.setItem(STORAGE_KEYS.refreshToken, refreshToken);
  }

  getAccessToken(): string | null {
    return localStorage.getItem(STORAGE_KEYS.accessToken);
  }

  getRefreshToken(): string | null {
    return localStorage.getItem(STORAGE_KEYS.refreshToken);
  }

  currentUser(): AuthUser | null {
    return this.currentUser$.value;
  }

  isAuthenticated(): boolean {
    return this.currentUser() !== null && this.getAccessToken() !== null;
  }

  hasRole(role: UserRole): boolean {
    return this.currentUser()?.role === role;
  }

  clearSession(): void {
    this.resetSession();
  }

  private resetSession(): void {
    localStorage.removeItem(STORAGE_KEYS.accessToken);
    localStorage.removeItem(STORAGE_KEYS.refreshToken);
    localStorage.removeItem(STORAGE_KEYS.user);
    this.currentUser$.next(null);
  }

  private loadStoredUser(): AuthUser | null {
    try {
      const raw = localStorage.getItem(STORAGE_KEYS.user);
      return raw ? (JSON.parse(raw) as AuthUser) : null;
    } catch {
      return null;
    }
  }

  private persistStoredUser(user: AuthUser): void {
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user));
  }
}