<?php

include(dirname(__FILE__, 2) . "/router.php");
include ROOT . "/includes/components/logo.php";

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceuil</title>
    <meta property="og:description" content="Accueil du site">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">
    <link rel="stylesheet" href="/src/styles/pages/index.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
</head>

<body>
    
    <section class="centered">
        <p class="big_text start__text">Toutes les informations <br> sur vos <span class="important">comptes</span> à portée <br> de main.</p>
    </section>

    <section class="centered connection">
        <button class="btn">
            <a href="/pages/login.php">CONNEXION</a>
        </button>
    </section>

    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>