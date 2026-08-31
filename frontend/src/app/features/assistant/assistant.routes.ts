import { Routes } from '@angular/router';

import { ROLES } from '@core/constants/app.constants';
import { roleGuard } from '@core/guards/role.guard';

import { AssistantComponent } from './pages/assistant/assistant.component';

export const ASSISTANT_ROUTES: Routes = [
  {
    path: 'assistant',
    component: AssistantComponent,
    canActivate: [roleGuard([ROLES.ADMIN, ROLES.PHARMACIEN])],
  },
];