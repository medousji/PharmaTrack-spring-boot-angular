import { Component, ElementRef, OnInit, ViewChild, inject } from '@angular/core';
import { DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { problemDetail } from '@core/http/http-error.util';
import { AuthService } from '@core/services/auth.service';
import type {
  ChatbotHistoryItem,
  ChatbotResponseData,
} from '@features/assistant/models/chatbot.models';
import { ChatbotService } from '@features/assistant/services/chatbot.service';

interface ChatItem {
  question: string;
  reponse: string;
  createdAt: string;
}

const SUGGESTIONS = [
  { label: '📦 Stock', message: 'Stock de Paracétamol' },
  { label: '⚠️ Stocks faibles', message: 'Stocks faibles' },
  { label: '📋 Recommandations', message: 'Recommandations' },
  { label: '🛒 Commander', message: 'Je veux commander' },
  { label: '🚨 Alertes', message: 'Alertes' },
  { label: '📊 Statistiques', message: 'Statistiques' },
  { label: '💡 Aide', message: 'Aide' },
] as const;

@Component({
  selector: 'app-assistant',
  standalone: true,
  imports: [DatePipe, FormsModule],
  templateUrl: './assistant.component.html',
  styleUrl: './assistant.component.css',
})
export class AssistantComponent implements OnInit {
  private readonly service = inject(ChatbotService);
  private readonly auth = inject(AuthService);

  @ViewChild('chatBox') private readonly chatBox!: ElementRef<HTMLElement>;

  readonly suggestions = SUGGESTIONS;

  items: ChatItem[] = [];
  typing = false;
  draft = '';
  error = '';
  today = new Date().toLocaleDateString('fr-FR');

  greeting =
    '👋 <strong>Bonjour !</strong><br><br>' +
    "Je suis votre assistant Pharma Track. Je peux vous aider à :<br><br>" +
    '📦 Consulter un stock<br>' +
    '🛒 Passer une commande<br>' +
    '📋 Voir les recommandations<br>' +
    '🚨 Voir les alertes<br>' +
    '📊 Afficher les statistiques<br><br>' +
    '<strong>Exemples :</strong><br>' +
    '• "Stock de Paracétamol"<br>' +
    '• "Commander 100 Amoxicilline"<br>' +
    '• "Recommandations"<br>' +
    '• "Alertes"<br>' +
    '• "Aide"<br><br>' +
    'Comment puis-je vous aider aujourd\'hui ?';

  userName = '';

  ngOnInit(): void {
    this.userName = this.auth.currentUser()?.name ?? '';
    this.service.historique().subscribe({
      next: (history: ChatbotHistoryItem[]) => {
        this.items = [...history]
          .reverse()
          .map((h) => ({ question: h.question, reponse: h.reponse, createdAt: h.createdAt }));
        this.scrollToBottom();
      },
      error: (err: unknown) =>
        (this.error = problemDetail(err, 'Erreur de chargement de l\u2019historique.')),
    });
  }

  ask(message: string): void {
    this.draft = message;
    this.send();
  }

  send(): void {
    const message = this.draft.trim();
    if (!message || this.typing) {
      return;
    }
    this.draft = '';
    this.error = '';
    this.typing = true;
    this.items = [...this.items, { question: message, reponse: '…', createdAt: new Date().toISOString() }];
    this.scrollToBottom();

    this.service.message(message).subscribe({
      next: (res: ChatbotResponseData) => {
        this.typing = false;
        this.items = this.items.map((item, i) =>
          i === this.items.length - 1 ? { ...item, reponse: res.reponse } : item,
        );
        this.scrollToBottom();
      },
      error: (err: unknown) => {
        this.typing = false;
        const detail = problemDetail(err, 'Une erreur est survenue. Veuillez réessayer.');
        this.items = this.items.map((item, i) =>
          i === this.items.length - 1
            ? { ...item, reponse: `❌ Désolé, une erreur s'est produite. ${detail}` }
            : item,
        );
      },
    });
  }

  format(text: string): string {
    const escaped = text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
    return escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
  }

  imprimer(): void {
    window.print();
  }

  private scrollToBottom(): void {
    setTimeout(() => {
      this.chatBox?.nativeElement.scrollTo(0, this.chatBox.nativeElement.scrollHeight);
    }, 0);
  }
}