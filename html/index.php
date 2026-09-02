<?php

require __DIR__ . '/../src/db.php';

try {
    $pdo = hamtaDatabas();
    $tabeller = $pdo->query('SHOW TABLES')->fetchAll();

    echo '<h1>Databasen svarar</h1>';
    echo '<p>Antal tabeller: ' . count($tabeller) . '</p>';
} catch (PDOException $e) {
    echo '<h1>Kunde inte ansluta</h1>';
    echo '<pre>' . $e->getMessage() . '</pre>';
}