export interface LabelCount {
  label: string;
  total: number;
}

export interface TopMedicament {
  id: string;
  nom: string;
  stock: number;
}

export interface DateCount {
  date: string;
  total: number;
}

export interface DashboardStats {
  totalMedicaments: number;
  ruptures: number;
  alertesNonLues: number;
  lotsProches: number;
  statutMedicaments: Record<string, number>;
  statutLots: Record<string, number>;
  alertesParType: Record<string, number>;
  categories: LabelCount[];
  laboratoires: LabelCount[];
  formes: LabelCount[];
  topMedicaments: TopMedicament[];
  evolutionMedicaments: DateCount[];
  lotsExpiration: DateCount[];
  evolutionAlertes: DateCount[];
}