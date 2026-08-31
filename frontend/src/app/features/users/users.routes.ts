import { Routes } from '@angular/router';

import { ROLES } from '@core/constants/app.constants';
import { roleGuard } from '@core/guards/role.guard';

import { AdminStatsComponent } from './pages/admin-stats/admin-stats.component';
import { PendingUsersComponent } from './pages/pending-users/pending-users.component';
import { UserFormComponent } from './pages/user-form/user-form.component';
import { UsersListComponent } from './pages/users-list/users-list.component';

export const USERS_ROUTES: Routes = [
  {
    path: 'admin/users',
    component: UsersListComponent,
    canActivate: [roleGuard([ROLES.ADMIN])],
  },
  {
    path: 'admin/users/stats',
    component: AdminStatsComponent,
    canActivate: [roleGuard([ROLES.ADMIN])],
  },
  {
    path: 'admin/dashboard',
    component: AdminStatsComponent,
    canActivate: [roleGuard([ROLES.ADMIN])],
  },
  {
    path: 'admin/users/nouveau',
    component: UserFormComponent,
    canActivate: [roleGuard([ROLES.ADMIN])],
  },
  {
    path: 'admin/users/:id/modifier',
    component: UserFormComponent,
    canActivate: [roleGuard([ROLES.ADMIN])],
  },
  {
    path: 'admin/users/pending',
    component: PendingUsersComponent,
    canActivate: [roleGuard([ROLES.ADMIN])],
  },
];