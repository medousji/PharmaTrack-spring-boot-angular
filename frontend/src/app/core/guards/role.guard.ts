import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';

import { UNAUTHORIZED_PATH } from '@core/constants/app.constants';
import type { UserRole } from '@core/models/auth.models';
import { AuthService } from '@core/services/auth.service';

/** Garde de route exigeant que l'utilisateur courant possède au moins un des rôles. */
export const roleGuard =
  (allowedRoles: readonly UserRole[]): CanActivateFn =>
  () => {
    const auth = inject(AuthService);
    const router = inject(Router);
    const user = auth.currentUser();

    if (user && allowedRoles.includes(user.role)) {
      return true;
    }
    void router.navigate([UNAUTHORIZED_PATH]);
    return false;
  };