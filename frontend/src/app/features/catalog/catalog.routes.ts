import { Routes } from '@angular/router';

import { WRITE_ROLES } from '@core/constants/app.constants';
import { roleGuard } from '@core/guards/role.guard';

import { MedicamentListComponent } from './pages/medicament-list/medicament-list.component';
import { MedicamentFormComponent } from './pages/medicament-form/medicament-form.component';
import { MedicamentDetailComponent } from './pages/medicament-detail/medicament-detail.component';
import { LotFormComponent } from './pages/lot-form/lot-form.component';
import { MouvementsComponent } from './pages/mouvements/mouvements.component';
import { LotsComponent } from './pages/lots/lots.component';

const canWrite = roleGuard([...WRITE_ROLES]);

export const CATALOG_ROUTES: Routes = [
  { path: 'medicaments', component: MedicamentListComponent, pathMatch: 'full' },
  {
    path: 'medicaments/nouveau',
    component: MedicamentFormComponent,
    canActivate: [canWrite],
  },
  { path: 'medicaments/:id', component: MedicamentDetailComponent },
  {
    path: 'medicaments/:id/modifier',
    component: MedicamentFormComponent,
    canActivate: [canWrite],
  },
  {
    path: 'medicaments/:id/lots/nouveau',
    component: LotFormComponent,
    canActivate: [canWrite],
  },
  {
    path: 'lots/nouveau',
    component: LotFormComponent,
    canActivate: [canWrite],
  },
  { path: 'mouvements', component: MouvementsComponent },
  { path: 'lots', component: LotsComponent },
];