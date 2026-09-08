<?php

require __DIR__ . '/../src/auth.php';

startaSession();

$fel = '';
$epost = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $epost = trim($_POST['epost'] ?? '');
    $losenord = $_POST['losenord'] ?? '';

    $anvandarId = loggaIn($epost, $losenord);

    if ($anvandarId === null) {
        $fel = 'Fel e-postadress eller lösenord.';
    } else {
        session_regenerate_id(true);

        $_SESSION['anvandar_id'] = $anvandarId;

        header('Location: index.php');
        exit;
    }
}

$sidtitel = 'Logga in';

require __DIR__ . '/../src/header.php';
?>

<h1>Logga in</h1>

<?php if ($fel !== ''): ?>
    <p class="fel"><?= htmlspecialchars($fel) ?></p>
<?php endif; ?>

<form method="post" class="smaltformular">
    <label for="epost">E-post</label>
    <input type="email" id="epost" name="epost"
           value="<?= htmlspecialchars($epost) ?>" required>

    <label for="losenord">Lösenord</label>
    <input type="password" id="losenord" name="losenord" required>

    <button type="submit">Logga in</button>
</form>

<p>Har du inget konto? <a href="registrera.php">Skapa ett här</a>.</p>

<?php require __DIR__ . '/../src/footer.php'; ?>