import type { Tone } from '@shared/ui/status-tone';

export type AlerteType =
  | 'expiration'
  | 'stock'
  | 'rupture'
  | 'qualite'
  | 'autre'
  | 'inscription'
  | 'approbation';
export type AlerteNiveau = 'faible' | 'moyen' | 'eleve' | 'critique';

export interface AlerteResponse {
  id: string;
  lotId?: string;
  type: AlerteType;
  niveau: AlerteNiveau;
  message: string;
  donneesConcernees: Record<string, unknown>;
  estLue: boolean;
  resolueAt?: string;
  createdAt: string;
}

export interface AlerteEvaluationSummary {
  rupturesCreees: number;
  expirationsCreees: number;
  stocksFaiblesCrees: number;
  total: number;
  evaluatedAt: string;
}

export const ALERTE_TYPES: readonly AlerteType[] = [
  'expiration',
  'stock',
  'rupture',
  'qualite',
  'autre',
  'inscription',
  'approbation',
];

/** Niveaux d'alerte, du plus au moins critique (ordre d'affichage des compteurs). */
export const ALERTE_NIVEAUX: readonly AlerteNiveau[] = [
  'critique',
  'eleve',
  'moyen',
  'faible',
];

export const ALERTE_TYPE_LABELS: Readonly<Record<AlerteType, string>> = {
  expiration: 'Expiration',
  stock: 'Stock faible',
  rupture: 'Rupture',
  qualite: 'Qualité',
  autre: 'Autre',
  inscription: 'Inscription',
  approbation: 'Approbation',
};

export const ALERTE_NIVEAU_LABELS: Readonly<Record<AlerteNiveau, string>> = {
  critique: 'Critique',
  eleve: 'Élevé',
  moyen: 'Moyen',
  faible: 'Faible',
};

export function alerteTypeLabel(type: AlerteType): string {
  return ALERTE_TYPE_LABELS[type] ?? type;
}

export function alerteNiveauLabel(niveau: AlerteNiveau): string {
  return ALERTE_NIVEAU_LABELS[niveau] ?? niveau;
}

const ALERTE_TYPE_TONES: Readonly<Record<AlerteType, Tone>> = {
  expiration: 'danger',
  stock: 'warning',
  rupture: 'violet',
  qualite: 'success',
  autre: 'gray',
  inscription: 'info',
  approbation: 'success',
};

const ALERTE_NIVEAU_TONES: Readonly<Record<AlerteNiveau, Tone>> = {
  critique: 'critical',
  eleve: 'orange',
  moyen: 'yellow',
  faible: 'info',
};

export function alerteTypeTone(type: AlerteType): Tone {
  return ALERTE_TYPE_TONES[type];
}

export function alerteNiveauTone(niveau: AlerteNiveau): Tone {
  return ALERTE_NIVEAU_TONES[niveau];
}