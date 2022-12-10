<?php


session_start();
include(dirname(__FILE__, 2) . "/router.php");

if (!isset($_SESSION['niveau'])) {
    exit("Erreur 401");
}

include ROOT . "/includes/cnx.inc.php";

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trésorerie</title>
    <meta property="og:description" content="Trésorerie">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
</head>

<body>

    <?php include ROOT . "/includes/components/nav.php"; ?>

    <section class="graphics_section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 24px;">Trésorerie</p>
            <div class="logos pc-nav">
                <div class="icon_container" onclick="window.location.href='/pages/user_treasury_graphics.php'">
                    <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
                </div>

                <div class="icon_container" onclick="window.location.href='/pages/search_unpaid.php'">
                    <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
                </div>

                <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
                    <img src="<?= $basepath ?>/src/img/treasury.svg" alt="Treasury icon">
                </div>

                <div class="icon_container" onclick="window.location.href='/pages/search_remittance.php'">
                    <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
                </div>
            </div>
        </div>
    </section>

    <?php 
        $array_export = array();
        if ($_SESSION['niveau'] == 1) {
            $SIREN = $_SESSION['SIREN'];
            include ("treasury_user.php");
        } else {
            include ("treasury_PO.php");
        }
        $_SESSION['tab_treasury'] = $array_export;
    ?>
    
    <div class="navbar mobile-nav">
        <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
            <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/search_unpaid.php'">
            <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/home.php'">
            <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
            <img src="<?= $basepath ?>/src/img/treasury.svg" alt="Treasury icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/search_remittance.php'">
            <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
        </div>
    </div>
    <?php

    ?>
    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>