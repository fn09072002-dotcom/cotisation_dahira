<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use App\Core\Database;

// Test rapide de connexion
try {
    $db = Database::getConnection();
    echo "Connexion à la base réussie !";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}