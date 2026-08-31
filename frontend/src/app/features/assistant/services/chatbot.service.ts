import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { environment } from '@env/environment';
import type {
  ChatbotHistoryItem,
  ChatbotResponseData,
} from '@features/assistant/models/chatbot.models';

@Injectable({ providedIn: 'root' })
export class ChatbotService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl}/assistant`;

  historique(): Observable<ChatbotHistoryItem[]> {
    return this.http.get<ChatbotHistoryItem[]>(`${this.base}/historique`);
  }

  message(message: string): Observable<ChatbotResponseData> {
    return this.http.post<ChatbotResponseData>(`${this.base}/message`, { message });
  }
}