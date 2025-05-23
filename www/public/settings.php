<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrollerar om användaren är inloggad
if (!isset($_SESSION['uid'])) {
    header("Location: ./index.php?error=notloggedin_settings");
    exit();
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/../inc/header.php';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Inställningar - EGY Talk</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <main>
        <section class="settings-section">
            <h2>Kontoinställningar för <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <p>Här kan du hantera dina kontoinställningar.</p>

            <div class="danger-zone">
                <h3>Radera konto</h3>
                <p>Om du raderar ditt konto kommer all din data, inklusive dina inlägg och kommentarer, att tas bort permanent. Detta kan inte ångras.</p>
                <form action="./api/deleteAccount.php" method="POST" onsubmit="return confirm('Är du helt säker på att du vill radera ditt konto? All din data kommer att tas bort permanent och detta kan inte ångras.');">
                    <button type="submit" class="button delete-account-button">Radera mitt konto permanent</button>
                </form>
            </div>
        </section>
    </main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/../inc/footer.php'; ?>
</body>
</html>
<style>
        .settings-section {
            background-color: #ffffff;
            padding: 20px;
            margin: 30px auto;
            max-width: 700px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            color: #000000;
            border: 1px solid #000000;
        }
        .settings-section h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #000000;
            font-size: 1.5em;
            border-bottom: 2px solid #cc0000;
            padding-bottom: 10px;
        }
        .danger-zone {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .danger-zone h3 {
            color: #cc0000;
            margin-bottom: 10px;
        }
        .button.delete-account-button {
            background-color: #dc3545;
            color: white;
            border: 1px solid #c82333;
        }
        .button.delete-account-button:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>