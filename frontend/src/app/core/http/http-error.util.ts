import type { ProblemResponse } from '@core/models/api.models';

interface ErrorLike {
  status?: number;
  error?: ProblemResponse;
}

/** Extrait le message du corps problem+json d'une erreur HTTP, sinon `fallback`. */
export function problemDetail(error: unknown, fallback = 'Une erreur est survenue.'): string {
  const err = (error ?? {}) as ErrorLike;
  const body = err?.error;
  const status = body?.status ?? err?.status;
  if (body?.detail) return body.detail;
  if (body?.title) return `${body.title} (${status ?? 'inconnu'})`;
  return fallback;
}