<?php

require __DIR__ . '/../src/auth.php';

$sidtitel = 'Start';

$anvandare = inloggadAnvandare();

require __DIR__ . '/../src/header.php';
?>

<?php if ($anvandare): ?>
    <h1>Välkommen tillbaka, <?= htmlspecialchars($anvandare['first_name']) ?></h1>

    <p class="beskrivning">
        Här är dina grupper och diskussioner. Gå till
        <a href="grupper.php">alla grupper</a> för att hitta nya att gå med i.
    </p>

    <div class="knapprad">
        <a href="mina-grupper.php" class="knapp">Mina grupper</a>
        <a href="skapa-grupp.php" class="knapp sekundar">Skapa en grupp</a>
    </div>
<?php else: ?>
    </main>

    <div class="hjalte">
        <h1><span>Prata</span><span>om det</span><span>du gillar.</span></h1>

        <p class="ingress">
            Gå med i grupper, starta en diskussion och prata med folk
            som bryr sig om samma saker som du.
        </p>

        <div class="knapprad">
            <a href="registrera.php" class="knapp">Skapa ett konto</a>
            <a href="logga-in.php" class="knapp sekundar">Logga in</a>
        </div>
    </div>

    <div class="funktioner">
        <div class="funktion">
            <h2>Hitta en grupp</h2>
            <p>Bläddra bland grupperna och ansök om att gå med i dem som passar dig.</p>
        </div>

        <div class="funktion">
            <h2>Starta en diskussion</h2>
            <p>Ställ en fråga eller dela ett tips. Alla i gruppen kan svara.</p>
        </div>

        <div class="funktion">
            <h2>Bjud in andra</h2>
            <p>Är du administratör kan du skicka en länk så att någon går med direkt.</p>
        </div>
    </div>

    <main>
<?php endif; ?>

<?php require __DIR__ . '/../src/footer.php'; ?>