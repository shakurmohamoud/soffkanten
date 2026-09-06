<?php

/**
 * Allt som rör inbjudningslänkar
 * Skapa länk, kolla att den gäller och lösa in den
 */

require_once __DIR__ . '/db.php';

/**
 * Skapar en inbjudningslänk till en gruppen
 * Länken gäller i 24 timmar och kan bara användas en gång
 * Ger tillbaka den slumpade nyckeln
 */
function skapaInbjudan(int $gruppId, int $adminId): string
{
    $nyckel = bin2hex(random_bytes(32));

    $sats = hamtaDatabas()->prepare(
        'INSERT INTO invites (group_id, created_by, token, expires_at)
         VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))'
    );

    $sats->execute([$gruppId, $adminId, $nyckel]);

    return $nyckel;
}

/**
 * Hämtar en inbjudan som fortfarande gäller
 * Ger tillbaka null om nyckeln är fel, använd eller för gammal
 */
function giltigInbjudan(string $nyckel): ?array
{
    $sats = hamtaDatabas()->prepare(
        'SELECT i.id, i.group_id, g.name
         FROM invites i
         JOIN forum_groups g ON g.id = i.group_id
         WHERE i.token = ? AND i.used = 0 AND i.expires_at > NOW()'
    );

    $sats->execute([$nyckel]);

    return $sats->fetch() ?: null;
}

/**
 * Löser in en inbjudan och gör användaren till medlem
 * Nyckeln märks som använd i samma transaktion
 * Ger tillbaka gruppens id, eller null om inbjudan inte gäller
 */
function loesInInbjudan(string $nyckel, int $anvandarId): ?int
{
    $pdo = hamtaDatabas();

    $pdo->beginTransaction();

    try {
        // Satsen ändrar bara raden om nyckeln fortfarande är oanvänd
        // Två personer som klickar samtidigt kan alltså inte båda komma in
        $sats = $pdo->prepare(
            'UPDATE invites
             SET used = 1
             WHERE token = ? AND used = 0 AND expires_at > NOW()'
        );

        $sats->execute([$nyckel]);

        if ($sats->rowCount() === 0) {
            $pdo->rollBack();

            return null;
        }

        $sats = $pdo->prepare('SELECT group_id FROM invites WHERE token = ?');
        $sats->execute([$nyckel]);

        $gruppId = (int) $sats->fetch()['group_id'];

        $sats = $pdo->prepare(
            'INSERT INTO group_members (group_id, user_id, status, role)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status = ?'
        );

        $sats->execute([$gruppId, $anvandarId, 'member', 'member', 'member']);

        $pdo->commit();

        return $gruppId;
    } catch (PDOException $e) {
        $pdo->rollBack();

        throw $e;
    }
}