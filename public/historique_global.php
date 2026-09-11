<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Auth;
use App\Models\Membre;
use App\Core\Database;


$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if (!Auth::check() || !Auth::isAdmin()) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    Membre::deletePaiement((int)$_POST['delete_id']);
    header('Location: /historique_global.php');
    exit;
}

$db = Database::getConnection();
$stmt = $db->query("
    SELECT p.id, p.montant, p.date_paiement, CONCAT(m.prenom, ' ', m.nom) AS membre, m.id AS membre_id
    FROM paiements p
    JOIN membres m ON p.membre_id = m.id
    ORDER BY p.date_paiement DESC, membre
");
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
$config = require __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($config['nom']) ?> - Tous les paiements</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../app/Views/navbar.php'; ?>
    <div class="container">
        <h2>Historique de tous les paiements</h2>

        <?php if (empty($paiements)): ?>
            <p>Aucun paiement enregistré pour l'instant.</p>
        <?php else: ?>
            <table>
                <tr><th>Date</th><th>Membre</th><th>Montant</th><th>Action</th></tr>
                <?php foreach ($paiements as $p): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($p['date_paiement'])) ?></td>
                    <td><a href="/historique.php?id=<?= $p['membre_id'] ?>"><?= htmlspecialchars($p['membre']) ?></a></td>
                    <td><?= number_format($p['montant'], 0, ',', ' ') ?> F</td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Supprimer ce paiement ?');" style="display:inline">
                            <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn danger" style="padding:0.3rem 0.6rem; font-size:0.8rem;">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
