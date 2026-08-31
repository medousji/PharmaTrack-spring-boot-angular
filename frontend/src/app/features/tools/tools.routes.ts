import { Routes } from '@angular/router';

export const TOOLS_ROUTES: Routes = [
  { path: 'scan', loadComponent: () => import('./pages/scan/scan.component').then((m) => m.ScanComponent) },
  { path: 'conformite', loadComponent: () => import('./pages/conformite/conformite.component').then((m) => m.ConformiteComponent) },
  { path: 'predictions', loadComponent: () => import('./pages/predictions/predictions.component').then((m) => m.PredictionsComponent) },
  { path: 'profile', loadComponent: () => import('./pages/profile/profile.component').then((m) => m.ProfileComponent) },
];