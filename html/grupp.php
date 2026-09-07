<?php

require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/grupper.php';
require __DIR__ . '/../src/diskussioner.php';
require __DIR__ . '/../src/inbjudningar.php';

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

$fel = '';
$inbjudningslank = '';

// Fältet atgard skiljer de olika formulären åt
// Sidan laddas om efteråt så att formuläret inte skickas igen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $atgard = $_POST['atgard'] ?? '';
    $valdAnvandarId = (int) ($_POST['anvandar_id'] ?? 0);

    if ($atgard === 'godkann' && $valdAnvandarId > 0) {
        godkannAnsokan($gruppId, $valdAnvandarId, $anvandarId);

        header('Location: grupp.php?id=' . $gruppId);
        exit;
    }

    if ($atgard === 'roll' && $valdAnvandarId > 0) {
        sattRoll($gruppId, $valdAnvandarId, $_POST['roll'] ?? '', $anvandarId);

        header('Location: grupp.php?id=' . $gruppId);
        exit;
    }

    if ($atgard === 'amne') {
        $rubrik = trim($_POST['rubrik'] ?? '');
        $text = trim($_POST['text'] ?? '');

        if ($rubrik === '' || $text === '') {
            $fel = 'Fyll i både ämne och ett första inlägg.';
        } else {
            $amneId = skapaAmne($gruppId, $anvandarId, $rubrik, $text);

            header('Location: amne.php?id=' . $amneId);
            exit;
        }
    }

    // Länken visas direkt i svaret eftersom den skulle försvinna vid en omladdning
    if ($atgard === 'inbjudan' && $roll === 'admin') {
        $nyckel = skapaInbjudan($gruppId, $anvandarId);

        $inbjudningslank = 'http://' . $_SERVER['HTTP_HOST'] . '/inbjudan.php?token=' . $nyckel;
    }
}

$ansokningar = $roll === 'admin' ? vantandeAnsokningar($gruppId) : [];
$medlemmar = $roll === 'admin' ? gruppensMedlemmar($gruppId) : [];
$amnen = gruppensAmnen($gruppId);

$sidtitel = $grupp['name'];

require __DIR__ . '/../src/header.php';
?>

<h1><?= htmlspecialchars($grupp['name']) ?></h1>

<p class="beskrivning"><?= htmlspecialchars($grupp['description']) ?></p>

<h2>Diskussioner</h2>

<?php if (!$amnen): ?>
    <p>Inga diskussioner än. Starta den första nedan.</p>
<?php else: ?>
    <ul class="amneslista">
        <?php foreach ($amnen as $amne): ?>
            <li>
                <a href="amne.php?id=<?= (int) $amne['id'] ?>">
                    <?= htmlspecialchars($amne['title']) ?>
                </a>

                <span class="meta">
                    startad av <?= htmlspecialchars($amne['first_name'] . ' ' . $amne['last_name']) ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Starta en diskussion</h2>

<?php if ($fel !== ''): ?>
    <p class="fel"><?= htmlspecialchars($fel) ?></p>
<?php endif; ?>

<form method="post" class="smaltformular">
    <input type="hidden" name="atgard" value="amne">

    <label for="rubrik">Ämne</label>
    <input type="text" id="rubrik" name="rubrik" maxlength="200" required>

    <label for="text">Första inlägget</label>
    <textarea id="text" name="text" rows="5" required></textarea>

    <button type="submit">Starta diskussion</button>
</form>

<?php if ($roll === 'admin'): ?>
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
                    <span class="roll"><?= $medlem['role'] === 'admin' ? 'administratör' : 'medlem' ?></span>
                </span>

                <?php if ((int) $medlem['id'] === $anvandarId): ?>
                    <span class="markering">Du</span>
                <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="atgard" value="roll">
                        <input type="hidden" name="anvandar_id" value="<?= (int) $medlem['id'] ?>">
                        <input type="hidden" name="roll" value="<?= $medlem['role'] === 'admin' ? 'member' : 'admin' ?>">

                        <button type="submit" class="sekundar">
                            <?= $medlem['role'] === 'admin' ? 'Gör till medlem' : 'Gör till administratör' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Bjud in</h2>

    <p>Länken gäller i 24 timmar och kan bara användas en gång.</p>

    <?php if ($inbjudningslank !== ''): ?>
        <input type="text" class="inbjudningslank" value="<?= htmlspecialchars($inbjudningslank) ?>" readonly>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="atgard" value="inbjudan">

        <button type="submit" class="sekundar">Skapa inbjudningslänk</button>
    </form>
<?php endif; ?>

<?php require __DIR__ . '/../src/footer.php'; ?>