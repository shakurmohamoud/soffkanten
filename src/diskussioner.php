<?php

/**
 * Allt som rör diskussioner
 * Ämnen och inlägg i grupperna
 */

require_once __DIR__ . '/db.php';

/**
 * Hämtar ämnena i en grupp med namnet på den som startade
 * Nyaste först
 */
function gruppensAmnen(int $gruppId): array
{
    $sats = hamtaDatabas()->prepare(
        'SELECT t.id, t.title, t.created_at, u.first_name, u.last_name
         FROM topics t
         JOIN users u ON u.id = t.user_id
         WHERE t.group_id = ?
         ORDER BY t.created_at DESC'
    );

    $sats->execute([$gruppId]);

    return $sats->fetchAll();
}

/**
 * Skapar ett ämne med ett första inlägg
 * Båda sparas tillsammans så att ett ämne aldrig blir tomt
 * Ger tillbaka ämnets id
 */
function skapaAmne(int $gruppId, int $anvandarId, string $rubrik, string $text): int
{
    $pdo = hamtaDatabas();

    $pdo->beginTransaction();

    try {
        $sats = $pdo->prepare(
            'INSERT INTO topics (group_id, user_id, title)
             VALUES (?, ?, ?)'
        );

        $sats->execute([$gruppId, $anvandarId, $rubrik]);

        $amneId = (int) $pdo->lastInsertId();

        $sats = $pdo->prepare(
            'INSERT INTO posts (topic_id, user_id, body)
             VALUES (?, ?, ?)'
        );

        $sats->execute([$amneId, $anvandarId, $text]);

        $pdo->commit();

        return $amneId;
    } catch (PDOException $e) {
        $pdo->rollBack();

        throw $e;
    }
}

/**
 * Hämtar ett ämne
 * Ger tillbaka null om ämnet inte finns
 */
function hamtaAmne(int $amneId): ?array
{
    $sats = hamtaDatabas()->prepare(
        'SELECT id, group_id, user_id, title, created_at
         FROM topics
         WHERE id = ?'
    );

    $sats->execute([$amneId]);

    return $sats->fetch() ?: null;
}

/**
 * Hämtar inläggen under ett ämne med namnet på den som skrev
 * Äldsta först så att samtalet läses uppifrån och ner
 */
function amnetsInlagg(int $amneId): array
{
    $sats = hamtaDatabas()->prepare(
        'SELECT p.id, p.body, p.created_at, u.first_name, u.last_name
         FROM posts p
         JOIN users u ON u.id = p.user_id
         WHERE p.topic_id = ?
         ORDER BY p.created_at'
    );

    $sats->execute([$amneId]);

    return $sats->fetchAll();
}

/**
 * Sparar ett svar under ett ämne
 */
function skapaInlagg(int $amneId, int $anvandarId, string $text): void
{
    $sats = hamtaDatabas()->prepare(
        'INSERT INTO posts (topic_id, user_id, body)
         VALUES (?, ?, ?)'
    );

    $sats->execute([$amneId, $anvandarId, $text]);
}