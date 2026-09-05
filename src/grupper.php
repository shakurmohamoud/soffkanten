<?php

/**
 * Allt som rör grupper
 * Skapa grupper, hämta listor och kolla medlemskap
 */

require_once __DIR__ . '/db.php';

/**
 * Skapar en grupp och gör den som skapade den till admin
 * Ger tillbaka gruppens id
 */
function skapaGrupp(string $namn, string $beskrivning, int $anvandarId): int
{
    $pdo = hamtaDatabas();

    $pdo->beginTransaction();

    try {
        $sats = $pdo->prepare(
            'INSERT INTO forum_groups (name, description, created_by)
             VALUES (?, ?, ?)'
        );

        $sats->execute([$namn, $beskrivning, $anvandarId]);

        $gruppId = (int) $pdo->lastInsertId();

        $sats = $pdo->prepare(
            'INSERT INTO group_members (group_id, user_id, status, role)
             VALUES (?, ?, ?, ?)'
        );

        $sats->execute([$gruppId, $anvandarId, 'member', 'admin']);

        $pdo->commit();

        return $gruppId;
    } catch (PDOException $e) {
        $pdo->rollBack();

        throw $e;
    }
}

/**
 * Hämtar grupperna som användaren är medlem i
 */
function minaGrupper(int $anvandarId): array
{
    $sats = hamtaDatabas()->prepare(
        'SELECT g.id, g.name, g.description, m.role
         FROM forum_groups g
         JOIN group_members m ON m.group_id = g.id
         WHERE m.user_id = ? AND m.status = ?
         ORDER BY g.name'
    );

    $sats->execute([$anvandarId, 'member']);

    return $sats->fetchAll();
}

/**
 * Hämtar grupperna som användaren inte är med i
 * Tar med om det redan finns en ansökan som väntar
 */
function ovrigaGrupper(int $anvandarId): array
{
    $sats = hamtaDatabas()->prepare(
        'SELECT g.id, g.name, g.description, m.status AS ansokan
         FROM forum_groups g
         LEFT JOIN group_members m
           ON m.group_id = g.id AND m.user_id = ?
         WHERE m.status IS NULL OR m.status = ?
         ORDER BY g.name'
    );

    $sats->execute([$anvandarId, 'pending']);

    return $sats->fetchAll();
}

/**
 * Hämtar en grupp
 * Ger tillbaka null om gruppen inte finns
 */
function hamtaGrupp(int $gruppId): ?array
{
    $sats = hamtaDatabas()->prepare(
        'SELECT id, name, description, created_by
         FROM forum_groups
         WHERE id = ?'
    );

    $sats->execute([$gruppId]);

    return $sats->fetch() ?: null;
}

/**
 * Kollar vilken roll användaren har i gruppen
 * Ger tillbaka member, admin eller null om hen inte är medlem
 */
function rollIGrupp(int $gruppId, int $anvandarId): ?string
{
    $sats = hamtaDatabas()->prepare(
        'SELECT role FROM group_members
         WHERE group_id = ? AND user_id = ? AND status = ?'
    );

    $sats->execute([$gruppId, $anvandarId, 'member']);

    $rad = $sats->fetch();

    return $rad ? $rad['role'] : null;
}

/**
 * Skickar in en ansökan om att gå med i en grupp
 * Unik regel i databasen gör att en dubblett hoppas över
 */
function ansokOmMedlemskap(int $gruppId, int $anvandarId): void
{
    $sats = hamtaDatabas()->prepare(
        'INSERT IGNORE INTO group_members (group_id, user_id, status, role)
         VALUES (?, ?, ?, ?)'
    );

    $sats->execute([$gruppId, $anvandarId, 'pending', 'member']);
}

/**
 * Hämtar väntande ansökningar till en grupp
 */
function vantandeAnsokningar(int $gruppId): array
{
    $sats = hamtaDatabas()->prepare(
        'SELECT u.id, u.first_name, u.last_name
         FROM group_members m
         JOIN users u ON u.id = m.user_id
         WHERE m.group_id = ? AND m.status = ?
         ORDER BY u.first_name'
    );

    $sats->execute([$gruppId, 'pending']);

    return $sats->fetchAll();
}

/**
 * Godkänner en ansökan
 * Kopplingen till adm gör att raden bara ändras om den som godkänner är admin i gruppen
 */
function godkannAnsokan(int $gruppId, int $anvandarId, int $adminId): void
{
    $sats = hamtaDatabas()->prepare(
        'UPDATE group_members AS ansokan
         JOIN group_members AS adm
           ON adm.group_id = ansokan.group_id
          AND adm.user_id = ?
          AND adm.status = ?
          AND adm.role = ?
         SET ansokan.status = ?
         WHERE ansokan.group_id = ?
           AND ansokan.user_id = ?
           AND ansokan.status = ?'
    );

    $sats->execute([
        $adminId,
        'member',
        'admin',
        'member',
        $gruppId,
        $anvandarId,
        'pending',
    ]);
}