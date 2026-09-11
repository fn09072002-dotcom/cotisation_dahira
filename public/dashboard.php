<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Auth;
use App\Models\Membre;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

if (!Auth::check()) {
    header('Location: /login.php');
    exit;
}

$user = Auth::user();
$isAdmin = Auth::isAdmin();

if ($isAdmin) {
    $stats = Membre::getDashboardStats();
    $soldes = Membre::getAllSoldes();
    $statutFixe = Membre::getStatutHebdoFixe();
} else {
    $monSolde = Membre::getSolde($user['membre_id']);
    $autres = Membre::getPaiementsPublics();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/../app/Views/navbar.php'; ?>
<div class="container">
    <h2>Bonjour, <?= htmlspecialchars($user['prenom']) ?></h2>
    <?php if ($isAdmin): ?>
    <a href="/membres.php" style="margin-right:1rem;">Gérer les membres</a>
    <a href="/paiement.php" style="margin-right:1rem;">Enregistrer un paiement</a>
    <a href="/historique.php" style="margin-right:1rem;">Mon historique</a>
<?php endif; ?>

    <?php if ($isAdmin): ?>
        <div class="stats">
            <div class="stat-card"><h3><?= $stats['total_membres'] ?></h3><p>Membres</p></div>
            <div class="stat-card"><h3><?= number_format($stats['total_encaisse'], 0, ',', ' ') ?> F</h3><p>Total encaissé</p></div>
            <div class="stat-card"><h3><?= number_format($stats['encaisse_mois_courant'], 0, ',', ' ') ?> F</h3><p>Ce mois-ci</p></div>
        </div>
        <h3>Soldes des membres</h3>
        <table>
            <tr><th>Membre</th><th>Catégorie</th><th>Versé (année)</th><th>Reste à verser</th></tr>
            <?php foreach ($soldes as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['membre']) ?></td>
                <td><?= htmlspecialchars($s['categorie']) ?></td>
                <td><?= number_format($s['total_verse_annee'], 0, ',', ' ') ?> F</td>
                <td><?= $s['reste_a_verser'] !== null ? number_format($s['reste_a_verser'], 0, ',', ' ') . ' F' : '—' ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
                <?php if (!empty($statutFixe)): ?>
        <h3>Groupe cotisation fixe (200F/vendredi)</h3>
        <table>
            <tr><th>Membre</th><th>Cette semaine</th><th>Cumul année</th></tr>
            <?php foreach ($statutFixe as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['membre']) ?></td>
                <td><?= $s['paye_cette_semaine'] ? '✅ Payé' : '❌ Pas payé' ?></td>
                <td><?= number_format($s['cumul_annee'], 0, ',', ' ') ?> F</td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    <?php else: ?>
        <h3>Mon profil</h3>
        <table>
            <tr><th>Catégorie</th><td><?= htmlspecialchars($monSolde['categorie']) ?></td></tr>
            <tr><th>Versé cette année</th><td><?= number_format($monSolde['total_verse_annee'], 0, ',', ' ') ?> F</td></tr>
            <?php if ($monSolde['reste_a_verser'] !== null): ?>
            <tr><th>Reste à verser</th><td><?= number_format($monSolde['reste_a_verser'], 0, ',', ' ') ?> F</td></tr>
            <?php endif; ?>
        </table>

        <h3>Montants versés par les autres membres</h3>
        <table>
            <tr><th>Membre</th><th>Total versé (année)</th></tr>
            <?php foreach ($autres as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['membre']) ?></td>
                <td><?= number_format($a['total_verse_annee'], 0, ',', ' ') ?> F</td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
