<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Membre
{
    public static function findByIdentifiant(string $identifiant): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM membres WHERE identifiant = :identifiant AND actif = 1");
        $stmt->execute(['identifiant' => $identifiant]);
        $membre = $stmt->fetch(PDO::FETCH_ASSOC);
        return $membre ?: null;
    }

    public static function getSolde(int $membreId): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM solde_membre WHERE membre_id = :id");
        $stmt->execute(['id' => $membreId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getAllSoldes(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM solde_membre ORDER BY membre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDashboardStats(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM dashboard_stats");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getPaiementsPublics(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT membre, total_verse_annee FROM solde_membre ORDER BY membre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllActifs(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT m.id, CONCAT(m.prenom, ' ', m.nom) AS nom_complet, c.nom AS categorie
            FROM membres m
            JOIN categories c ON m.categorie_id = c.id
            WHERE m.actif = 1
            ORDER BY nom_complet
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addPaiement(int $membreId, float $montant, string $date): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO paiements (membre_id, montant, date_paiement) VALUES (:membre_id, :montant, :date)");
        return $stmt->execute([
            'membre_id' => $membreId,
            'montant' => $montant,
            'date' => $date,
        ]);
    }

    public static function getStatutHebdoFixe(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT
                m.id AS membre_id,
                CONCAT(m.prenom, ' ', m.nom) AS membre,
                COALESCE(SUM(p.montant), 0) AS cumul_annee,
                MAX(CASE
                    WHEN p.date_paiement = (
                        SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 4 + 7) % 7 DAY)
                    ) THEN 1 ELSE 0
                END) AS paye_cette_semaine
            FROM membres m
            JOIN categories c ON m.categorie_id = c.id
            LEFT JOIN paiements p ON p.membre_id = m.id AND p.annee = YEAR(CURDATE())
            WHERE c.type = 'fixe_hebdo' AND m.actif = 1
            GROUP BY m.id
            ORDER BY membre
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT m.id, m.nom, m.prenom, m.identifiant, m.role, m.actif,
                   c.id AS categorie_id, c.nom AS categorie
            FROM membres m
            JOIN categories c ON m.categorie_id = c.id
            ORDER BY m.actif DESC, m.nom
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM membres WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getCategories(): array
    {
        $db = Database::getConnection();
        return $db->query("SELECT id, nom FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function identifiantExiste(string $identifiant, ?int $excludeId = null): bool
    {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM membres WHERE identifiant = :identifiant";
        $params = ['identifiant' => $identifiant];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function genererIdentifiant(string $prenom, string $nom): string
    {
        $base = strtolower(trim($prenom . '.' . $nom));
        $base = str_replace(' ', '', $base);
        $base = preg_replace('/[^a-z0-9.]/', '', $base);
        $identifiant = $base;
        $i = 1;
        while (self::identifiantExiste($identifiant)) {
            $i++;
            $identifiant = $base . $i;
        }
        return $identifiant;
    }

    public static function create(string $nom, string $prenom, int $categorieId, string $motDePasse, string $role = 'membre'): int
    {
        $db = Database::getConnection();
        $identifiant = self::genererIdentifiant($prenom, $nom);
        $hash = password_hash($motDePasse, PASSWORD_BCRYPT);

        $stmt = $db->prepare("INSERT INTO membres (nom, prenom, categorie_id, identifiant, mot_de_passe, role) VALUES (:nom, :prenom, :categorie_id, :identifiant, :mot_de_passe, :role)");
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'categorie_id' => $categorieId,
            'identifiant' => $identifiant,
            'mot_de_passe' => $hash,
            'role' => $role,
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, string $nom, string $prenom, int $categorieId, string $role): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE membres SET nom = :nom, prenom = :prenom, categorie_id = :categorie_id, role = :role WHERE id = :id");
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'categorie_id' => $categorieId,
            'role' => $role,
            'id' => $id,
        ]);
    }

    public static function toggleActif(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE membres SET actif = NOT actif WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

     public static function getHistoriquePaiements(int $membreId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, montant, date_paiement
            FROM paiements
            WHERE membre_id = :membre_id
            ORDER BY date_paiement DESC
        ");
        $stmt->execute(['membre_id' => $membreId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public static function deletePaiement(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM paiements WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
