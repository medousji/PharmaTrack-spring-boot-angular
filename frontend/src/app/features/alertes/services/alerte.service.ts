import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, forkJoin } from 'rxjs';
import { map } from 'rxjs/operators';

import type { PagedResponse } from '@core/models/api.models';
import { environment } from '@env/environment';
import type {
  AlerteEvaluationSummary,
  AlerteNiveau,
  AlerteResponse,
  AlerteType,
} from '@features/alertes/models/alerte.models';

export interface AlerteListParams {
  type?: AlerteType;
  niveau?: AlerteNiveau;
  estLue?: boolean;
  page?: number;
  size?: number;
}

@Injectable({ providedIn: 'root' })
export class AlerteService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl}/alertes`;

  list(params: AlerteListParams): Observable<PagedResponse<AlerteResponse>> {
    return this.http.get<PagedResponse<AlerteResponse>>(`${this.base}`, {
      params: this.toParams(params),
    });
  }

  /** Compte des alertes non lues par niveau (une requête par niveau, agrégée). */
  countUnreadByLevel(): Observable<{ niveau: AlerteNiveau; total: number }[]> {
    const levels: readonly AlerteNiveau[] = ['critique', 'eleve', 'moyen', 'faible'];
    return forkJoin(
      levels.map((niveau) =>
        this.list({ niveau, estLue: false, page: 0, size: 1 }).pipe(
          map((res) => ({ niveau, total: res.totalElements })),
        ),
      ),
    );
  }

  reEvaluate(): Observable<AlerteEvaluationSummary> {
    return this.http.post<AlerteEvaluationSummary>(`${this.base}`, {});
  }

  markRead(id: string): Observable<AlerteResponse> {
    return this.http.post<AlerteResponse>(`${this.base}/${id}/lire`, {});
  }

  resolve(id: string): Observable<AlerteResponse> {
    return this.http.post<AlerteResponse>(`${this.base}/${id}/resoudre`, {});
  }

  private toParams(params: AlerteListParams): HttpParams {
    let p = new HttpParams();
    for (const [k, v] of Object.entries(params)) {
      if (v !== undefined && v !== null && v !== '') {
        p = p.set(k, String(v));
      }
    }
    return p;
  }
}