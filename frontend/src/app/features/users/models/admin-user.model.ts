import type { AuthUser, UserRole, UserStatus } from '@core/models/auth.models';

/** Utilisateur en attente d'approbation (vue admin). */
export type PendingUser = AuthUser;

export interface ApproveRequest {
  approved: boolean;
  role?: UserRole;
  pharmacieId?: string;
}

export interface CreateUserRequest {
  name: string;
  email: string;
  password: string;
  role: UserRole;
  status?: UserStatus;
  pharmacieId?: string;
}

export interface UpdateUserRequest {
  name: string;
  email: string;
  role?: UserRole;
  status?: UserStatus;
  pharmacieId?: string;
  password?: string;
}

export interface UserListParams {
  search?: string;
  role?: UserRole;
  statut?: UserStatus;
  page?: number;
  size?: number;
}

export interface AdminUserStats {
  total: number;
  enAttente: number;
  admins: number;
  pharmaciens: number;
  fournisseurs: number;
  visiteurs: number;
  parStatut: Record<string, number>;
}

/** Pharmacie de référence (sélecteurs des formulaires). */
export interface Pharmacie {
  id: string;
  nom: string;
  adresse?: string;
  telephone?: string;
}