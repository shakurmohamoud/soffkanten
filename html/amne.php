<?php

require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/grupper.php';
require __DIR__ . '/../src/diskussioner.php';

$anvandarId = inloggadAnvandarId();

if ($anvandarId === null) {
    header('Location: logga-in.php');
    exit;
}

$amneId = (int) ($_GET['id'] ?? 0);
$amne = hamtaAmne($amneId);

if ($amne === null) {
    http_response_code(404);

    $sidtitel = 'Diskussionen finns inte';

    require __DIR__ . '/../src/header.php';

    echo '<h1>Diskussionen finns inte</h1>';
    echo '<p><a href="mina-grupper.php">Tillbaka till mina grupper</a></p>';

    require __DIR__ . '/../src/footer.php';
    exit;
}

$gruppId = (int) $amne['group_id'];
$grupp = hamtaGrupp($gruppId);

// Rollen hämtas för gruppen som ämnet tillhör
// Den som inte är medlem där får inte läsa diskussionen
$roll = rollIGrupp($gruppId, $anvandarId);

if ($roll === null) {
    http_response_code(403);

    $sidtitel = 'Ingen åtkomst';

    require __DIR__ . '/../src/header.php';

    echo '<h1>Du är inte medlem i gruppen</h1>';
    echo '<p>Du behöver bli medlem för att läsa diskussionen.</p>';
    echo '<p><a href="grupper.php">Tillbaka till alla grupper</a></p>';

    require __DIR__ . '/../src/footer.php';
    exit;
}

$fel = '';

// Tar emot ett svar och laddar om sidan så att formuläret inte skickas igen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = trim($_POST['text'] ?? '');

    if ($text === '') {
        $fel = 'Skriv något innan du skickar.';
    } else {
        skapaInlagg($amneId, $anvandarId, $text);

        header('Location: amne.php?id=' . $amneId);
        exit;
    }
}

$inlagg = amnetsInlagg($amneId);

$sidtitel = $amne['title'];

require __DIR__ . '/../src/header.php';
?>

<p class="brodsmula">
    <a href="grupp.php?id=<?= $gruppId ?>"><?= htmlspecialchars($grupp['name']) ?></a>
</p>

<h1><?= htmlspecialchars($amne['title']) ?></h1>

<ul class="inlaggslista">
    <?php foreach ($inlagg as $rad): ?>
        <li>
            <p class="meta">
                <?= htmlspecialchars($rad['first_name'] . ' ' . $rad['last_name']) ?>
            </p>

            <p><?= nl2br(htmlspecialchars($rad['body'])) ?></p>
        </li>
    <?php endforeach; ?>
</ul>

<h2>Svara</h2>

<?php if ($fel !== ''): ?>
    <p class="fel"><?= htmlspecialchars($fel) ?></p>
<?php endif; ?>

<form method="post" class="svarsformular">
    <label for="text">Ditt svar</label>
    <textarea id="text" name="text" rows="5" required></textarea>

    <button type="submit">Skicka svar</button>
</form>

<?php require __DIR__ . '/../src/footer.php'; ?>