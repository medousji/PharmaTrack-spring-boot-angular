export interface ChatbotHistoryItem {
  id: string;
  question: string;
  reponse: string;
  intention: string | null;
  donnees: Record<string, unknown> | null;
  createdAt: string;
}

export interface ChatbotResponseData {
  success: boolean;
  reponse: string;
  intention: string | null;
  donnees: Record<string, unknown> | null;
}