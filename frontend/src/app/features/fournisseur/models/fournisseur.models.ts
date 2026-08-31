import type { Tone } from '@shared/ui/status-tone';

export type CommandeStatut =
  | 'en_attente'
  | 'confirmee'
  | 'preparation'
  | 'partiel'
  | 'expediee'
  | 'livree'
  | 'annulee';

export interface CommandeLigneResponse {
  id: string;
  medicamentId: string;
  medicamentNom: string;
  quantite: number;
  quantiteDemandee: number;
  stockAvant: number;
  stockRestant: number;
  quantiteManquante: number;
  quantiteLivrable: number;
  prixUnitaire: number;
  totalLigne: number;
}

export interface CommandeResponse {
  id: string;
  numeroCommande: string;
  statut: CommandeStatut;
  dateCommande: string;
  dateLivraisonPrevue?: string;
  totalHt: number;
  totalTtc: number;
  fournisseurId: string;
  fournisseurNom: string;
  createdAt: string;
  lignes: CommandeLigneResponse[];
}

export interface FournisseurStats {
  commandesEncours: number;
  commandesLivrees: number;
  produitsDisponibles: number;
}

export interface FournisseurDashboard {
  fournisseurId: string;
  raisonSociale: string;
  delaiLivraisonMoyen: number;
  stats: FournisseurStats;
  dernieresCommandes: CommandeResponse[];
}

export interface FournisseurMedicamentResponse {
  id: string;
  fournisseurId: string;
  fournisseurNom: string;
  medicamentId: string;
  medicamentNom: string;
  dci?: string;
  formePharmaceutique?: string;
  dosage?: string;
  referenceFournisseur?: string;
  prixAchat: number;
  prixPublic?: number;
  stockDisponible: number;
  stockMinimum: number;
  delaiLivraison?: number;
  disponible: boolean;
  derniereMiseAJour?: string;
}

export interface MedicamentSelectionResponse {
  id: string;
  codeCip: string;
  nomCommercialFr?: string;
  dci?: string;
  formePharmaceutique?: string;
  dosage?: string;
}

export interface DisponibiliteResponse {
  disponible: boolean;
  type: string;
  raison?: string;
  quantiteDisponible?: number;
  quantiteManquante?: number;
  stockMinimum?: number;
  stockActuel?: number;
  prixAchat: number;
  medicamentNom?: string;
  fournisseurId?: string;
  fournisseurNom?: string;
}

export interface CommandeResult {
  success: boolean;
  type: string;
  commande?: CommandeResponse;
  quantiteCommandee: number;
  quantiteManquante: number;
  stockAvant: number;
  stockApres: number;
  stockMinimum: number;
  message?: string;
  alternatifs: FournisseurMedicamentResponse[];
}

export interface UpdatePrixItem {
  id: string;
  prixAchat: number;
  stockDisponible: number;
  disponible: boolean;
}

const COMMANDE_STATUT_LABELS: Readonly<Record<CommandeStatut, string>> = {
  en_attente: 'En attente',
  confirmee: 'Confirmée',
  preparation: 'En préparation',
  partiel: 'Partielle',
  expediee: 'Expédiée',
  livree: 'Livrée',
  annulee: 'Annulée',
};

const COMMANDE_STATUT_TONES: Readonly<Record<CommandeStatut, Tone>> = {
  en_attente: 'warning',
  confirmee: 'info',
  preparation: 'yellow',
  partiel: 'orange',
  expediee: 'violet',
  livree: 'success',
  annulee: 'gray',
};

export const COMMANDE_STATUTS: readonly CommandeStatut[] = [
  'en_attente',
  'confirmee',
  'preparation',
  'partiel',
  'expediee',
  'livree',
  'annulee',
];

export function commandeStatutLabel(statut: CommandeStatut): string {
  return COMMANDE_STATUT_LABELS[statut] ?? statut;
}

export function commandeStatutTone(statut: CommandeStatut): Tone {
  return COMMANDE_STATUT_TONES[statut] ?? 'gray';
}