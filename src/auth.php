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

/**
 * Kontrollerar e-post och lösenord.
 * Returnerar användarens id vid rätt uppgifter, annars null.
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
 * Returnerar id på inloggad användare, eller null om ingen är inloggad.
 */
function inloggadAnvandarId(): ?int
{
    startaSession();

    return $_SESSION['anvandar_id'] ?? null;
}

/**
 * Hämtar uppgifter om den inloggade användaren.
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