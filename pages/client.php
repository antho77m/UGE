<?php

session_start();
include(dirname(__FILE__, 2) . "/router.php");

$action = $_SERVER['QUERY_STRING'];
$actions_title = [
    'add' => 'Ajout de compte',
    'edit' => 'Modification de compte',
    'delete' => 'Suppression de compte',
    'display' => 'Liste des comptes'
];

include ROOT . "/includes/cnx.inc.php";
require_once ROOT . "/includes/functions/client.php";

if ($action == 'add') {
    if (isset($_POST["name"]) && isset($_POST["siren"]) && isset($_POST["password"]) && isset($_POST["id"])) {
        add_account($_POST["name"], $_POST["siren"], $_POST["password"], $_POST["id"]);
    }
}

if ($action == 'delete') {
    if (isset($_POST["name"]) && isset($_POST["siren"]) && isset($_POST["id"])) {
        delete_account($_POST["name"], $_POST["siren"], $_POST["id"]);
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des comptes</title>
    <meta property="og:description" content="Gestion des comptes">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
</head>

<body>

    <?php include ROOT . "/includes/components/nav.php"; ?>

    <section class="clients_manage">

        <p style="font-size: 24px; margin: 10px 0;"><?= $actions_title[$action] ?></p>

        <?php if ($action === 'display') {
            display_clients();
        } else {
        ?>
            <form action="" method="POST" class="client__form">
                <div class="form__group">
                    <label for="name">Nom du client</label>
                    <div class="input__container">
                        <input type="text" name="name" id="name" placeholder="Raison sociale" autocomplete="off">
                    </div>
                </div>

                <div class="form__group">
                    <label for="name">N° de SIREN</label>
                    <div class="input__container">
                        <input type="number" name="siren" id="siren" placeholder="SIREN" autocomplete="off">
                    </div>
                </div>

                <div class="form__group">
                    <label for="name">Identifiant du compte</label>
                    <div class="input__container">
                        <input type="number" name="id" id="id" placeholder="Identifiant du compte" autocomplete="off">
                    </div>
                </div>

                <?php if ($action !== 'delete') : ?>
                    <div class="form__group">
                        <label for="name">Mot de passe</label>
                        <div class="input__container">
                            <input type="password" name="password" id="password" placeholder="Mot de passe" autocomplete="off">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form__group form__action">
                    <button class="btn__cancel actions_btn"><span class="material-symbols-outlined icon">close</span>Annuler</button>
                    <?php if ($action === 'add') : ?>
                        <button type="sumbit" class="btn__confirm actions_btn"><span class="material-symbols-outlined icon">add</span>Confirmer</button>
                    <?php elseif ($action === 'delete') : ?>
                        <button type="sumbit" class="btn__delete actions_btn"><span class="material-symbols-outlined icon">delete</span>Suppression</button>
                    <?php endif; ?>
                </div>
            </form>
        <?php } ?>


    </section>



    <script>
        const btnCancel = document.querySelector('.btn__cancel');

        btnCancel.addEventListener('click', (e) => {
            // prevent default behavior
            e.preventDefault();
            // redirect to home page
            window.location.href = "<?= $basepath ?>/pages/home.php";
        });
    </script>

    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>