<?php

require __DIR__ . '/../src/auth.php';

$anvandare = inloggadAnvandare();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Soffkanten</title>
</head>
<body>
    <h1>Soffkanten</h1>

    <?php if ($anvandare): ?>
        <p>Inloggad som
           <?= htmlspecialchars($anvandare['first_name']) ?>
           <?= htmlspecialchars($anvandare['last_name']) ?>
        </p>
    <?php else: ?>
        <p>Du är inte inloggad.</p>
        <p>
            <a href="logga-in.php">Logga in</a> eller
            <a href="registrera.php">skapa konto</a>
        </p>
    <?php endif; ?>
</body>
</html>