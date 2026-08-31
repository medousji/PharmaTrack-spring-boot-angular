import { Routes } from '@angular/router';

import { ROLES } from '@core/constants/app.constants';
import { roleGuard } from '@core/guards/role.guard';

import { CommandeCreerComponent } from './pages/commande-creer/commande-creer.component';
import { CommandeSelectionComponent } from './pages/commande-selection/commande-selection.component';

export const COMMANDES_ROUTES: Routes = [
  {
    path: 'commandes',
    component: CommandeSelectionComponent,
    canActivate: [roleGuard([ROLES.PHARMACIEN])],
  },
  {
    path: 'commandes/creer/:medicamentId',
    component: CommandeCreerComponent,
    canActivate: [roleGuard([ROLES.PHARMACIEN])],
  },
];