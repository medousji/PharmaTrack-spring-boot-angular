import type {
  CommandeResponse,
  CommandeStatut,
} from '@features/fournisseur/models/fournisseur.models';

export interface MessageResponse {
  id: string;
  expediteurId: string;
  expediteurNom: string;
  destinataireId: string;
  destinataireNom: string;
  commandeId?: string;
  message: string;
  estLu: boolean;
  createdAt: string;
}

export interface CommandeChatResponse {
  id: string;
  numeroCommande: string;
  fournisseurNom: string;
  statut: CommandeStatut;
  totalTtc: number;
  createdAt: string;
  dernierMessage: string | null;
  dateDernierMessage: string | null;
  nonLus: number;
}

export interface ConversationResponse {
  contactId: string;
  nom: string;
  role: string;
  dernierMessage: string | null;
  date: string | null;
  nonLus: number;
}

export interface ChatOverview {
  commandes: CommandeChatResponse[];
  conversations: ConversationResponse[];
  totalNonLus: number;
}

export interface CommandeThread {
  commande: CommandeResponse;
  messages: MessageResponse[];
}

export interface ConversationThread {
  contactId: string;
  contactNom: string;
  contactRole: string;
  messages: MessageResponse[];
}

export interface EnvoyerMessageRequest {
  message: string;
  destinataireId?: string;
  commandeId?: string;
}

const ROLE_LABELS: Readonly<Record<string, string>> = {
  admin: 'Administrateur',
  pharmacien: 'Pharmacien',
  fournisseur: 'Fournisseur',
  visiteur: 'Visiteur',
};

export function roleLabel(role: string): string {
  return ROLE_LABELS[role] ?? role;
}