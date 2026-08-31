import { inject } from '@angular/core';
import {
  HttpErrorResponse,
  HttpEvent,
  HttpHandlerFn,
  HttpInterceptorFn,
  HttpRequest,
} from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, of, Subject, throwError } from 'rxjs';
import { catchError, filter, finalize, switchMap, take } from 'rxjs/operators';

import { AUTH_PATH, SESSION_PATH } from '@core/constants/app.constants';
import { AuthService } from '@core/services/auth.service';

/** File d'attente partagée : tous les appels 401 en vol attendent un seul refresh. */
const refreshResult = new Subject<boolean>();
let refreshInProgress = false;

export const authInterceptor: HttpInterceptorFn = (
  req: HttpRequest<unknown>,
  next: HttpHandlerFn,
): Observable<HttpEvent<unknown>> => {
  const auth = inject(AuthService);
  const router = inject(Router);

  // Seuls login/register/refresh restent publics ; /auth/me et /auth/logout
  // exigent un jeton et doivent pouvoir déclencher un refresh sur 401.
  const isPublic =
    req.url.includes(AUTH_PATH) &&
    !/\/auth\/(me|logout)$/.test(req.url);
  const token = auth.getAccessToken();

  let request = req;
  if (token && !isPublic && !req.headers.has('Authorization')) {
    request = req.clone({ setHeaders: { Authorization: `Bearer ${token}` } });
  }

  return next(request).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status !== 401 || isPublic) {
        return throwError(() => error);
      }
      if (!auth.getRefreshToken()) {
        goToLogin(auth, router);
        return throwError(() => error);
      }
      return retryWithRefresh(auth, router).pipe(
        switchMap((refreshed) => {
          if (!refreshed) {
            return throwError(() => error);
          }
          const retried = req.clone({
            setHeaders: { Authorization: `Bearer ${auth.getAccessToken() ?? ''}` },
          });
          return next(retried);
        }),
      );
    }),
  );
};

/** Rafraîchit le couple de tokens une seule fois ; émet true si la session est récupérée. */
function retryWithRefresh(auth: AuthService, router: Router): Observable<boolean> {
  if (refreshInProgress) {
    // Un autre appel rafraîchit déjà la session : on attend le résultat partagé.
    let recovered = false;
    return refreshResult.pipe(
      filter((ok) => {
        recovered = ok;
        return true;
      }),
      take(1),
      switchMap(() => {
        if (recovered) return of(true);
        goToLogin(auth, router);
        return of(false);
      }),
    );
  }

  refreshInProgress = true;
  return auth.refresh(auth.getRefreshToken() ?? '').pipe(
    switchMap((res) => {
      auth.updateTokens(res.accessToken, res.refreshToken);
      refreshResult.next(true);
      return of(true);
    }),
    catchError(() => {
      refreshResult.next(false);
      goToLogin(auth, router);
      return of(false);
    }),
    finalize(() => {
      refreshInProgress = false;
    }),
  );
}

function goToLogin(auth: AuthService, router: Router): void {
  auth.clearSession();
  void router.navigate([SESSION_PATH]);
}