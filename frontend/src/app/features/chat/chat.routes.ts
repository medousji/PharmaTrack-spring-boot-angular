import { Routes } from '@angular/router';

import { ROLES } from '@core/constants/app.constants';
import { roleGuard } from '@core/guards/role.guard';

import { ChatComponent } from './pages/chat/chat.component';
import { ChatCommandeComponent } from './pages/chat-commande/chat-commande.component';
import { ChatConversationComponent } from './pages/chat-conversation/chat-conversation.component';

const CHAT_ROLES = [ROLES.ADMIN, ROLES.PHARMACIEN, ROLES.FOURNISSEUR] as const;

export const CHAT_ROUTES: Routes = [
  {
    path: 'chat',
    component: ChatComponent,
    canActivate: [roleGuard(CHAT_ROLES)],
  },
  {
    path: 'chat/commande/:id',
    component: ChatCommandeComponent,
    canActivate: [roleGuard(CHAT_ROLES)],
  },
  {
    path: 'chat/conversation/:contactId',
    component: ChatConversationComponent,
    canActivate: [roleGuard(CHAT_ROLES)],
  },
];