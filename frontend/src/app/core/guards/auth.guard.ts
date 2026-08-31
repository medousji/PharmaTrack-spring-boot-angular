import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';

import { SESSION_PATH } from '@core/constants/app.constants';
import { AuthService } from '@core/services/auth.service';

export const authGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router = inject(Router);

  if (auth.isAuthenticated()) {
    return true;
  }
  void router.navigate([SESSION_PATH]);
  return false;
};