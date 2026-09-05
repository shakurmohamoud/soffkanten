<?php

require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/grupper.php';

$anvandarId = inloggadAnvandarId();

if ($anvandarId === null) {
    header('Location: logga-in.php');
    exit;
}

// Tar emot en ansökan och laddar om sidan så att formuläret inte skickas igen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gruppId = (int) ($_POST['grupp_id'] ?? 0);

    if ($gruppId > 0) {
        ansokOmMedlemskap($gruppId, $anvandarId);
    }

    header('Location: grupper.php');
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
                    <form method="post">
                        <input type="hidden" name="grupp_id" value="<?= (int) $grupp['id'] ?>">

                        <button type="submit">Ansök om medlemskap</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/../src/footer.php'; ?>