-- ===========================================================================
-- PharmaTrack fake test data seed
-- Uses fixed UUIDs so self-references (generic -> princeps) and the append-only
-- mouvement ledger stay consistent with lot quantities.
-- BCrypt hashes are valid for: Admin@123, Pharma@123, Supply@123, Visitor@123.
-- ===========================================================================
\set ON_ERROR_STOP on
BEGIN;

-- ---------------------------------------------------------------------------
-- PHARMACIES
-- ---------------------------------------------------------------------------
INSERT INTO pharmacies (id, nom, adresse, telephone, email, licence_number, responsable) VALUES
('a0000000-0000-0000-0000-000000000001', 'Pharmacie Centrale', '12 Avenue Hassan II, Agadir', '0528-82-11-11', 'contact@pharmacie-centrale.ma', 'LC-ADR-000111', 'Dr. Amine Alaoui'),
('a0000000-0000-0000-0000-000000000002', 'Pharmacie de l''Aurore', '8 Rue Mohammed V, Casablanca', '0522-27-33-22', 'contact@pharmacie-aurore.ma', 'LC-CAS-000222', 'Dr. Salma Bennis');

-- ---------------------------------------------------------------------------
-- USERS
-- ---------------------------------------------------------------------------
INSERT INTO users (id, pharmacie_id, name, email, password_hash, role, status, is_approved, approved_at) VALUES
('b0000000-0000-0000-0000-000000000001', NULL,   'Admin Système',          'admin@pharmatrack.ma',     '$2a$10$ArQZnVIB5shaSeRHs6EINezLPoCMn7fjaQZUkisszgMi03gzUxCDy', 'admin',       'active', TRUE,  now()),
('b0000000-0000-0000-0000-000000000002', 'a0000000-0000-0000-0000-000000000001', 'Dr. Amine Alaoui',    'amine@pharmatrack.ma',     '$2a$10$cjykwCyJNdwO73h8HhJ4JOEg0M2rTXzmsBZgxEyoRsYJ.SCUtMDVm', 'pharmacien',  'active', TRUE,  now()),
('b0000000-0000-0000-0000-000000000003', 'a0000000-0000-0000-0000-000000000002', 'Dr. Salma Bennis',     'salma@pharmatrack.ma',     '$2a$10$cjykwCyJNdwO73h8HhJ4JOEg0M2rTXzmsBZgxEyoRsYJ.SCUtMDVm', 'pharmacien',  'active', TRUE,  now()),
('b0000000-0000-0000-0000-000000000004', NULL,   'Société MedSupply',       'medsupply@pharmatrack.ma', '$2a$10$o9hqZw4i9BpE2nrvZUC6pucKNZK8RHkR3ZAF63.lA5V3ogNGiTeGG', 'fournisseur', 'active', TRUE,  now()),
('b0000000-0000-0000-0000-000000000005', 'a0000000-0000-0000-0000-000000000001', 'Karim El Fassi',       'karim@pharmatrack.ma',     '$2a$10$3fE8XkixQuTSJgjSlBHIgOEenXOJ7O.ioTdmHG/aebZO.fFAOdDKy', 'visiteur',    'active', FALSE, NULL),
('b0000000-0000-0000-0000-000000000006', 'a0000000-0000-0000-0000-000000000002', 'Nadia Berrada',        'nadia@pharmatrack.ma',     '$2a$10$3fE8XkixQuTSJgjSlBHIgOEenXOJ7O.ioTdmHG/aebZO.fFAOdDKy', 'visiteur',    'inactive', TRUE, now());

-- ---------------------------------------------------------------------------
-- MEDICAMENTS
-- Princesps use code starting with "PR-", generics "GE-", psychotropes flagged.
-- ---------------------------------------------------------------------------
INSERT INTO medicaments (id, code_cip, nom_commercial_fr, nom_commercial_ar, dci, forme_pharmaceutique, dosage, conditionnement, ppv, ph, prix_br, prix_public, taux_remboursement, laboratoire, pays_origine, stock_min, stock_max, seuil_alerte, classe_therapeutique, voie_administration, contre_indications, effets_indesirables, interactions_medicamenteuses, conditions_conservation, code_atc, est_psychotrope, est_ther_lourde, est_renouvelable, delai_renouvellement, code_barre, est_generique, medicament_reference_id, statut) VALUES
-- Analgésiques / antipyrétiques (paracétamol)
('c0000000-0000-0000-0000-000000000001', '3400931234567', 'Doliprane', 'دوليفران', 'paracétamol',      'comprimé',                 '1000 mg', 'boîte de 8',         2.200,  1.540,  1.260,  2.60, 65.00, 'Sanofi',        'France',  50, 500, 100, 'Antalgique',            'orale', 'Insuffisance hépatique sévère', 'Rares réactions cutanées', 'Anticoagulants oraux', 'T° < 25°C', 'N02BE01', FALSE, FALSE, TRUE, 0, '3400931234567', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000002', '3400937654321', 'Paracétamol Biogaran', 'باراسيتامول', 'paracétamol', 'comprimé', '1000 mg', 'boîte de 8',        1.320,  0.920,  0.760,  1.60, 65.00, 'Biogaran',      'France',  40, 400, 80,  'Antalgique',            'orale', 'Insuffisance hépatique sévère', 'Rares réactions cutanées', 'Anticoagulants oraux', 'T° < 25°C', 'N02BE01', FALSE, FALSE, TRUE, 0, '3400937654321', TRUE,  'c0000000-0000-0000-0000-000000000001', 'actif'),
('c0000000-0000-0000-0000-000000000003', '3400939876543', 'Efferalgan', 'إيفيرالجان', 'paracétamol',      'comprimé effervescent', '500 mg',  'tube de 16',       1.980,  1.390,  1.140,  2.30, 65.00, 'UPSA',          'France',  30, 300, 60,  'Antalgique',            'orale', 'Insuffisance hépatique sévère', 'Irritation gastrique', 'Anticoagulants oraux', 'T° < 25°C, au sec', 'N02BE01', FALSE, FALSE, TRUE, 0, '3400939876543', FALSE, NULL, 'actif'),
-- AINS
('c0000000-0000-0000-0000-000000000004', '3400935551212', 'Ibuprofène Biogaran', 'إيبوبروفين', 'ibuprofène', 'comprimé', '400 mg', 'boîte de 20',       2.750,  1.920,  1.580,  3.20, 65.00, 'Biogaran',      'France',  30, 300, 60,  'Anti-inflammatoire',    'orale', 'Ulcère gastroduodénal', 'Troubles digestifs', 'AINS, anticoagulants', 'T° < 25°C', 'M01AE01', FALSE, FALSE, TRUE, 0, '3400935551212', TRUE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000005', '3400938765432', 'Advil', 'أدفيل', 'ibuprofène',           'comprimé', '400 mg', 'boîte de 20',       3.850,  2.690,  2.210,  4.40, 65.00, 'Pfizer',        'Belgique', 20, 250, 50,  'Anti-inflammatoire',    'orale', 'Ulcère gastroduodénal', 'Troubles digestifs', 'AINS, anticoagulants', 'T° < 25°C', 'M01AE01', FALSE, FALSE, TRUE, 0, '3400938765432', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000006', '3400932223334', 'Voltarène', 'فولتارين', 'diclofénac',       'comprimé gastro-résistant', '50 mg', 'boîte de 20',   4.950,  3.470,  2.850,  5.60, 65.00, 'Novartis',      'Suisse',  20, 200, 40,  'Anti-inflammatoire',    'orale', 'Ulcère, Insuff. cardiaque', 'Troubles digestifs', 'AINS, diurétiques', 'T° < 25°C', 'M01AB05', FALSE, FALSE, TRUE, 0, '3400932223334', FALSE, NULL, 'actif'),
-- Antibiotiques (pénicillines)
('c0000000-0000-0000-0000-000000000007', '3400934445556', 'Amoxicilline Biogaran', 'أموكسيسيلين', 'amoxicilline', 'gélule', '500 mg', 'boîte de 12',    3.300,  2.310,  1.980,  3.90, 65.00, 'Biogaran',      'France',  30, 300, 60,  'Antibiotique',          'orale', 'Allergie aux pénicillines', 'Diarrhée', 'Anticoagulants', 'T° < 25°C', 'J01CA04', FALSE, FALSE, TRUE, 0, '3400934445556', TRUE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000008', '3400936667778', 'Augmentin', 'أغمينتين', 'amoxicilline/ac.clav', 'comprimé', '1 g / 125 mg', 'boîte de 12',  9.800,  6.860,  5.880, 11.20, 65.00, 'GSK',           'France',  15, 150, 30,  'Antibiotique',          'orale', 'Allergie aux pénicillines', 'Diarrhée', 'Allopurinol', 'T° < 25°C', 'J01CR02', FALSE, FALSE, TRUE, 0, '3400936667778', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000009', '3400938889990', 'Zithromax', 'زيثروماكس', 'azithromycine',   'comprimé', '250 mg', 'boîte de 6',       7.450,  5.220,  4.470,  8.50, 65.00, 'Pfizer',        'Belgique', 15, 120, 30,  'Antibiotique (macrolide)','orale', 'Insuff. hépatique sévère', 'Troubles digestifs', 'Antiacides', 'T° < 25°C', 'J01FA10', FALSE, FALSE, TRUE, 0, '3400938889990', FALSE, NULL, 'actif'),
-- Protections gastriques
('c0000000-0000-0000-0000-000000000010', '3400931112223', 'Oméprazole Biogaran', 'أوميبرازول', 'oméprazole', 'gélule gastro-résistante', '20 mg', 'boîte de 14', 3.630,  2.540,  2.180,  4.10, 65.00, 'Biogaran',      'France',  30, 300, 60,  'Anti-ulcéreux (IPP)',   'orale', 'Hypersensibilité aux IPP', 'Céphalées', 'Clopidogrel', 'T° < 25°C', 'A02BC01', FALSE, FALSE, TRUE, 0, '3400931112223', TRUE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000011', '3400935556677', 'Lansoprazole', 'لانسوبرازول', 'lansoprazole', 'gélule gastro-résistante', '30 mg', 'boîte de 14',  4.290,  3.000,  2.570,  4.90, 65.00, 'Sandoz',        'France',  20, 200, 40,  'Anti-ulcéreux (IPP)',   'orale', 'Hypersensibilité aux IPP', 'Céphalées', 'Théophylline', 'T° < 25°C', 'A02BC03', FALSE, FALSE, TRUE, 0, '3400935556677', TRUE, NULL, 'actif'),
-- Cardiologie
('c0000000-0000-0000-0000-000000000012', '3400937778889', 'Amlor', 'أملور', 'amlodipine',           'comprimé', '5 mg', 'boîte de 30',       6.600,  4.620,  3.960,  7.50, 65.00, 'Pfizer',        'France',  15, 150, 30,  'Antihypertenseur (I.Ca)','orale', 'Hypotension sévère', 'Œdèmes des membres', 'Inhibiteurs CYP3A4', 'T° < 25°C', 'C08CA01', FALSE, FALSE, TRUE, 0, '3400937778889', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000013', '3400933334445', 'Concor', 'كونكور', 'bisoprolol',           'comprimé', '5 mg', 'boîte de 30',      7.150,  5.000,  4.290,  8.10, 65.00, 'Merck',         'Allemagne',15, 150, 30,  'Bêtabloquant',          'orale', 'Bradycardie, asthme', 'Bradycardie', 'Amiodarone', 'T° < 25°C', 'C07AB07', FALSE, FALSE, TRUE, 0, '3400933334445', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000014', '3400939990001', 'Plavix', 'بلافيكس', 'clopidogrel',          'comprimé', '75 mg', 'boîte de 30',     12.300,  8.610,  7.380, 14.00, 65.00, 'Sanofi',        'France',  10, 120, 25,  'Antiagrégant plaquettaire','orale', 'Hémorragie active', 'Saignements', 'AINS, anticoagulants', 'T° < 25°C', 'B01AC04', FALSE, FALSE, TRUE, 0, '3400939990001', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000015', '3400932224446', 'Crestor', 'كريستور', 'rosuvastatine',      'comprimé', '10 mg', 'boîte de 30',     9.780,  6.850,  5.870, 11.20, 65.00, 'AstraZeneca',   'France',  15, 120, 30,  'Hypolipémiant (statine)','orale', 'Insuff. hépatique', 'Myalgies', 'Anticoagulants', 'T° < 25°C', 'C10AA07', FALSE, FALSE, TRUE, 0, '3400932224446', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000016', '3400936668880', 'Atorvastatine', 'أتورفاستاتين', 'atorvastatine','comprimé', '20 mg', 'boîte de 30',    5.500,  3.850,  3.300,  6.30, 65.00, 'Sandoz',        'France',  20, 150, 40,  'Hypolipémiant (statine)','orale', 'Insuff. hépatique', 'Myalgies', 'Clarithromycine', 'T° < 25°C', 'C10AA05', FALSE, FALSE, TRUE, 0, '3400936668880', TRUE, NULL, 'actif'),
-- Métabolisme (diabète, thyroïde)
('c0000000-0000-0000-0000-000000000017', '3400931113334', 'Metformine', 'ميتفورمين', 'metformine',      'comprimé', '850 mg', 'boîte de 60',      2.640,  1.850,  1.580,  3.10, 65.00, 'Sandoz',        'France',  50, 400, 100, 'Antidiabétique (biguanide)','orale', 'Insuff. rénale', 'Troubles digestifs', 'Produits de contraste', 'T° < 25°C', 'A10BA02', FALSE, FALSE, TRUE, 0, '3400931113334', TRUE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000018', '3400934446667', 'Januvia', 'جانوفيا', 'sitagliptine',        'comprimé', '100 mg', 'boîte de 28',   18.900,  13.230, 11.340, 21.50, 65.00, 'MSD',           'France',  10, 100, 20,  'Antidiabétique (DPP4)','orale', 'Insuff. rénale sévère', 'Pancréatite', 'Sulfamides', 'T° < 25°C', 'A10BH01', FALSE, FALSE, TRUE, 0, '3400934446667', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000019', '3400932223335', 'Levothyrox', 'ليفوتيروكس', 'lévothyroxine',  'comprimé', '50 µg', 'boîte de 30',     4.620,  3.230,  2.770,  5.30, 65.00, 'Merck',         'Allemagne',30, 250, 60,  'Hormone thyroïdienne', 'orale', 'Hyperthyroïdie non traitée', 'Palpitations', 'Anticoagulants', 'T° < 25°C', 'H03AA01', FALSE, FALSE, TRUE, 0, '3400932223335', FALSE, NULL, 'actif'),
-- RESPIRATOIRE / ALLERGIE
('c0000000-0000-0000-0000-000000000020', '3400936667779', 'Ventoline', 'فينتولين', 'salbutamol',       'solution pour inhalation (aérosol)', '100 µg/dose', 'aérosol 200 doses', 4.950, 3.470, 2.970,  5.60, 65.00, 'GSK', 'France', 10, 90, 20, 'Bronchodilatateur (B2)','inhalation', 'Allergie au salbutamol', 'Tremblements', 'Bêtabloquants', 'T° < 25°C, à l''abri de la chaleur', 'R03AC02', FALSE, FALSE, TRUE, 0, '3400936667779', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000021', '3400938881112', 'Zyrtec', 'زيرتيك', 'cétirizine',          'comprimé', '10 mg', 'boîte de 7',      2.200,  1.540,  1.320,  2.60, 65.00, 'UCB',           'Belgique', 30, 250, 60,  'Antihistaminique',      'orale', 'Insuff. rénale sévère', 'Somnolence', 'Alcool', 'T° < 25°C', 'R06AE07', FALSE, FALSE, TRUE, 0, '3400938881112', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000022', '3400935558889', 'Clarityne', 'كلاريتين', 'loratadine',       'comprimé', '10 mg', 'boîte de 7',      2.530,  1.770,  1.520,  3.00, 65.00, 'Bayer',         'France',  30, 250, 60,  'Antihistaminique',      'orale', 'Allergie à la loratadine', 'Céphalées', 'Kétoconazole', 'T° < 25°C', 'R06AX13', FALSE, FALSE, TRUE, 0, '3400935558889', FALSE, NULL, 'actif'),
-- GASTRO
('c0000000-0000-0000-0000-000000000023', '3400932221113', 'Imodium', 'إيموديوم', 'lopéramide',        'gélule', '2 mg', 'boîte de 20',        2.750,  1.920,  1.650,  3.20, 65.00, 'Sanofi',        'France',  20, 180, 40,  'Antidiarrhéique',       'orale', 'Dysenterie', 'Constipation', 'Aucune majeure', 'T° < 25°C', 'A07DA03', FALSE, FALSE, TRUE, 0, '3400932221113', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000024', '3400934442224', 'Smecta', 'سميكتا', 'diosmectite',         'poudre pour suspension buvable', '3 g', 'boîte de 30 sachets', 4.400, 3.080, 2.640,  5.00, 65.00, 'Ipsen',         'France', 25, 200, 50, 'Antidiarrhéique (adsorbant)','orale', 'Obstruction intestinale', 'Constipation', 'Aucune majeure', 'T° < 25°C, au sec', 'A07BC05', FALSE, FALSE, TRUE, 0, '3400934442224', FALSE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000025', '3400936663335', 'Spasfon', 'سباسفون', 'phloroglucinol',     'comprimé', '80 mg', 'boîte de 30',      3.190,  2.230,  1.910,  3.70, 65.00, 'Mayoly Spindler','France', 30, 250, 60, 'Antispasmodique',       'orale', 'Allergie au phloroglucinol', 'Allergie cutanée', 'Aucune majeure', 'T° < 25°C', 'A03AX', FALSE, FALSE, TRUE, 0, '3400936663335', FALSE, NULL, 'actif'),
-- PSYCHOTROPES
('c0000000-0000-0000-0000-000000000026', '3400931119999', 'Temesta', 'تيميستا', 'lorazépam',          'comprimé', '1 mg', 'boîte de 30',      2.200,  1.540,  1.320,  2.60, 65.00, 'Pfizer',        'France',  10, 100, 20,  'Anxiolytique (benzodiazépine)','orale', 'Myasthénie, insuff. resp.', 'Somnolence', 'Alcool, opiacés', 'T° < 25°C', 'N05BA06', TRUE, FALSE, TRUE, 0, '3400931119999', TRUE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000027', '3400932220000', 'Xanax', 'زاناكس', 'alprazolam',           'comprimé', '0,25 mg', 'boîte de 30',    2.750,  1.920,  1.650,  3.20, 65.00, 'Pfizer',        'France',  10, 100, 20,  'Anxiolytique (benzodiazépine)','orale', 'Myasthénie, insuff. resp.', 'Somnolence', 'Alcool, opiacés', 'T° < 25°C', 'N05BA12', TRUE, FALSE, TRUE, 0, '3400932220000', TRUE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000028', '3400931111111', 'Stilnox', 'ستيلنوكس', 'zolpidem',          'comprimé', '10 mg', 'boîte de 14',     3.080,  2.150,  1.850,  3.50, 65.00, 'Sanofi',        'France',  10, 90,  20,  'Hypnotique',            'orale', 'Apnée du sommeil', 'Somnolence diurne', 'Alcool, opiacés', 'T° < 25°C', 'N05CF02', TRUE, FALSE, FALSE, 28, '3400931111111', TRUE, NULL, 'actif'),
('c0000000-0000-0000-0000-000000000029', '3400932222222', 'Deroxat', 'ديروكسات', 'paroxétine',        'comprimé', '20 mg', 'boîte de 28',     11.500,  8.050,  6.900, 13.10, 65.00, 'GSK',           'France',  10, 100, 20,  'Antidépresseur (ISRS)', 'orale', 'IMAO', 'Somnolence, nausées', 'IMAO, alcool', 'T° < 25°C', 'N06AB05', TRUE, FALSE, FALSE, 0, '3400932222222', TRUE, NULL, 'actif'),
-- Divers
('c0000000-0000-0000-0000-000000000030', '3400933333333', 'Betadine', 'بيتادين', 'povidone iodée',     'solution pour application cutanée (dermique)', '10 %', 'flacon 125 ml', 3.300, 2.310, 1.980,  3.80, 65.00, 'MEDA Pharma',   'France', 15, 120, 30, 'Antiseptique',          'cutanée', 'Allergie à l''iode', 'Irritation locale', 'Aucune majeure', 'T° < 25°C, à l''abri de la lumière', 'D08AG02', FALSE, FALSE, TRUE, 0, '3400933333333', FALSE, NULL, 'actif');

-- Attach generics to their princeps reference (paracétamol princeps = Doliprane id 1)
UPDATE medicaments SET medicament_reference_id = 'c0000000-0000-0000-0000-000000000001'
 WHERE id IN ('c0000000-0000-0000-0000-000000000002', 'c0000000-0000-0000-0000-000000000003');
UPDATE medicaments SET medicament_reference_id = 'c0000000-0000-0000-0000-000000000005'
 WHERE id = 'c0000000-0000-0000-0000-000000000004';
UPDATE medicaments SET medicament_reference_id = 'c0000000-0000-0000-0000-000000000008'
 WHERE id = 'c0000000-0000-0000-0000-000000000007';
UPDATE medicaments SET medicament_reference_id = 'c0000000-0000-0000-0000-000000000005'
 WHERE id = 'c0000000-0000-0000-0000-000000000009';

-- ---------------------------------------------------------------------------
-- LOTS
-- Varied expiry: some healthy, some near-expiry (-> 'expiration' alerts),
-- some already expired (-> 'perime' status). quantite_actuelle < initiale
-- on several lots so the movement ledger and stock alerts make sense.
-- ---------------------------------------------------------------------------
INSERT INTO lots (id, medicament_id, numero_lot, date_fabrication, date_peremption, quantite_initiale, quantite_actuelle, fournisseur_nom, date_reception, statut, prix_achat, prix_vente, numero_facture, emplacement, observations) VALUES
('d0000000-0000-0000-0000-000000000001', 'c0000000-0000-0000-0000-000000000001', 'DOL-A-001', '2025-01-15', '2027-01-15', 300,   88, 'Sanofi Distribution',    '2025-02-01', 'actif',   1.260, 2.600, 'F-2025-0001', 'A1-01', 'Bon état'),
('d0000000-0000-0000-0000-000000000002', 'c0000000-0000-0000-0000-000000000001', 'DOL-A-002', '2025-06-10', '2027-06-10', 200,  120, 'Sanofi Distribution',    '2025-07-01', 'actif',   1.260, 2.600, 'F-2025-0002', 'A1-02', NULL),
('d0000000-0000-0000-0000-000000000003', 'c0000000-0000-0000-0000-000000000002', 'PBG-A-001', '2024-11-20', '2026-09-01', 150,   40, 'Biogaran Distribution',  '2024-12-05', 'actif',   0.760, 1.600, 'F-2024-0015', 'A2-01', 'Proche péremption'),
('d0000000-0000-0000-0000-000000000004', 'c0000000-0000-0000-0000-000000000002', 'PBG-A-002', '2025-03-01', '2026-03-01', 100,   15, 'Biogaran Distribution',  '2025-03-20', 'actif',   0.760, 1.600, 'F-2025-0008', 'A2-02', 'Stock faible -> alerte stock'),
('d0000000-0000-0000-0000-000000000005', 'c0000000-0000-0000-0000-000000000003', 'EFF-A-001', '2024-08-05', '2026-05-01', 180,   90, 'UPSA Distribution',      '2024-09-01', 'actif',   1.140, 2.300, 'F-2024-0020', 'B1-01', NULL),
('d0000000-0000-0000-0000-000000000006', 'c0000000-0000-0000-0000-000000000004', 'IBU-A-001', '2025-02-14', '2027-02-14', 200,   35, 'Biogaran Distribution',  '2025-03-02', 'actif',   1.580, 3.200, 'F-2025-0009', 'B2-01', 'Stock faible'),
('d0000000-0000-0000-0000-000000000007', 'c0000000-0000-0000-0000-000000000005', 'ADV-A-001', '2025-04-01', '2027-04-01', 120,   75, 'Pfizer Distribution',    '2025-04-20', 'actif',   2.210, 4.400, 'F-2025-0012', 'B2-02', NULL),
('d0000000-0000-0000-0000-000000000008', 'c0000000-0000-0000-0000-000000000006', 'VOL-A-001', '2025-01-30', '2027-01-30', 100,   55, 'Novartis Distribution',  '2025-02-15', 'actif',   2.850, 5.600, 'F-2025-0005', 'C1-01', NULL),
('d0000000-0000-0000-0000-000000000009', 'c0000000-0000-0000-0000-000000000007', 'AMX-A-001', '2025-05-01', '2027-05-01', 250,   18, 'Biogaran Distribution',  '2025-05-25', 'actif',   1.980, 3.900, 'F-2025-0018', 'C2-01', 'Stock critique'),
('d0000000-0000-0000-0000-000000000010', 'c0000000-0000-0000-0000-000000000008', 'AUG-A-001', '2024-12-01', '2026-12-01', 120,   60, 'GSK Distribution',       '2025-01-10', 'actif',   5.880, 11.200, 'F-2025-0003', 'C2-02', NULL),
('d0000000-0000-0000-0000-000000000011', 'c0000000-0000-0000-0000-000000000009', 'ZIT-A-001', '2025-02-20', '2027-02-20', 80,    12, 'Pfizer Distribution',    '2025-03-10', 'actif',   4.470, 8.500, 'F-2025-0010', 'D1-01', 'Stock faible'),
('d0000000-0000-0000-0000-000000000012', 'c0000000-0000-0000-0000-000000000010', 'OME-A-001', '2025-03-15', '2027-03-15', 200,  100, 'Biogaran Distribution',  '2025-04-02', 'actif',   2.180, 4.100, 'F-2025-0013', 'D1-02', NULL),
('d0000000-0000-0000-0000-000000000013', 'c0000000-0000-0000-0000-000000000011', 'LAN-A-001', '2025-04-10', '2027-04-10', 95,    50, 'Sandoz Distribution',    '2025-04-28', 'actif',   2.570, 4.900, 'F-2025-0015', 'D2-01', NULL),
('d0000000-0000-0000-0000-000000000014', 'c0000000-0000-0000-0000-000000000012', 'AML-A-001', '2024-07-01', '2026-07-01', 150,   70, 'Pfizer Distribution',    '2024-08-01', 'actif',   3.960, 7.500, 'F-2024-0025', 'E1-01', NULL),
('d0000000-0000-0000-0000-000000000015', 'c0000000-0000-0000-0000-000000000013', 'CON-A-001', '2025-01-05', '2027-01-05', 90,    45, 'Merck Distribution',     '2025-01-22', 'actif',   4.290, 8.100, 'F-2025-0002', 'E1-02', NULL),
('d0000000-0000-0000-0000-000000000016', 'c0000000-0000-0000-0000-000000000014', 'PLA-A-001', '2025-02-10', '2027-02-10', 60,    10, 'Sanofi Distribution',    '2025-02-26', 'actif',   7.380, 14.000, 'F-2025-0006', 'E2-01', 'Stock faible'),
('d0000000-0000-0000-0000-000000000017', 'c0000000-0000-0000-0000-000000000015', 'CRE-A-001', '2025-03-05', '2027-03-05', 70,    38, 'AstraZeneca Dist.',      '2025-03-24', 'actif',   5.870, 11.200, 'F-2025-0011', 'E2-02', NULL),
('d0000000-0000-0000-0000-000000000018', 'c0000000-0000-0000-0000-000000000016', 'ATO-A-001', '2025-04-20', '2027-04-20', 100,   65, 'Sandoz Distribution',    '2025-05-08', 'actif',   3.300, 6.300, 'F-2025-0016', 'F1-01', NULL),
('d0000000-0000-0000-0000-000000000019', 'c0000000-0000-0000-0000-000000000017', 'MET-A-001', '2025-01-12', '2027-01-12', 300,  160, 'Sandoz Distribution',    '2025-02-01', 'actif',   1.580, 3.100, 'F-2025-0004', 'F1-02', NULL),
('d0000000-0000-0000-0000-000000000020', 'c0000000-0000-0000-0000-000000000018', 'JAN-A-001', '2025-05-01', '2027-05-01', 40,    22, 'MSD Distribution',       '2025-05-20', 'actif',  11.340, 21.500, 'F-2025-0019', 'F2-01', NULL),
('d0000000-0000-0000-0000-000000000021', 'c0000000-0000-0000-0000-000000000019', 'LEV-A-001', '2025-02-01', '2027-02-01', 120,   30, 'Merck Distribution',     '2025-02-18', 'actif',   2.770, 5.300, 'F-2025-0007', 'F2-02', 'Stock faible'),
('d0000000-0000-0000-0000-000000000022', 'c0000000-0000-0000-0000-000000000020', 'VEN-A-001', '2025-03-01', '2026-08-01', 60,    24, 'GSK Distribution',       '2025-03-15', 'actif',   2.970, 5.600, 'F-2025-0011', 'G1-01', NULL),
('d0000000-0000-0000-0000-000000000023', 'c0000000-0000-0000-0000-000000000021', 'ZYR-A-001', '2025-04-15', '2027-04-15', 90,    55, 'UCB Distribution',       '2025-05-01', 'actif',   1.320, 2.600, 'F-2025-0017', 'G1-02', NULL),
('d0000000-0000-0000-0000-000000000024', 'c0000000-0000-0000-0000-000000000022', 'CLA-A-001', '2025-05-10', '2027-05-10', 70,    40, 'Bayer Distribution',     '2025-05-25', 'actif',   1.520, 3.000, 'F-2025-0020', 'G2-01', NULL),
('d0000000-0000-0000-0000-000000000025', 'c0000000-0000-0000-0000-000000000023', 'IMO-A-001', '2024-09-01', '2026-06-01', 80,    28, 'Sanofi Distribution',    '2024-10-01', 'actif',   1.650, 3.200, 'F-2024-0022', 'H1-01', 'Proche péremption'),
('d0000000-0000-0000-0000-000000000026', 'c0000000-0000-0000-0000-000000000024', 'SME-A-001', '2025-01-20', '2027-01-20', 100,   66, 'Ipsen Distribution',     '2025-02-05', 'actif',   2.640, 5.000, 'F-2025-0005', 'H1-02', NULL),
('d0000000-0000-0000-0000-000000000027', 'c0000000-0000-0000-0000-000000000025', 'SPA-A-001', '2025-02-25', '2027-02-25', 110,   85, 'Mayoly Spindler Dist.',  '2025-03-12', 'actif',   1.910, 3.700, 'F-2025-0010', 'H2-01', NULL),
-- PSYCHOTROPES
('d0000000-0000-0000-0000-000000000028', 'c0000000-0000-0000-0000-000000000026', 'TEM-A-001', '2024-10-01', '2026-10-01', 60,   30, 'Pfizer Distribution',    '2024-10-20', 'actif',   1.320, 2.600, 'F-2024-0030', 'P1-01', 'Psychotrope — stockage sécurisé'),
('d0000000-0000-0000-0000-000000000029', 'c0000000-0000-0000-0000-000000000027', 'XAN-A-001', '2024-09-15', '2026-09-15', 60,   12, 'Pfizer Distribution',    '2024-10-01', 'actif',   1.650, 3.200, 'F-2024-0031', 'P1-02', 'Psychotrope — stock faible'),
('d0000000-0000-0000-0000-000000000030', 'c0000000-0000-0000-0000-000000000028', 'STI-A-001', '2024-09-10', '2026-09-10', 40,   20, 'Sanofi Distribution',    '2024-09-25', 'actif',   1.850, 3.500, 'F-2024-0029', 'P2-01', 'Psychotrope'),
('d0000000-0000-0000-0000-000000000031', 'c0000000-0000-0000-0000-000000000029', 'DER-A-001', '2024-11-01', '2026-11-01', 56,   44, 'GSK Distribution',       '2024-11-15', 'actif',   6.900, 13.100, 'F-2024-0032', 'P2-02', 'Psychotrope'),
-- EXPIRED LOT (already past expiry -> statut 'perime')
('d0000000-0000-0000-0000-000000000032', 'c0000000-0000-0000-0000-000000000030', 'BET-A-001', '2023-05-01', '2025-05-01', 50,   34, 'MEDA Distribution',      '2023-06-01', 'perime',   1.980, 3.800, 'F-2023-0009', 'Q1-01', 'Lot périmé (05/2025)');

-- ---------------------------------------------------------------------------
-- MOUVEMENTS (append-only ledger)
-- One 'entree' (full initial qty) + one 'sortie' (initiale - actuelle) per lot.
-- ---------------------------------------------------------------------------
INSERT INTO mouvements (lot_id, user_id, type, quantite, quantite_avant, quantite_apres, reference, motif, created_at) VALUES
('d0000000-0000-0000-0000-000000000001','b0000000-0000-0000-0000-000000000002','entree',300,  0,300,'F-2025-0001','Réception commande',            '2025-02-01 09:00:00+00'),
('d0000000-0000-0000-0000-000000000001','b0000000-0000-0000-0000-000000000002','sortie',212,300, 88,'V-2025-0101','Ventes période',                 '2025-08-20 16:30:00+00'),
('d0000000-0000-0000-0000-000000000002','b0000000-0000-0000-0000-000000000002','entree',200,  0,200,'F-2025-0002','Réception commande',            '2025-07-01 10:00:00+00'),
('d0000000-0000-0000-0000-000000000002','b0000000-0000-0000-0000-000000000002','sortie', 80,200,120,'V-2025-0201','Ventes période',                 '2025-08-25 11:45:00+00'),
('d0000000-0000-0000-0000-000000000003','b0000000-0000-0000-0000-000000000002','entree',150,  0,150,'F-2024-0015','Réception commande',            '2024-12-05 09:30:00+00'),
('d0000000-0000-0000-0000-000000000003','b0000000-0000-0000-0000-000000000002','sortie',110,150, 40,'V-2025-0301','Ventes période',                 '2025-08-15 14:00:00+00'),
('d0000000-0000-0000-0000-000000000004','b0000000-0000-0000-0000-000000000002','entree',100,  0,100,'F-2025-0008','Réception commande',            '2025-03-20 08:45:00+00'),
('d0000000-0000-0000-0000-000000000004','b0000000-0000-0000-0000-000000000002','sortie', 85,100, 15,'V-2025-0401','Ventes période',                 '2025-08-22 17:20:00+00'),
('d0000000-0000-0000-0000-000000000005','b0000000-0000-0000-0000-000000000002','entree',180,  0,180,'F-2024-0020','Réception commande',            '2024-09-01 11:00:00+00'),
('d0000000-0000-0000-0000-000000000005','b0000000-0000-0000-0000-000000000002','sortie', 90,180, 90,'V-2025-0501','Ventes période',                 '2025-08-10 10:10:00+00'),
('d0000000-0000-0000-0000-000000000006','b0000000-0000-0000-0000-000000000002','entree',200,  0,200,'F-2025-0009','Réception commande',            '2025-03-02 09:15:00+00'),
('d0000000-0000-0000-0000-000000000006','b0000000-0000-0000-0000-000000000002','sortie',165,200, 35,'V-2025-0601','Ventes période',                 '2025-08-28 13:00:00+00'),
('d0000000-0000-0000-0000-000000000007','b0000000-0000-0000-0000-000000000002','entree',120,  0,120,'F-2025-0012','Réception commande',            '2025-04-20 10:30:00+00'),
('d0000000-0000-0000-0000-000000000007','b0000000-0000-0000-0000-000000000002','sortie', 45,120, 75,'V-2025-0701','Ventes période',                 '2025-08-18 12:00:00+00'),
('d0000000-0000-0000-0000-000000000008','b0000000-0000-0000-0000-000000000003','entree',100,  0,100,'F-2025-0005','Réception commande',            '2025-02-15 09:00:00+00'),
('d0000000-0000-0000-0000-000000000008','b0000000-0000-0000-0000-000000000003','sortie', 45,100, 55,'V-2025-0801','Ventes période',                 '2025-08-21 15:30:00+00'),
('d0000000-0000-0000-0000-000000000009','b0000000-0000-0000-0000-000000000002','entree',250,  0,250,'F-2025-0018','Réception commande',            '2025-05-25 09:40:00+00'),
('d0000000-0000-0000-0000-000000000009','b0000000-0000-0000-0000-000000000002','sortie',232,250, 18,'V-2025-0901','Ventes période',                 '2025-08-29 18:00:00+00'),
('d0000000-0000-0000-0000-000000000010','b0000000-0000-0000-0000-000000000003','entree',120,  0,120,'F-2025-0003','Réception commande',            '2025-01-10 09:20:00+00'),
('d0000000-0000-0000-0000-000000000010','b0000000-0000-0000-0000-000000000003','sortie', 60,120, 60,'V-2025-1001','Ventes période',                 '2025-08-14 11:30:00+00'),
('d0000000-0000-0000-0000-000000000011','b0000000-0000-0000-0000-000000000002','entree', 80,  0, 80,'F-2025-0010','Réception commande',            '2025-03-10 10:00:00+00'),
('d0000000-0000-0000-0000-000000000011','b0000000-0000-0000-0000-000000000002','sortie', 68, 80, 12,'V-2025-1101','Ventes période',                 '2025-08-26 16:10:00+00'),
('d0000000-0000-0000-0000-000000000012','b0000000-0000-0000-0000-000000000002','entree',200,  0,200,'F-2025-0013','Réception commande',            '2025-04-02 09:50:00+00'),
('d0000000-0000-0000-0000-000000000012','b0000000-0000-0000-0000-000000000002','sortie',100,200,100,'V-2025-1201','Ventes période',                 '2025-08-19 14:40:00+00'),
('d0000000-0000-0000-0000-000000000013','b0000000-0000-0000-0000-000000000002','entree', 95,  0, 95,'F-2025-0015','Réception commande',            '2025-04-28 10:20:00+00'),
('d0000000-0000-0000-0000-000000000013','b0000000-0000-0000-0000-000000000002','sortie', 45, 95, 50,'V-2025-1301','Ventes période',                 '2025-08-24 15:00:00+00'),
('d0000000-0000-0000-0000-000000000014','b0000000-0000-0000-0000-000000000003','entree',150,  0,150,'F-2024-0025','Réception commande',            '2024-08-01 09:30:00+00'),
('d0000000-0000-0000-0000-000000000014','b0000000-0000-0000-0000-000000000003','sortie', 80,150, 70,'V-2025-1401','Ventes période',                 '2025-08-13 12:20:00+00'),
('d0000000-0000-0000-0000-000000000015','b0000000-0000-0000-0000-000000000003','entree', 90,  0, 90,'F-2025-0002','Réception commande',            '2025-01-22 09:10:00+00'),
('d0000000-0000-0000-0000-000000000015','b0000000-0000-0000-0000-000000000003','sortie', 45, 90, 45,'V-2025-1501','Ventes période',                 '2025-08-16 11:00:00+00'),
('d0000000-0000-0000-0000-000000000016','b0000000-0000-0000-0000-000000000003','entree', 60,  0, 60,'F-2025-0006','Réception commande',            '2025-02-26 09:00:00+00'),
('d0000000-0000-0000-0000-000000000016','b0000000-0000-0000-0000-000000000003','sortie', 50, 60, 10,'V-2025-1601','Ventes période',                 '2025-08-27 17:45:00+00'),
('d0000000-0000-0000-0000-000000000017','b0000000-0000-0000-0000-000000000003','entree', 70,  0, 70,'F-2025-0011','Réception commande',            '2025-03-24 10:00:00+00'),
('d0000000-0000-0000-0000-000000000017','b0000000-0000-0000-0000-000000000003','sortie', 32, 70, 38,'V-2025-1701','Ventes période',                 '2025-08-20 15:50:00+00'),
('d0000000-0000-0000-0000-000000000018','b0000000-0000-0000-0000-000000000002','entree',100,  0,100,'F-2025-0016','Réception commande',            '2025-05-08 09:30:00+00'),
('d0000000-0000-0000-0000-000000000018','b0000000-0000-0000-0000-000000000002','sortie', 35,100, 65,'V-2025-1801','Ventes période',                 '2025-08-23 14:10:00+00'),
('d0000000-0000-0000-0000-000000000019','b0000000-0000-0000-0000-000000000002','entree',300,  0,300,'F-2025-0004','Réception commande',            '2025-02-01 08:50:00+00'),
('d0000000-0000-0000-0000-000000000019','b0000000-0000-0000-0000-000000000002','sortie',140,300,160,'V-2025-1901','Ventes période',                 '2025-08-28 10:30:00+00'),
('d0000000-0000-0000-0000-000000000020','b0000000-0000-0000-0000-000000000003','entree', 40,  0, 40,'F-2025-0019','Réception commande',            '2025-05-20 09:00:00+00'),
('d0000000-0000-0000-0000-000000000020','b0000000-0000-0000-0000-000000000003','sortie', 18, 40, 22,'V-2025-2001','Ventes période',                 '2025-08-22 12:40:00+00'),
('d0000000-0000-0000-0000-000000000021','b0000000-0000-0000-0000-000000000002','entree',120,  0,120,'F-2025-0007','Réception commande',            '2025-02-18 09:40:00+00'),
('d0000000-0000-0000-0000-000000000021','b0000000-0000-0000-0000-000000000002','sortie', 90,120, 30,'V-2025-2101','Ventes période',                 '2025-08-26 16:00:00+00'),
('d0000000-0000-0000-0000-000000000022','b0000000-0000-0000-0000-000000000002','entree', 60,  0, 60,'F-2025-0011','Réception commande',            '2025-03-15 10:00:00+00'),
('d0000000-0000-0000-0000-000000000022','b0000000-0000-0000-0000-000000000002','sortie', 36, 60, 24,'V-2025-2201','Ventes période',                 '2025-08-19 13:20:00+00'),
('d0000000-0000-0000-0000-000000000023','b0000000-0000-0000-0000-000000000003','entree', 90,  0, 90,'F-2025-0017','Réception commande',            '2025-05-01 09:20:00+00'),
('d0000000-0000-0000-0000-000000000023','b0000000-0000-0000-0000-000000000003','sortie', 35, 90, 55,'V-2025-2301','Ventes période',                 '2025-08-21 14:30:00+00'),
('d0000000-0000-0000-0000-000000000024','b0000000-0000-0000-0000-000000000003','entree', 70,  0, 70,'F-2025-0020','Réception commande',            '2025-05-25 09:10:00+00'),
('d0000000-0000-0000-0000-000000000024','b0000000-0000-0000-0000-000000000003','sortie', 30, 70, 40,'V-2025-2401','Ventes période',                 '2025-08-17 15:00:00+00'),
('d0000000-0000-0000-0000-000000000025','b0000000-0000-0000-0000-000000000002','entree', 80,  0, 80,'F-2024-0022','Réception commande',            '2024-10-01 09:30:00+00'),
('d0000000-0000-0000-0000-000000000025','b0000000-0000-0000-0000-000000000002','sortie', 52, 80, 28,'V-2025-2501','Ventes période',                 '2025-08-12 16:40:00+00'),
('d0000000-0000-0000-0000-000000000026','b0000000-0000-0000-0000-000000000002','entree',100,  0,100,'F-2025-0005','Réception commande',            '2025-02-05 08:40:00+00'),
('d0000000-0000-0000-0000-000000000026','b0000000-0000-0000-0000-000000000002','sortie', 34,100, 66,'V-2025-2601','Ventes période',                 '2025-08-24 13:10:00+00'),
('d0000000-0000-0000-0000-000000000027','b0000000-0000-0000-0000-000000000002','entree',110,  0,110,'F-2025-0010','Réception commande',            '2025-03-12 10:30:00+00'),
('d0000000-0000-0000-0000-000000000027','b0000000-0000-0000-0000-000000000002','sortie', 25,110, 85,'V-2025-2701','Ventes période',                 '2025-08-18 11:40:00+00'),
-- PSYCHOTROPES
('d0000000-0000-0000-0000-000000000028','b0000000-0000-0000-0000-000000000002','entree', 60,  0, 60,'F-2024-0030','Réception commande',            '2024-10-20 09:00:00+00'),
('d0000000-0000-0000-0000-000000000028','b0000000-0000-0000-0000-000000000002','sortie', 30, 60, 30,'V-2025-2801','Ventes période',                 '2025-08-15 15:30:00+00'),
('d0000000-0000-0000-0000-000000000029','b0000000-0000-0000-0000-000000000002','entree', 60,  0, 60,'F-2024-0031','Réception commande',            '2024-10-01 09:00:00+00'),
('d0000000-0000-0000-0000-000000000029','b0000000-0000-0000-0000-000000000002','sortie', 48, 60, 12,'V-2025-2901','Ventes période',                 '2025-08-27 17:00:00+00'),
('d0000000-0000-0000-0000-000000000030','b0000000-0000-0000-0000-000000000002','entree', 40,  0, 40,'F-2024-0029','Réception commande',            '2024-09-25 09:00:00+00'),
('d0000000-0000-0000-0000-000000000030','b0000000-0000-0000-0000-000000000002','sortie', 20, 40, 20,'V-2025-3001','Ventes période',                 '2025-08-20 14:00:00+00'),
('d0000000-0000-0000-0000-000000000031','b0000000-0000-0000-0000-000000000002','entree', 56,  0, 56,'F-2024-0032','Réception commande',            '2024-11-15 09:00:00+00'),
('d0000000-0000-0000-0000-000000000031','b0000000-0000-0000-0000-000000000002','sortie', 12, 56, 44,'V-2025-3101','Ventes période',                 '2025-08-25 15:30:00+00'),
-- EXPIRED LOT
('d0000000-0000-0000-0000-000000000032','b0000000-0000-0000-0000-000000000002','entree', 50,  0, 50,'F-2023-0009','Réception commande',            '2023-06-01 09:00:00+00'),
('d0000000-0000-0000-0000-000000000032','b0000000-0000-0000-0000-000000000002','sortie', 16, 50, 34,'V-2024-3201','Ventes période',                 '2024-04-10 14:00:00+00');

-- ---------------------------------------------------------------------------
-- ALERTES  (must respect the partial unique index: one open per lot+type)
-- expirations (near) + expired lot + stock alerts for low stock lots.
-- ---------------------------------------------------------------------------
INSERT INTO alertes (lot_id, type, niveau, message, donnees_concernees, est_lue) VALUES
-- Expiration (near): lot 3 (2026-09), lot 5 (2026-05), lot 25 (2026-06), lot 22 (2026-08)
('d0000000-0000-0000-0000-000000000003','expiration','moyen',  'Lot PBG-A-001 — péremption le 2026-09-01 (proche).',  '{"date_peremption":"2026-09-01","medicament":"paracétamol"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000005','expiration','moyen',  'Lot EFF-A-001 — péremption le 2026-05-01 (proche).',  '{"date_peremption":"2026-05-01","medicament":"paracétamol"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000025','expiration','moyen',  'Lot IMO-A-001 — péremption le 2026-06-01 (proche).',  '{"date_peremption":"2026-06-01","medicament":"lopéramide"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000022','expiration','moyen',  'Lot VEN-A-001 — péremption le 2026-08-01 (à surveiller).','{"date_peremption":"2026-08-01","medicament":"salbutamol"}'::jsonb, FALSE),
-- Expired lot
('d0000000-0000-0000-0000-000000000032','expiration','critique','Lot BET-A-001 — PÉRIMÉ le 2025-05-01 (ne doit plus être vendu).','{"date_peremption":"2025-05-01","medicament":"povidone iodée"}'::jsonb, FALSE),
-- Stock alerts (below seuil_alerte)
('d0000000-0000-0000-0000-000000000004','stock','eleve',   'Stock faible : 15 restants (seuil 80) pour paracétamol Biogaran 1000mg.','{"quantite_actuelle":15,"seuil_alerte":80,"medicament":"paracétamol"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000006','stock','eleve',   'Stock faible : 35 restants (seuil 60) pour ibuprofène Biogaran 400mg.','{"quantite_actuelle":35,"seuil_alerte":60,"medicament":"ibuprofène"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000009','stock','critique','Stock critique : 18 restants (seuil 60) pour amoxicilline Biogaran 500mg.','{"quantite_actuelle":18,"seuil_alerte":60,"medicament":"amoxicilline"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000011','stock','eleve',   'Stock faible : 12 restants (seuil 30) pour Zithromax 250mg.','{"quantite_actuelle":12,"seuil_alerte":30,"medicament":"azithromycine"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000016','stock','moyen',   'Stock faible : 10 restants (seuil 25) pour Plavix 75mg.','{"quantite_actuelle":10,"seuil_alerte":25,"medicament":"clopidogrel"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000021','stock','eleve',   'Stock faible : 30 restants (seuil 60) pour Levothyrox 50µg.','{"quantite_actuelle":30,"seuil_alerte":60,"medicament":"lévothyroxine"}'::jsonb, FALSE),
('d0000000-0000-0000-0000-000000000029','stock','moyen',   'Stock faible : 12 restants (seuil 20) pour Xanax 0,25mg (psychotrope).','{"quantite_actuelle":12,"seuil_alerte":20,"medicament":"alprazolam"}'::jsonb, FALSE);

COMMIT;
