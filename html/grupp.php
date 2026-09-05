<?php

require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/grupper.php';

$anvandarId = inloggadAnvandarId();

if ($anvandarId === null) {
    header('Location: logga-in.php');
    exit;
}

$gruppId = (int) ($_GET['id'] ?? 0);
$grupp = hamtaGrupp($gruppId);

if ($grupp === null) {
    http_response_code(404);

    $sidtitel = 'Gruppen finns inte';

    require __DIR__ . '/../src/header.php';

    echo '<h1>Gruppen finns inte</h1>';
    echo '<p><a href="grupper.php">Tillbaka till alla grupper</a></p>';

    require __DIR__ . '/../src/footer.php';
    exit;
}

$roll = rollIGrupp($gruppId, $anvandarId);

if ($roll === null) {
    http_response_code(403);

    $sidtitel = 'Ingen åtkomst';

    require __DIR__ . '/../src/header.php';

    echo '<h1>Du är inte medlem i gruppen</h1>';
    echo '<p>Du behöver bli medlem för att se diskussionerna här.</p>';
    echo '<p><a href="grupper.php">Tillbaka till alla grupper</a></p>';

    require __DIR__ . '/../src/footer.php';
    exit;
}

// Fältet atgard skiljer de olika formulären åt
// Sidan laddas om efteråt så att formuläret inte skickas igen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $atgard = $_POST['atgard'] ?? '';
    $valdAnvandarId = (int) ($_POST['anvandar_id'] ?? 0);

    if ($atgard === 'godkann' && $valdAnvandarId > 0) {
        godkannAnsokan($gruppId, $valdAnvandarId, $anvandarId);
    }

    if ($atgard === 'roll' && $valdAnvandarId > 0) {
        sattRoll($gruppId, $valdAnvandarId, $_POST['roll'] ?? '', $anvandarId);
    }

    header('Location: grupp.php?id=' . $gruppId);
    exit;
}

$ansokningar = $roll === 'admin' ? vantandeAnsokningar($gruppId) : [];
$medlemmar = $roll === 'admin' ? gruppensMedlemmar($gruppId) : [];

$sidtitel = $grupp['name'];

require __DIR__ . '/../src/header.php';
?>

<h1><?= htmlspecialchars($grupp['name']) ?></h1>

<p><?= htmlspecialchars($grupp['description']) ?></p>

<?php if ($roll === 'admin'): ?>
    <p>Du är administratör för den här gruppen.</p>

    <h2>Ansökningar</h2>

    <?php if (!$ansokningar): ?>
        <p>Inga ansökningar just nu.</p>
    <?php else: ?>
        <ul class="ansokningslista">
            <?php foreach ($ansokningar as $ansokande): ?>
                <li>
                    <span>
                        <?= htmlspecialchars($ansokande['first_name'] . ' ' . $ansokande['last_name']) ?>
                    </span>

                    <form method="post">
                        <input type="hidden" name="atgard" value="godkann">
                        <input type="hidden" name="anvandar_id" value="<?= (int) $ansokande['id'] ?>">

                        <button type="submit">Godkänn</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Medlemmar</h2>

    <ul class="medlemslista">
        <?php foreach ($medlemmar as $medlem): ?>
            <li>
                <span>
                    <?= htmlspecialchars($medlem['first_name'] . ' ' . $medlem['last_name']) ?>
                    (<?= $medlem['role'] === 'admin' ? 'administratör' : 'medlem' ?>)
                </span>

                <?php if ((int) $medlem['id'] === $anvandarId): ?>
                    <span class="markering">Du</span>
                <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="atgard" value="roll">
                        <input type="hidden" name="anvandar_id" value="<?= (int) $medlem['id'] ?>">
                        <input type="hidden" name="roll" value="<?= $medlem['role'] === 'admin' ? 'member' : 'admin' ?>">

                        <button type="submit">
                            <?= $medlem['role'] === 'admin' ? 'Gör till medlem' : 'Gör till administratör' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Diskussioner</h2>

<p>Inga diskussioner än.</p>

<?php require __DIR__ . '/../src/footer.php'; ?>