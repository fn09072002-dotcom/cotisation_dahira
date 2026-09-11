<?php
use App\Core\Auth;
$user = Auth::user();
$isAdmin = Auth::isAdmin();
$config = require __DIR__ . '/../../config/app.php';
?>
<div class="navbar">
    <div class="brand">
        <?= htmlspecialchars($config['nom']) ?>
        <span style="font-size:0.75rem; font-weight:400; opacity:0.8;">— <?= htmlspecialchars($config['annee']) ?></span>
    </div>
    <nav>
        <a href="/dashboard.php">Tableau de bord</a>
        <a href="/historique.php">Mon historique</a>
        <a href="/historique_global.php">Tous les paiements</a>
        <?php if ($isAdmin): ?>
            <a href="/membres.php">Membres</a>
            <a href="/paiement.php">Enregistrer paiement</a>
        <?php endif; ?>
        <a href="/logout.php">Déconnexion (<?= htmlspecialchars($user['prenom'] ?? '') ?>)</a>
    </nav>
</div>