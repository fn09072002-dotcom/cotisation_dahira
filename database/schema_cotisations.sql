CREATE DATABASE IF NOT EXISTS gestion_cotisations;
USE gestion_cotisations;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    type ENUM('objectif_annuel', 'fixe_hebdo') NOT NULL,
    montant_cible DECIMAL(10,2) NULL,
    montant_fixe DECIMAL(10,2) NULL
);

CREATE TABLE membres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    categorie_id INT NOT NULL,
    identifiant VARCHAR(150) UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'membre') NOT NULL DEFAULT 'membre',
    date_adhesion DATE DEFAULT (CURRENT_DATE),
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    membre_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    date_paiement DATE NOT NULL,
    annee INT GENERATED ALWAYS AS (YEAR(date_paiement)) STORED,
    mois INT GENERATED ALWAYS AS (MONTH(date_paiement)) STORED,
    FOREIGN KEY (membre_id) REFERENCES membres(id)
);

CREATE VIEW solde_membre AS
SELECT
    m.id AS membre_id,
    CONCAT(m.prenom, ' ', m.nom) AS membre,
    c.nom AS categorie,
    c.type,
    c.montant_cible,
    c.montant_fixe,
    COALESCE(SUM(CASE WHEN p.annee = YEAR(CURDATE()) THEN p.montant ELSE 0 END), 0) AS total_verse_annee,
    CASE
        WHEN c.type = 'objectif_annuel'
            THEN c.montant_cible - COALESCE(SUM(CASE WHEN p.annee = YEAR(CURDATE()) THEN p.montant ELSE 0 END), 0)
        ELSE NULL
    END AS reste_a_verser
FROM membres m
JOIN categories c ON m.categorie_id = c.id
LEFT JOIN paiements p ON p.membre_id = m.id
GROUP BY m.id, c.id;

CREATE VIEW dashboard_stats AS
SELECT
    (SELECT COUNT(*) FROM membres WHERE actif = TRUE) AS total_membres,
    (SELECT COALESCE(SUM(montant), 0) FROM paiements) AS total_encaisse,
    (SELECT COALESCE(SUM(montant), 0) FROM paiements
        WHERE MONTH(date_paiement) = MONTH(CURDATE())
        AND YEAR(date_paiement) = YEAR(CURDATE())) AS encaisse_mois_courant;

INSERT INTO categories (nom, type, montant_cible, montant_fixe) VALUES
('Garçons 40000', 'objectif_annuel', 40000, NULL),
('Filles 20000',  'objectif_annuel', 20000, NULL),
('Garçons 20000', 'objectif_annuel', 20000, NULL),
('Jeunes 10000',  'objectif_annuel', 10000, NULL),
('Fixe 200',      'fixe_hebdo',      NULL,  200);
