import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { DEFAULT_PAGE_SIZE } from '@core/constants/app.constants';
import { toHttpParams } from '@core/http/http-params.util';
import type { PagedResponse } from '@core/models/api.models';
import { environment } from '@env/environment';
import type {
  CommandeResponse,
  FournisseurDashboard,
  FournisseurMedicamentResponse,
  UpdatePrixItem,
} from '../models/fournisseur.models';

export interface FournisseurCommandesParams {
  statut?: string;
  page?: number;
  size?: number;
}

@Injectable({ providedIn: 'root' })
export class FournisseurService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl}/fournisseur`;

  dashboard(): Observable<FournisseurDashboard> {
    return this.http.get<FournisseurDashboard>(`${this.base}/dashboard`);
  }

  commandes(params: FournisseurCommandesParams): Observable<PagedResponse<CommandeResponse>> {
    return this.http.get<PagedResponse<CommandeResponse>>(`${this.base}/commandes`, {
      params: toHttpParams(params),
    });
  }

  getCommande(id: string): Observable<CommandeResponse> {
    return this.http.get<CommandeResponse>(`${environment.apiUrl}/commandes/${id}`);
  }

  expedier(id: string): Observable<CommandeResponse> {
    return this.http.post<CommandeResponse>(`${this.base}/commandes/${id}/expedier`, {});
  }

  prix(page = 0, size = DEFAULT_PAGE_SIZE): Observable<PagedResponse<FournisseurMedicamentResponse>> {
    return this.http.get<PagedResponse<FournisseurMedicamentResponse>>(`${this.base}/prix`, {
      params: { page, size },
    });
  }

  updatePrix(items: UpdatePrixItem[]): Observable<void> {
    return this.http.put<void>(`${this.base}/prix`, { prix: items });
  }
}