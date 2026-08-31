import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { environment } from '@env/environment';
import type {
  ChatOverview,
  CommandeThread,
  ConversationThread,
  EnvoyerMessageRequest,
  MessageResponse,
} from '@features/chat/models/chat.models';

@Injectable({ providedIn: 'root' })
export class ChatService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl}/chat`;

  overview(): Observable<ChatOverview> {
    return this.http.get<ChatOverview>(`${this.base}/overview`);
  }

  commandeThread(commandeId: string): Observable<CommandeThread> {
    return this.http.get<CommandeThread>(`${this.base}/commandes/${commandeId}`);
  }

  conversationThread(contactId: string): Observable<ConversationThread> {
    return this.http.get<ConversationThread>(`${this.base}/conversations/${contactId}`);
  }

  envoyer(req: EnvoyerMessageRequest): Observable<MessageResponse> {
    return this.http.post<MessageResponse>(`${this.base}/messages`, req);
  }
}