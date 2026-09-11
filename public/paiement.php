<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Auth;
use App\Models\Membre;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

if (!Auth::check() || !Auth::isAdmin()) {
    header('Location: /login.php');
    exit;
}

$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membreId = (int)($_POST['membre_id'] ?? 0);
    $montant = (float)($_POST['montant'] ?? 0);
    $date = $_POST['date_paiement'] ?? '';

    if ($membreId <= 0 || $montant <= 0 || empty($date)) {
        $erreur = "Merci de remplir tous les champs correctement.";
    } else {
        $jourSemaine = date('N', strtotime($date)); // 5 = vendredi
        if ($jourSemaine != 5) {
            $erreur = "La date choisie n'est pas un vendredi. Vérifiez la date.";
        } else {
            Membre::addPaiement($membreId, $montant, $date);
            $message = "Paiement enregistré avec succès !";
        }
    }
}

$membres = Membre::getAllActifs();

// Calcul du prochain vendredi par défaut
$prochainVendredi = date('Y-m-d', strtotime('this friday'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrer un paiement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 1rem; background: #f9f9f9; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .box { background: white; padding: 1.5rem; border-radius: 8px; max-width: 400px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        select, input { width: 100%; padding: 0.6rem; margin: 0.5rem 0; box-sizing: border-box; }
        button { width: 100%; padding: 0.7rem; background: #2c7a4b; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .message { color: green; }
        .erreur { color: red; }
        a { color: #2c7a4b; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Enregistrer un paiement</h2>
        <a href="/dashboard.php">← Retour au tableau de bord</a>
    </div>

    <div class="box">
        <?php if ($message): ?><p class="message"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <?php if ($erreur): ?><p class="erreur"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

        <form method="POST">
            <label>Membre</label>
            <select name="membre_id" required>
                <option value="">-- Choisir un membre --</option>
                <?php foreach ($membres as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nom_complet']) ?> (<?= htmlspecialchars($m['categorie']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <label>Montant (F)</label>
            <input type="number" name="montant" min="1" step="1" required>

            <label>Date (vendredi)</label>
            <input type="date" name="date_paiement" value="<?= $prochainVendredi ?>" required>

            <button type="submit">Enregistrer le paiement</button>
        </form>
    </div>
</body>
</html>
