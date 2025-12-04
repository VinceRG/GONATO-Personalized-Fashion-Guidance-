<?php
// app/Helpers/Csrf.php

class Csrf
{
    public static function ensureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Generate or get existing token
    public static function getToken(): string
    {
        self::ensureSession();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    // Validate and (optionally) rotate token
    public static function validate(?string $token): bool
    {
        self::ensureSession();

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        $isValid = hash_equals($_SESSION['csrf_token'], $token);

        // Optional: rotate token after successful use
        if ($isValid) {
            unset($_SESSION['csrf_token']);
        }

        return $isValid;
    }
}
