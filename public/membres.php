<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Auth;
use App\Models\Membre;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if (!Auth::check() || !Auth::isAdmin()) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    Membre::toggleActif((int)$_POST['toggle_id']);
    header('Location: /membres.php');
    exit;
}

$membres = Membre::getAll();
$config = require __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($config['nom']) ?> - Gestion des membres</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../app/Views/navbar.php'; ?>
    <div class="container">
        <div class="header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:0.5rem;">
            <h2>Gestion des membres</h2>
            <a class="btn" href="/membre_form.php">+ Ajouter un membre</a>
        </div>

        <table>
            <tr>
                <th>Nom</th>
                <th>Identifiant</th>
                <th>Catégorie</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($membres as $m): ?>
            <tr class="<?= $m['actif'] ? '' : 'inactif' ?>">
                <td><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></td>
                <td><?= htmlspecialchars($m['identifiant']) ?></td>
                <td><?= htmlspecialchars($m['categorie']) ?></td>
                <td><?= htmlspecialchars($m['role']) ?></td>
                <td><?= $m['actif'] ? 'Actif' : 'Inactif' ?></td>
                <td>
                    <a class="btn" href="/membre_form.php?id=<?= $m['id'] ?>" style="padding:0.3rem 0.6rem; font-size:0.8rem;">Modifier</a>
                    <a class="btn" href="/historique.php?id=<?= $m['id'] ?>" style="padding:0.3rem 0.6rem; font-size:0.8rem;">Historique</a>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="toggle_id" value="<?= $m['id'] ?>">
                        <button class="btn danger" style="padding:0.3rem 0.6rem; font-size:0.8rem;" onclick="return confirm('Confirmer ?')">
                            <?= $m['actif'] ? 'Désactiver' : 'Réactiver' ?>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>