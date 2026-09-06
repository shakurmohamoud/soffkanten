<?php

require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/inbjudningar.php';

startaSession();

$nyckel = $_GET['token'] ?? '';

// Nyckeln sparas i sessionen så att den finns kvar efter inloggning
if ($nyckel !== '') {
    $_SESSION['inbjudan'] = $nyckel;
} else {
    $nyckel = $_SESSION['inbjudan'] ?? '';
}

$inbjudan = $nyckel !== '' ? giltigInbjudan($nyckel) : null;

if ($inbjudan === null) {
    http_response_code(404);

    unset($_SESSION['inbjudan']);

    $sidtitel = 'Länken gäller inte';

    require __DIR__ . '/../src/header.php';

    echo '<h1>Länken gäller inte</h1>';
    echo '<p>Inbjudan är använd, för gammal eller felaktig.</p>';
    echo '<p><a href="index.php">Till startsidan</a></p>';

    require __DIR__ . '/../src/footer.php';
    exit;
}

$anvandarId = inloggadAnvandarId();

// Den som inte är inloggad får först skapa konto eller logga in
if ($anvandarId === null) {
    $sidtitel = 'Inbjudan';

    require __DIR__ . '/../src/header.php';
    ?>

    <h1>Du är inbjuden till <?= htmlspecialchars($inbjudan['name']) ?></h1>

    <p>Logga in eller skapa ett konto för att gå med.</p>

    <p>
        <a href="logga-in.php">Logga in</a>
        eller
        <a href="registrera.php">skapa ett konto</a>
    </p>

    <?php
    require __DIR__ . '/../src/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gruppId = loesInInbjudan($nyckel, $anvandarId);

    unset($_SESSION['inbjudan']);

    if ($gruppId === null) {
        header('Location: inbjudan.php');
        exit;
    }

    header('Location: grupp.php?id=' . $gruppId);
    exit;
}

$sidtitel = 'Inbjudan';

require __DIR__ . '/../src/header.php';
?>

<h1>Du är inbjuden till <?= htmlspecialchars($inbjudan['name']) ?></h1>

<p>Klicka nedan för att gå med direkt, utan att vänta på godkännande.</p>

<form method="post">
    <button type="submit">Gå med i gruppen</button>
</form>

<?php require __DIR__ . '/../src/footer.php'; ?>