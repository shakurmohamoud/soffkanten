<?php

require __DIR__ . '/../src/auth.php';

$sidtitel = 'Start';
$anvandare = inloggadAnvandare();

require __DIR__ . '/../src/header.php';
?>

<?php if ($anvandare): ?>
    <h1>Välkommen tillbaka, <?= htmlspecialchars($anvandare['first_name']) ?></h1>

    <p>
        Här är dina grupper och diskussioner. Gå till
        <a href="grupper.php">alla grupper</a> för att hitta nya
        att gå med i.
    </p>
<?php else: ?>
    <h1>Välkommen till Soffkanten</h1>

    <p>
        Ett forum där du går med i grupper och pratar om det
        som intresserar just dig.
    </p>

    <p>
        <a href="registrera.php">Skapa ett konto</a> eller
        <a href="logga-in.php">logga in</a> om du redan har ett.
    </p>
<?php endif; ?>

<?php require __DIR__ . '/../src/footer.php'; ?>