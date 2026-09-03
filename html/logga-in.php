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
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Logga in - Soffkanten</title>
</head>
<body>
    <h1>Logga in</h1>

    <?php if ($fel !== ''): ?>
        <p><?= htmlspecialchars($fel) ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label for="epost">E-post</label><br>
            <input type="email" id="epost" name="epost"
                   value="<?= htmlspecialchars($epost) ?>" required>
        </p>

        <p>
            <label for="losenord">Lösenord</label><br>
            <input type="password" id="losenord" name="losenord" required>
        </p>

        <button type="submit">Logga in</button>
    </form>

    <p>Har du inget konto? <a href="registrera.php">Skapa ett här</a>.</p>
</body>
</html>