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

$sidtitel = $grupp['name'];

require __DIR__ . '/../src/header.php';
?>

<h1><?= htmlspecialchars($grupp['name']) ?></h1>

<p><?= htmlspecialchars($grupp['description']) ?></p>

<?php if ($roll === 'admin'): ?>
    <p>Du är administratör för den här gruppen.</p>
<?php endif; ?>

<h2>Diskussioner</h2>

<p>Inga diskussioner än.</p>

<?php require __DIR__ . '/../src/footer.php'; ?>