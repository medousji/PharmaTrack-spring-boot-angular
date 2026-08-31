/** Réponse paginée renvoyée par l'API. */
export interface PagedResponse<T> {
  content: T[];
  page: number;
  size: number;
  totalElements: number;
  totalPages: number;
}

/** Corps RFC 7807 (problem+json) renvoyé en cas d'erreur applicative. */
export interface ProblemResponse {
  type?: string;
  title?: string;
  status: number;
  detail?: string;
  instance?: string;
  errors?: Record<string, string[]>;
}