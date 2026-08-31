import { Component, OnDestroy, OnInit, inject } from '@angular/core';
import { DatePipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { Subscription, interval } from 'rxjs';

import { problemDetail } from '@core/http/http-error.util';
import { AuthService } from '@core/services/auth.service';
import { roleLabel, type ConversationThread, type MessageResponse } from '@features/chat/models/chat.models';
import { ChatService } from '@features/chat/services/chat.service';

@Component({
  selector: 'app-chat-conversation',
  standalone: true,
  imports: [DatePipe, RouterLink, FormsModule],
  templateUrl: './chat-conversation.component.html',
  styleUrl: './chat-conversation.component.css',
})
export class ChatConversationComponent implements OnInit, OnDestroy {
  private readonly service = inject(ChatService);
  private readonly route = inject(ActivatedRoute);
  private readonly auth = inject(AuthService);

  readonly roleLabel = roleLabel;

  thread: ConversationThread | null = null;
  error = '';
  sendError = '';
  draft = '';
  sending = false;

  myId = '';
  contactId = '';

  private timer: Subscription | null = null;

  ngOnInit(): void {
    this.myId = this.auth.currentUser()?.id ?? '';
    const contactId = this.route.snapshot.paramMap.get('contactId');
    if (!contactId) {
      this.error = 'Contact introuvable.';
      return;
    }
    this.contactId = contactId;
    this.reload();
    this.timer = interval(10000).subscribe(() => this.reload());
  }

  ngOnDestroy(): void {
    this.timer?.unsubscribe();
  }

  reload(): void {
    this.service.conversationThread(this.contactId).subscribe({
      next: (res) => {
        this.thread = res;
        this.error = '';
      },
      error: (err: unknown) =>
        (this.error = problemDetail(err, 'Erreur de chargement de la discussion.')),
    });
  }

  send(): void {
    const message = this.draft.trim();
    if (!message || this.sending) {
      return;
    }
    this.sending = true;
    this.sendError = '';
    this.service.envoyer({ message, destinataireId: this.contactId }).subscribe({
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

  onEnter(event: Event): void {
    if (!(event as KeyboardEvent).shiftKey) {
      this.send();
    }
  }

  mine(message: MessageResponse): boolean {
    return message.expediteurId === this.myId;
  }
}