import type { Tone } from '@shared/ui/status-tone';

export type MedicamentStatut = 'actif' | 'inactif' | 'retire';
export type LotStatut = 'actif' | 'epuise' | 'perime' | 'bloque';
export type MouvementType = 'entree' | 'sortie' | 'ajustement';

export interface MedicamentResponse {
  id: string;
  statut: MedicamentStatut;
  createdAt: string;
  updatedAt: string;
  codeCip: string;
  nomCommercialFr: string;
  nomCommercialAr?: string;
  dci: string;
  formePharmaceutique: string;
  dosage: string;
  conditionnement?: string;
  ppv?: number;
  ph?: number;
  prixBr?: number;
  prixPublic?: number;
  tauxRemboursement?: number;
  laboratoire?: string;
  paysOrigine?: string;
  stockMin: number;
  stockMax: number;
  seuilAlerte: number;
  classeTherapeutique?: string;
  voieAdministration?: string;
  contreIndications?: string;
  effetsIndesirables?: string;
  interactionsMedicamenteuses?: string;
  conditionsConservation?: string;
  codeAtc?: string;
  estPsychotrope: boolean;
  estTherLourde: boolean;
  estRenouvelable: boolean;
  delaiRenouvellement?: number;
  codeBarre?: string;
  estGenerique: boolean;
  medicamentReferenceId?: string;
}

export interface MedicamentDetailResponse extends MedicamentResponse {
  stockActif?: number;
  stockTotal?: number;
  estEnRupture: boolean;
  estProchePeremption: boolean;
  datePeremptionProche?: string;
  valeurStock?: number;
}

export interface MedicamentCreateRequest {
  codeCip: string;
  nomCommercialFr: string;
  nomCommercialAr?: string;
  dci: string;
  formePharmaceutique: string;
  dosage: string;
  conditionnement?: string;
  ppv?: number;
  ph?: number;
  prixBr?: number;
  prixPublic?: number;
  tauxRemboursement?: number;
  laboratoire?: string;
  paysOrigine?: string;
  stockMin: number;
  stockMax: number;
  seuilAlerte: number;
  classeTherapeutique?: string;
  voieAdministration?: string;
  contreIndications?: string;
  effetsIndesirables?: string;
  interactionsMedicamenteuses?: string;
  conditionsConservation?: string;
  codeAtc?: string;
  estPsychotrope: boolean;
  estTherLourde: boolean;
  estRenouvelable: boolean;
  delaiRenouvellement?: number;
  codeBarre?: string;
  estGenerique: boolean;
  medicamentReferenceId?: string;
}

export interface MedicamentUpdateRequest extends MedicamentCreateRequest {
  statut: MedicamentStatut;
}

export interface LotResponse {
  id: string;
  medicamentId: string;
  numeroLot: string;
  dateFabrication?: string;
  datePeremption: string;
  quantiteInitiale: number;
  quantiteActuelle: number;
  fournisseurNom?: string;
  dateReception?: string;
  statut: LotStatut;
  prixAchat: number;
  prixVente: number;
  emplacement?: string;
  joursAvantPeremption: number | null;
  createdAt: string;
  updatedAt: string;
}

export interface LotCreateRequest {
  medicamentId: string;
  numeroLot: string;
  dateFabrication?: string;
  datePeremption: string;
  quantiteInitiale: number;
  fournisseurNom?: string;
  dateReception?: string;
  prixAchat: number;
  prixVente: number;
  numeroFacture?: string;
  emplacement?: string;
  observations?: string;
}

export interface StockAdjustmentRequest {
  type: MouvementType;
  quantite?: number;
  motif?: string;
  reference?: string;
}

export interface MouvementResponse {
  id: string;
  lotId: string;
  pharmacieId?: string;
  userId?: string;
  type: MouvementType;
  quantite: number;
  quantiteAvant: number;
  quantiteApres: number;
  reference?: string;
  motif?: string;
  scannedAt?: string;
  createdAt: string;
}

export interface StockAdjustmentResponse {
  lot: LotResponse;
  mouvement: MouvementResponse;
}

/* ==== Couleurs d'affichage (badges/pills) ==== */

const MEDICAMENT_STATUT_TONES: Readonly<Record<MedicamentStatut, Tone>> = {
  actif: 'success',
  inactif: 'warning',
  retire: 'danger',
};

const LOT_STATUT_TONES: Readonly<Record<LotStatut, Tone>> = {
  actif: 'success',
  epuise: 'gray',
  perime: 'danger',
  bloque: 'warning',
};

const MOUVEMENT_TYPE_TONES: Readonly<Record<MouvementType, Tone>> = {
  entree: 'success',
  sortie: 'danger',
  ajustement: 'warning',
};

export function medicamentStatutTone(statut: MedicamentStatut): Tone {
  return MEDICAMENT_STATUT_TONES[statut];
}

export function lotStatutTone(statut: LotStatut): Tone {
  return LOT_STATUT_TONES[statut];
}

export function mouvementTypeTone(type: MouvementType): Tone {
  return MOUVEMENT_TYPE_TONES[type];
}