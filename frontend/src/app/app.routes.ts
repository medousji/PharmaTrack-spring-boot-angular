import { Routes } from '@angular/router';

import { authGuard } from './core/guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    pathMatch: 'full',
    redirectTo: '/login',
  },
  {
    path: '',
    loadChildren: () => import('@features/auth/auth.routes').then((m) => m.AUTH_ROUTES),
  },
  {
    path: 'dashboard',
    canActivate: [authGuard],
    loadChildren: () =>
      import('@features/dashboard/dashboard.routes').then((m) => m.DASHBOARD_ROUTES),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadChildren: () => import('@features/users/users.routes').then((m) => m.USERS_ROUTES),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadChildren: () => import('@features/catalog/catalog.routes').then((m) => m.CATALOG_ROUTES),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadChildren: () => import('@features/alertes/alertes.routes').then((m) => m.ALERTES_ROUTES),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadChildren: () => import('@features/fournisseur/fournisseur.routes').then((m) => m.FOURNISSEUR_ROUTES),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadChildren: () => import('@features/commandes/commande.routes').then((m) => m.COMMANDES_ROUTES),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadChildren: () => import('@features/chat/chat.routes').then((m) => m.CHAT_ROUTES),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadChildren: () => import('@features/assistant/assistant.routes').then((m) => m.ASSISTANT_ROUTES),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadChildren: () => import('@features/tools/tools.routes').then((m) => m.TOOLS_ROUTES),
  },
  {
    path: 'unauthorized',
    loadComponent: () =>
      import('@features/unauthorized/unauthorized.component').then(
        (m) => m.UnauthorizedComponent,
      ),
  },
  { path: '**', redirectTo: '' },
];