<?php

/**
 * Toppen av varje sida - logotyp och meny
 * Sidan sätter $sidtitel innan den laddar in den här filen
 */

require_once __DIR__ . '/auth.php';

$inloggad = inloggadAnvandare();
$titel = $sidtitel ?? 'Soffkanten';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Soffkanten är ett forum där du går med i grupper och pratar om det du är intresserad av.">
    <title><?= htmlspecialchars($titel) ?> - Soffkanten</title>
    <link rel="stylesheet" href="stil.css">
</head>
<body>
    <header>
        <a href="index.php" class="logotyp">Soffkanten</a>

        <nav>
            <?php if ($inloggad): ?>
                <a href="grupper.php">Alla grupper</a>
                <a href="mina-grupper.php">Mina grupper</a>
                <a href="skapa-grupp.php">Skapa grupp</a>
                <a href="logga-ut.php">Logga ut</a>
            <?php else: ?>
                <a href="logga-in.php">Logga in</a>
                <a href="registrera.php">Skapa konto</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>