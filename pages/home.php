<?php

session_start();
include(dirname(__FILE__, 2) . "/router.php");

$_SESSION['try'] = 0;

if (isset($_SESSION['niveau'])) {
    (($_SESSION['niveau'] == 1) ? $level = 'Mon compte' : (($_SESSION['niveau'] == 2) ? $level = 'Gestion des comptes' : $level = 'Acceuil'));
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $level ?></title>
    <meta property="og:description" content="Acceuil du site">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
</head>

<body>

    <?php include ROOT . "/includes/components/nav.php"; ?>

    <?php
    if ($_SESSION['niveau'] == 1) {
        include ROOT . "/includes/components/client__account.php";
        header("Location: /pages/treasury.php");
    } else if ($_SESSION['niveau'] == 2) {
        include ROOT . "/includes/components/admin__clients_manage.php";
    } else if ($_SESSION['niveau'] == 3) {
        include ROOT . "/includes/components/product_owner__home.php";
        header("Location: /pages/treasury.php");
    }
    ?>

    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>