<?php

namespace App\Core;

class Auth
{
    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $membre): void
    {
        self::ensureSession();
        $_SESSION['membre_id'] = $membre['id'];
        $_SESSION['identifiant'] = $membre['identifiant'];
        $_SESSION['role'] = $membre['role'];
        $_SESSION['nom'] = $membre['nom'];
        $_SESSION['prenom'] = $membre['prenom'];
    }

    public static function check(): bool
    {
        self::ensureSession();
        return isset($_SESSION['membre_id']);
    }

    public static function isAdmin(): bool
    {
        self::ensureSession();
        return ($_SESSION['role'] ?? null) === 'admin';
    }

    public static function user(): ?array
    {
        self::ensureSession();
        return isset($_SESSION['membre_id']) ? $_SESSION : null;
    }

    public static function logout(): void
    {
        self::ensureSession();
        session_unset();
        session_destroy();
    }
}
