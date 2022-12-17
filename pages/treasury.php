<?php

session_start();
include(dirname(__FILE__, 2) . "/router.php");

if (isset($_SESSION['niveau'])) {
    if ($_SESSION['niveau'] == 2) {
        header("Location: login.php");
    }
} else {
    header("Location: login.php");
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

    <div class="navbar mobile-nav">
        <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
            <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
        </div>

        <?php if ($_SESSION['niveau'] == 1) : ?>
            <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
                <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
            </div>
        <?php elseif ($_SESSION['niveau'] == 3) : ?>
            <div class="icon_container" onclick="window.location.href='/pages/PO_graphics.php'">
                <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
            </div>
        <?php endif; ?>

        <div class="icon_container" onclick="window.location.href='/pages/search_unpaid.php'">
            <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/search_remittance.php'">
            <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
        </div>
    </div>
    
    <section class="graphics_section pcnav_treasury">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 24px;">Trésorerie</p>
            <div class="logos pc-nav">
                <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
                    <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
                </div>

                <?php if ($_SESSION['niveau'] == 1) : ?>
                    <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
                        <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
                    </div>
                <?php elseif ($_SESSION['niveau'] == 3) : ?>
                    <div class="icon_container" onclick="window.location.href='/pages/PO_graphics.php'">
                        <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
                    </div>
                <?php endif; ?>

                <div class="icon_container" onclick="window.location.href='/pages/search_unpaid.php'">
                    <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
                </div>

                <div class="icon_container" onclick="window.location.href='/pages/search_remittance.php'">
                    <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
                </div>
            </div>
        </div>
    </section>

    <?php
    $array_export = array(); // création du tableau qui va prendre en valeurs les éléments à exporter (comptes et soldes)
    if ($_SESSION['niveau'] == 1) { // Si connecté en tant que Commerçant
        if (!isset($_SESSION['SIREN'])) {
            header("Location: login.php"); // redirige vers la page de connexion
        }
        $SIREN = $_SESSION['SIREN'];
        include("treasury_user.php");
    } else if ($_SESSION['niveau'] == 3) { // Si connecté en tant que Product Owner
        include("treasury_PO.php");
    } else {
        header("Location: login.php"); // redirige vers la page de connexion
    }
    $_SESSION['tab_treasury'] = $array_export; // mise en variable de session de array_export
    ?>

    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>