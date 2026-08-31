import { Component, OnDestroy, OnInit, inject } from '@angular/core';
import { DatePipe, DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { Subscription, interval } from 'rxjs';

import { problemDetail } from '@core/http/http-error.util';
import { StatusPillComponent } from '@shared/components/status-pill/status-pill.component';
import {
  commandeStatutLabel,
  commandeStatutTone,
} from '@features/fournisseur/models/fournisseur.models';
import type { ChatOverview } from '@features/chat/models/chat.models';
import { ChatService } from '@features/chat/services/chat.service';

@Component({
  selector: 'app-chat',
  standalone: true,
  imports: [DatePipe, DecimalPipe, RouterLink, StatusPillComponent],
  templateUrl: './chat.component.html',
  styleUrl: './chat.component.css',
})
export class ChatComponent implements OnInit, OnDestroy {
  private readonly service = inject(ChatService);

  readonly statutLabel = commandeStatutLabel;
  readonly statutTone = commandeStatutTone;

  data: ChatOverview | null = null;
  error = '';
  activeTab: 'commandes' | 'conversations' = 'commandes';

  private timer: Subscription | null = null;

  ngOnInit(): void {
    this.reload();
    this.timer = interval(10000).subscribe(() => this.reload());
  }

  ngOnDestroy(): void {
    this.timer?.unsubscribe();
  }

  setTab(tab: 'commandes' | 'conversations'): void {
    this.activeTab = tab;
  }

  reload(): void {
    this.service.overview().subscribe({
      next: (res) => {
        this.data = res;
        this.error = '';
      },
      error: (err: unknown) =>
        (this.error = problemDetail(err, 'Erreur de chargement de la messagerie.')),
    });
  }
}