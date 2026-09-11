<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Models\Membre;
use App\Core\Auth;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
$config = require __DIR__ . '/../config/app.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    $membre = Membre::findByIdentifiant($identifiant);

    if ($membre && password_verify($motDePasse, $membre['mot_de_passe'])) {
        Auth::login($membre);
        header('Location: /dashboard.php');
        exit;
    } else {
        $erreur = "Identifiant ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DAHIRA AKHYAAR - Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-logo">🕌</div>
            <h1>DAHIRA AKHYAAR</h1>
            <h1><?= htmlspecialchars($config['nom']) ?></h1>
<p class="subtitle">Gestion des cotisations — Année <?= htmlspecialchars($config['annee']) ?></p>
            <?php if ($erreur): ?>
                <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>Identifiant</label>
                <input type="text" name="identifiant" placeholder="prenom.nom" required autofocus>

                <label>Mot de passe</label>
                <input type="password" name="mot_de_passe" placeholder="••••••••" required>

                <button type="submit">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>