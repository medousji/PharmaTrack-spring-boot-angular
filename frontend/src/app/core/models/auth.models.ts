export type UserRole = 'admin' | 'pharmacien' | 'fournisseur' | 'visiteur';

export type UserStatus = 'active' | 'inactive' | 'suspended';

export interface AuthUser {
  id: string;
  name: string;
  email: string;
  role: UserRole;
  isApproved: boolean;
  status: string;
  pharmacieId?: string;
  pharmacieNom?: string;
  createdAt?: string;
  lastLoginAt?: string;
  approvedAt?: string;
}

export interface TokenResponse {
  accessToken: string;
  refreshToken: string;
  expiresInSeconds: number;
  tokenType: string;
  user: AuthUser;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  pharmacieId?: string;
}

export interface RefreshRequest {
  refreshToken: string;
}