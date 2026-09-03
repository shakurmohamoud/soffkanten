<?php

/**
 * Användare och inloggning.
 * Hanterar registrering, lösenordshashning och sessioner.
 */

require_once __DIR__ . '/db.php';

/**
 * Startar sessionen om den inte redan är igång.
 */
function startaSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Kontrollerar om e-postadressen redan finns i databasen.
 */
function epostFinns(string $epost): bool
{
    $sats = hamtaDatabas()->prepare('SELECT id FROM users WHERE email = ?');
    $sats->execute([$epost]);

    return $sats->fetch() !== false;
}

/**
 * Skapar en ny användare och returnerar dess id.
 */
function skapaAnvandare(
    string $fornamn,
    string $efternamn,
    string $epost,
    string $losenord
): int {
    $hash = password_hash($losenord, PASSWORD_DEFAULT);

    $sats = hamtaDatabas()->prepare(
        'INSERT INTO users (first_name, last_name, email, password_hash)
         VALUES (?, ?, ?, ?)'
    );

    $sats->execute([$fornamn, $efternamn, $epost, $hash]);

    return (int) hamtaDatabas()->lastInsertId();
}