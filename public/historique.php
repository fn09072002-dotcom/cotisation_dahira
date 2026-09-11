<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Auth;
use App\Models\Membre;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if (!Auth::check()) {
    header('Location: /login.php');
    exit;
}

$user = Auth::user();
$isAdmin = Auth::isAdmin();

$membreId = isset($_GET['id']) ? (int)$_GET['id'] : (int)$user['membre_id'];
if (!$isAdmin && $membreId !== (int)$user['membre_id']) {
    $membreId = (int)$user['membre_id'];
}

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    Membre::deletePaiement((int)$_POST['delete_id']);
    header('Location: /historique.php?id=' . $membreId);
    exit;
}

$membre = Membre::find($membreId);
$historique = Membre::getHistoriquePaiements($membreId);
$total = array_sum(array_column($historique, 'montant'));
$config = require __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($config['nom']) ?> - Historique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../app/Views/navbar.php'; ?>
    <div class="container">
        <div class="header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2>Historique de <?= $membre ? htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) : 'membre introuvable' ?></h2>
            <?php if ($isAdmin): ?><a href="/historique_global.php">Voir tous les membres</a><?php endif; ?>
        </div>

        <?php if (!$membre): ?>
            <p class="erreur">Ce membre n'existe pas.</p>
        <?php elseif (empty($historique)): ?>
            <p>Aucun paiement enregistré pour l'instant.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Montant</th>
                    <?php if ($isAdmin): ?><th>Action</th><?php endif; ?>
                </tr>
                <?php foreach ($historique as $h): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($h['date_paiement'])) ?></td>
                    <td><?= number_format($h['montant'], 0, ',', ' ') ?> F</td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <form method="POST" onsubmit="return confirm('Supprimer ce paiement de <?= number_format($h['montant'], 0, ',', ' ') ?> F ?');" style="display:inline">
                            <input type="hidden" name="delete_id" value="<?= $h['id'] ?>">
                            <button type="submit" class="btn danger" style="padding:0.3rem 0.6rem; font-size:0.8rem;">Supprimer</button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>
            <p style="font-weight:700; margin-top:1rem;">Total versé : <?= number_format($total, 0, ',', ' ') ?> F</p>
        <?php endif; ?>
    </div>
</body>
</html>