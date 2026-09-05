<?php

require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/grupper.php';

$anvandarId = inloggadAnvandarId();

if ($anvandarId === null) {
    header('Location: logga-in.php');
    exit;
}

$fel = [];
$namn = '';
$beskrivning = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namn = trim($_POST['namn'] ?? '');
    $beskrivning = trim($_POST['beskrivning'] ?? '');

    if ($namn === '') {
        $fel[] = 'Gruppen måste ha ett namn.';
    }

    if ($beskrivning === '') {
        $fel[] = 'Beskriv vad som ska diskuteras i gruppen.';
    }

    if (!$fel) {
        $gruppId = skapaGrupp($namn, $beskrivning, $anvandarId);

        header('Location: grupp.php?id=' . $gruppId);
        exit;
    }
}

$sidtitel = 'Skapa grupp';

require __DIR__ . '/../src/header.php';
?>

<h1>Skapa grupp</h1>

<p>Du blir administratör för gruppen du skapar.</p>

<?php if ($fel): ?>
    <ul class="fel">
        <?php foreach ($fel as $meddelande): ?>
            <li><?= htmlspecialchars($meddelande) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post">
    <p>
        <label for="namn">Namn på gruppen</label><br>
        <input type="text" id="namn" name="namn"
               value="<?= htmlspecialchars($namn) ?>" required>
    </p>

    <p>
        <label for="beskrivning">Vad ska diskuteras här?</label><br>
        <textarea id="beskrivning" name="beskrivning" rows="4"
                  required><?= htmlspecialchars($beskrivning) ?></textarea>
    </p>

    <button type="submit">Skapa grupp</button>
</form>

<?php require __DIR__ . '/../src/footer.php'; ?>