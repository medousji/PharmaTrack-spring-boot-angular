import { Routes } from '@angular/router';

import { ROLES } from '@core/constants/app.constants';
import { roleGuard } from '@core/guards/role.guard';

import { FournisseurCommandeDetailComponent } from './pages/fournisseur-commande-detail/fournisseur-commande-detail.component';
import { FournisseurCommandesComponent } from './pages/fournisseur-commandes/fournisseur-commandes.component';
import { FournisseurDashboardComponent } from './pages/fournisseur-dashboard/fournisseur-dashboard.component';
import { FournisseurPrixComponent } from './pages/fournisseur-prix/fournisseur-prix.component';

export const FOURNISSEUR_ROUTES: Routes = [
  {
    path: 'fournisseur',
    component: FournisseurDashboardComponent,
    canActivate: [roleGuard([ROLES.FOURNISSEUR])],
  },
  {
    path: 'fournisseur/commandes',
    component: FournisseurCommandesComponent,
    canActivate: [roleGuard([ROLES.FOURNISSEUR])],
  },
  {
    path: 'fournisseur/commandes/:id',
    component: FournisseurCommandeDetailComponent,
    canActivate: [roleGuard([ROLES.FOURNISSEUR])],
  },
  {
    path: 'fournisseur/prix',
    component: FournisseurPrixComponent,
    canActivate: [roleGuard([ROLES.FOURNISSEUR])],
  },
];