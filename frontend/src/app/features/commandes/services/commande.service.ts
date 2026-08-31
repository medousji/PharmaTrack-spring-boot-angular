import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { toHttpParams } from '@core/http/http-params.util';
import type { PagedResponse } from '@core/models/api.models';
import { environment } from '@env/environment';
import type {
  CommandeResponse,
  CommandeResult,
  DisponibiliteResponse,
  FournisseurMedicamentResponse,
  MedicamentSelectionResponse,
} from '@features/fournisseur/models/fournisseur.models';

export interface MedicamentSelectionParams {
  search?: string;
  page?: number;
  size?: number;
}

@Injectable({ providedIn: 'root' })
export class CommandeService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl}/commandes`;

  medicaments(params: MedicamentSelectionParams): Observable<PagedResponse<MedicamentSelectionResponse>> {
    return this.http.get<PagedResponse<MedicamentSelectionResponse>>(`${this.base}/medicaments`, {
      params: toHttpParams(params),
    });
  }

  fournisseurs(medicamentId: string): Observable<FournisseurMedicamentResponse[]> {
    return this.http.get<FournisseurMedicamentResponse[]>(
      `${this.base}/medicaments/${medicamentId}/fournisseurs`,
    );
  }

  verifierDisponibilite(
    fournisseurMedicamentId: string,
    quantite: number,
  ): Observable<DisponibiliteResponse> {
    return this.http.post<DisponibiliteResponse>(`${this.base}/verifier-disponibilite`, {
      fournisseurMedicamentId,
      quantite,
    });
  }

  passer(fournisseurMedicamentId: string, quantite: number): Observable<CommandeResult> {
    return this.http.post<CommandeResult>(`${this.base}/passer`, {
      fournisseurMedicamentId,
      quantite,
    });
  }

  detail(id: string): Observable<CommandeResponse> {
    return this.http.get<CommandeResponse>(`${this.base}/${id}`);
  }
}