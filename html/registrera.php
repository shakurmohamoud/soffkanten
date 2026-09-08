<?php

require __DIR__ . '/../src/auth.php';

startaSession();

$fel = [];
$fornamn = '';
$efternamn = '';
$epost = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fornamn = trim($_POST['fornamn'] ?? '');
    $efternamn = trim($_POST['efternamn'] ?? '');
    $epost = trim($_POST['epost'] ?? '');
    $losenord = $_POST['losenord'] ?? '';
    $losenordIgen = $_POST['losenord_igen'] ?? '';

    if ($fornamn === '') {
        $fel[] = 'Förnamn måste fyllas i.';
    }

    if ($efternamn === '') {
        $fel[] = 'Efternamn måste fyllas i.';
    }

    if (!filter_var($epost, FILTER_VALIDATE_EMAIL)) {
        $fel[] = 'Ange en giltig e-postadress.';
    } elseif (epostFinns($epost)) {
        $fel[] = 'Det finns redan ett konto med den e-postadressen.';
    }

    if (strlen($losenord) < 8) {
        $fel[] = 'Lösenordet måste vara minst 8 tecken.';
    }

    if ($losenord !== $losenordIgen) {
        $fel[] = 'Lösenorden matchar inte.';
    }

    if (!$fel) {
        $anvandarId = skapaAnvandare($fornamn, $efternamn, $epost, $losenord);

        session_regenerate_id(true);

        $_SESSION['anvandar_id'] = $anvandarId;

        header('Location: index.php');
        exit;
    }
}

$sidtitel = 'Skapa konto';

require __DIR__ . '/../src/header.php';
?>

<h1>Skapa konto</h1>

<?php if ($fel): ?>
    <div class="fel">
        <?php foreach ($fel as $meddelande): ?>
            <p><?= htmlspecialchars($meddelande) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" class="smaltformular">
    <label for="fornamn">Förnamn</label>
    <input type="text" id="fornamn" name="fornamn"
           value="<?= htmlspecialchars($fornamn) ?>" required>

    <label for="efternamn">Efternamn</label>
    <input type="text" id="efternamn" name="efternamn"
           value="<?= htmlspecialchars($efternamn) ?>" required>

    <label for="epost">E-post</label>
    <input type="email" id="epost" name="epost"
           value="<?= htmlspecialchars($epost) ?>" required>

    <label for="losenord">Lösenord</label>
    <input type="password" id="losenord" name="losenord" required>

    <label for="losenord_igen">Upprepa lösenord</label>
    <input type="password" id="losenord_igen" name="losenord_igen" required>

    <button type="submit">Skapa konto</button>
</form>

<p>Har du redan ett konto? <a href="logga-in.php">Logga in här</a>.</p>

<?php require __DIR__ . '/../src/footer.php'; ?>