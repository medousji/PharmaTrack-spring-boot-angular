import { Component, OnDestroy, OnInit, inject } from '@angular/core';
import { DatePipe, DecimalPipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { Subscription, interval } from 'rxjs';

import { problemDetail } from '@core/http/http-error.util';
import { AuthService } from '@core/services/auth.service';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  commandeStatutLabel,
  commandeStatutTone,
} from '@features/fournisseur/models/fournisseur.models';
import type { CommandeThread, MessageResponse } from '@features/chat/models/chat.models';
import { ChatService } from '@features/chat/services/chat.service';

@Component({
  selector: 'app-chat-commande',
  standalone: true,
  imports: [DatePipe, DecimalPipe, RouterLink, FormsModule, StatusPillComponent],
  templateUrl: './chat-commande.component.html',
  styleUrl: './chat-commande.component.css',
})
export class ChatCommandeComponent implements OnInit, OnDestroy {
  private readonly service = inject(ChatService);
  private readonly route = inject(ActivatedRoute);
  private readonly auth = inject(AuthService);

  readonly statutLabel = commandeStatutLabel;
  readonly statutTone = commandeStatutTone;

  thread: CommandeThread | null = null;
  error = '';
  sendError = '';
  draft = '';
  sending = false;

  myId = '';

  private timer: Subscription | null = null;

  ngOnInit(): void {
    this.myId = this.auth.currentUser()?.id ?? '';
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) {
      this.error = 'Commande introuvable.';
      return;
    }
    this.reload(id);
    this.timer = interval(10000).subscribe(() => this.reload(id));
  }

  ngOnDestroy(): void {
    this.timer?.unsubscribe();
  }

  reload(id: string): void {
    this.service.commandeThread(id).subscribe({
      next: (res) => {
        this.thread = res;
        this.error = '';
      },
      error: (err: unknown) =>
        (this.error = problemDetail(err, 'Erreur de chargement de la discussion.')),
    });
  }

  send(id: string): void {
    const message = this.draft.trim();
    if (!message || this.sending) {
      return;
    }
    this.sending = true;
    this.sendError = '';
    this.service.envoyer({ message, commandeId: id }).subscribe({
      next: (sent: MessageResponse) => {
        this.draft = '';
        this.sending = false;
        if (this.thread) {
          this.thread = { ...this.thread, messages: [...this.thread.messages, sent] };
        }
      },
      error: (err: unknown) => {
        this.sending = false;
        this.sendError = problemDetail(err, "Erreur lors de l'envoi du message.");
      },
    });
  }

  onEnter(event: Event, id: string): void {
    if (!(event as KeyboardEvent).shiftKey) {
      this.send(id);
    }
  }

  mine(message: MessageResponse): boolean {
    return message.expediteurId === this.myId;
  }

  dateLivraison(d: string | undefined): string {
    return d ?? '—';
  }
}