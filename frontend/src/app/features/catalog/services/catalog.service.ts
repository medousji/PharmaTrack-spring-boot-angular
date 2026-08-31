import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { toHttpParams } from '@core/http/http-params.util';
import type { PagedResponse } from '@core/models/api.models';
import { environment } from '@env/environment';
import {
  LotCreateRequest,
  LotResponse,
  LotStatut,
  MedicamentCreateRequest,
  MedicamentDetailResponse,
  MedicamentResponse,
  MedicamentStatut,
  MedicamentUpdateRequest,
  MouvementResponse,
  MouvementType,
  StockAdjustmentRequest,
  StockAdjustmentResponse,
} from '../models/catalog.models';

export interface MedicamentListParams {
  search?: string;
  classeTherapeutique?: string;
  statut?: MedicamentStatut;
  enRupture?: boolean;
  prochePeremption?: boolean;
  page?: number;
  size?: number;
}

export interface LotListParams {
  statut?: LotStatut;
  page?: number;
  size?: number;
}

export interface MouvementListParams {
  lotId?: string;
  pharmacieId?: string;
  type?: MouvementType;
  from?: string;
  to?: string;
  page?: number;
  size?: number;
}

@Injectable({ providedIn: 'root' })
export class CatalogService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl}`;

  listMedicaments(
    params: MedicamentListParams,
  ): Observable<PagedResponse<MedicamentDetailResponse>> {
    return this.http.get<PagedResponse<MedicamentDetailResponse>>(
      `${this.base}/medicaments`,
      { params: toHttpParams(params) },
    );
  }

  getMedicament(id: string): Observable<MedicamentDetailResponse> {
    return this.http.get<MedicamentDetailResponse>(`${this.base}/medicaments/${id}`);
  }

  createMedicament(req: MedicamentCreateRequest): Observable<MedicamentResponse> {
    return this.http.post<MedicamentResponse>(`${this.base}/medicaments`, req);
  }

  updateMedicament(id: string, req: MedicamentUpdateRequest): Observable<MedicamentResponse> {
    return this.http.put<MedicamentResponse>(`${this.base}/medicaments/${id}`, req);
  }

  retireMedicament(id: string): Observable<void> {
    return this.http.delete<void>(`${this.base}/medicaments/${id}`);
  }

  listLotsByMedicament(
    id: string,
    params: LotListParams,
  ): Observable<PagedResponse<LotResponse>> {
    return this.http.get<PagedResponse<LotResponse>>(
      `${this.base}/medicaments/${id}/lots`,
      { params: toHttpParams(params) },
    );
  }

  listLots(params: LotListParams): Observable<PagedResponse<LotResponse>> {
    return this.http.get<PagedResponse<LotResponse>>(`${this.base}/lots`, {
      params: toHttpParams(params),
    });
  }

  nextLotToDispense(id: string): Observable<LotResponse> {
    return this.http.get<LotResponse>(`${this.base}/medicaments/${id}/prochain-lot`);
  }

  createLot(req: LotCreateRequest): Observable<LotResponse> {
    return this.http.post<LotResponse>(`${this.base}/lots`, req);
  }

  adjustStock(
    lotId: string,
    req: StockAdjustmentRequest,
  ): Observable<StockAdjustmentResponse> {
    return this.http.post<StockAdjustmentResponse>(
      `${this.base}/lots/${lotId}/adjust-stock`,
      req,
    );
  }

  listMouvements(
    params: MouvementListParams,
  ): Observable<PagedResponse<MouvementResponse>> {
    return this.http.get<PagedResponse<MouvementResponse>>(`${this.base}/mouvements`, {
      params: toHttpParams(params),
    });
  }
}