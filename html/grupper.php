<?php

require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/grupper.php';

$anvandarId = inloggadAnvandarId();

if ($anvandarId === null) {
    header('Location: logga-in.php');
    exit;
}

$grupper = ovrigaGrupper($anvandarId);

$sidtitel = 'Alla grupper';

require __DIR__ . '/../src/header.php';
?>

<h1>Grupper att gå med i</h1>

<?php if (!$grupper): ?>
    <p>Det finns inga fler grupper att gå med i just nu.</p>
    <p><a href="skapa-grupp.php">Skapa en egen grupp</a>.</p>
<?php else: ?>
    <ul class="grupplista">
        <?php foreach ($grupper as $grupp): ?>
            <li>
                <h2><?= htmlspecialchars($grupp['name']) ?></h2>

                <p><?= htmlspecialchars($grupp['description']) ?></p>

                <?php if ($grupp['ansokan'] === 'pending'): ?>
                    <p class="markering">Din ansökan väntar på svar</p>
                <?php else: ?>
                    <p>Ansökningsknapp kommer imorgon.</p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/../src/footer.php'; ?>