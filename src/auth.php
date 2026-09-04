<?php

/**
 * Allt som rör användare och inloggning
 * Registrering, lösenord och sessioner
 */

require_once __DIR__ . '/db.php';

/**
 * Startar sessionen om den inte redan är igång
 */
function startaSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Kollar om e-postadressen redan är upptagen
 */
function epostFinns(string $epost): bool
{
    $sats = hamtaDatabas()->prepare('SELECT id FROM users WHERE email = ?');
    $sats->execute([$epost]);

    return $sats->fetch() !== false;
}

/**
 * Sparar en ny användare och ger tillbaka id:t
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

/**
 * Kollar e-post och lösenord
 * Ger tillbaka id:t om de stämmer, annars null
 */
function loggaIn(string $epost, string $losenord): ?int
{
    $sats = hamtaDatabas()->prepare(
        'SELECT id, password_hash FROM users WHERE email = ?'
    );

    $sats->execute([$epost]);
    $anvandare = $sats->fetch();

    if (!$anvandare) {
        return null;
    }

    if (!password_verify($losenord, $anvandare['password_hash'])) {
        return null;
    }

    return (int) $anvandare['id'];
}

/**
 * Ger tillbaka id:t på den som är inloggad, annars null
 */
function inloggadAnvandarId(): ?int
{
    startaSession();

    return $_SESSION['anvandar_id'] ?? null;
}

/**
 * Hämtar namn och e-post för den som är inloggad
 */
function inloggadAnvandare(): ?array
{
    $id = inloggadAnvandarId();

    if ($id === null) {
        return null;
    }

    $sats = hamtaDatabas()->prepare(
        'SELECT id, first_name, last_name, email FROM users WHERE id = ?'
    );

    $sats->execute([$id]);

    return $sats->fetch() ?: null;
}