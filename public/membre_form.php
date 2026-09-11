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

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$membre = $id ? Membre::find($id) : null;
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $categorieId = (int)($_POST['categorie_id'] ?? 0);
    $role = $_POST['role'] ?? 'membre';

    if ($prenom === '' || $categorieId <= 0) {
        $erreur = "Le prénom et la catégorie sont obligatoires.";
    } elseif ($id) {
        Membre::update($id, $nom, $prenom, $categorieId, $role);
        header('Location: /membres.php');
        exit;
    } else {
        $motDePasse = $_POST['mot_de_passe'] ?? 'cotisation123';
        Membre::create($nom, $prenom, $categorieId, $motDePasse, $role);
        header('Location: /membres.php');
        exit;
    }
}

$categories = Membre::getCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Modifier' : 'Ajouter' ?> un membre</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 1rem; background: #f9f9f9; }
        .box { background: white; padding: 1.5rem; border-radius: 8px; max-width: 400px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        input, select { width: 100%; padding: 0.6rem; margin: 0.5rem 0; box-sizing: border-box; }
        button { width: 100%; padding: 0.7rem; background: #2c7a4b; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .erreur { color: red; }
        a { color: #2c7a4b; text-decoration: none; }
    </style>
</head>
<body>
    <p><a href="/membres.php">← Retour à la liste</a></p>
    <div class="box">
        <h2><?= $id ? 'Modifier' : 'Ajouter' ?> un membre</h2>
        <?php if ($erreur): ?><p class="erreur"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

        <form method="POST">
            <label>Prénom</label>
            <input type="text" name="prenom" value="<?= htmlspecialchars($membre['prenom'] ?? '') ?>" required>

            <label>Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($membre['nom'] ?? '') ?>">

            <label>Catégorie</label>
            <select name="categorie_id" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (isset($membre['categorie_id']) && $membre['categorie_id'] == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Rôle</label>
            <select name="role">
                <option value="membre" <?= (($membre['role'] ?? '') === 'membre') ? 'selected' : '' ?>>Membre</option>
                <option value="admin" <?= (($membre['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>

            <?php if (!$id): ?>
            <label>Mot de passe initial</label>
            <input type="text" name="mot_de_passe" value="cotisation123">
            <?php endif; ?>

            <button type="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>
