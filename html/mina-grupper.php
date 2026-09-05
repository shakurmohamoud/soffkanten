<?php

require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/grupper.php';

$anvandarId = inloggadAnvandarId();

if ($anvandarId === null) {
    header('Location: logga-in.php');
    exit;
}

$grupper = minaGrupper($anvandarId);

$sidtitel = 'Mina grupper';

require __DIR__ . '/../src/header.php';
?>

<h1>Mina grupper</h1>

<?php if (!$grupper): ?>
    <p>Du är inte med i någon grupp än.</p>
    <p>
        Titta bland <a href="grupper.php">alla grupper</a> eller
        <a href="skapa-grupp.php">skapa en egen</a>.
    </p>
<?php else: ?>
    <ul class="grupplista">
        <?php foreach ($grupper as $grupp): ?>
            <li>
                <h2>
                    <a href="grupp.php?id=<?= $grupp['id'] ?>">
                        <?= htmlspecialchars($grupp['name']) ?>
                    </a>
                </h2>

                <p><?= htmlspecialchars($grupp['description']) ?></p>

                <?php if ($grupp['role'] === 'admin'): ?>
                    <p class="markering">Administratör</p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/../src/footer.php'; ?>