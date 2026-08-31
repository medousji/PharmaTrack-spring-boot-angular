import type { UserRole } from '@core/models/auth.models';

export const ROLES: Readonly<Record<'ADMIN' | 'PHARMACIEN' | 'FOURNISSEUR' | 'VISITEUR', UserRole>> = {
  ADMIN: 'admin',
  PHARMACIEN: 'pharmacien',
  FOURNISSEUR: 'fournisseur',
  VISITEUR: 'visiteur',
} as const;

/** Rôles autorisés à écrire dans le catalogue/le stock. */
export const WRITE_ROLES: readonly UserRole[] = [ROLES.ADMIN, ROLES.PHARMACIEN];

export const STORAGE_KEYS = {
  accessToken: 'pharmatrack_access',
  refreshToken: 'pharmatrack_refresh',
  user: 'pharmatrack_user',
} as const;

/** Segment d'URL des endpoints d'authentification (publics, jamais interceptés). */
export const AUTH_PATH = '/auth/';

export const SESSION_PATH = '/login';
export const UNAUTHORIZED_PATH = '/unauthorized';
export const DEFAULT_PAGE_SIZE = 25;