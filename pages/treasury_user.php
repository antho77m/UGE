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

    <?php
    function show_treasury_client_date($SIREN, $date) // fonction profil client
    { // Fonction qui affiche le solde des transactions du jour de la trésorerie du client à une date donnée
        global $cnx;

        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, count(num_autorisation) AS nbT, 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+' AND date_traitement <= '$date'), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-' AND date_traitement <= '$date'), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE date_traitement <= '$date' AND SIREN LIKE '%$SIREN'
        GROUP BY SIREN ORDER BY ''");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_OBJ);
        $ligne = $result;

        // if (!empty($ligne)) {
        //     if ($ligne[0]->montant_total >= 0) {
        //         echo '<p>SIREN : ' . $ligne[0]->SIREN . ' Raison sociale : ' . $ligne[0]->Raison_sociale . ' Nombre de transactions : ' . $ligne[0]->nbT . ' Montant total : <span style="color : green;">' . $ligne[0]->montant_total . '</span> Date : ' . $date . '</p><br>';
        //     } else {
        //         echo '<p>SIREN : ' . $ligne[0]->SIREN . ' Raison sociale : ' . $ligne[0]->Raison_sociale . ' Nombre de transactions : ' . $ligne[0]->nbT . ' Montant total : <span style="color : red;">' . $ligne[0]->montant_total . '</span> Date : ' . $date . '</p><br>';
        //     }
        // }


        return $result;
    }

    function showTreasury($SIREN) // Affiche la trésorerie du client au jour même
    {
        global $cnx;

        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, count(num_autorisation) AS nbT, 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+'), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-'), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE SIREN LIKE '%$SIREN'
        GROUP BY SIREN");
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_OBJ);
        $ligne = $result;

        // debug($ligne);

        // if (!empty($ligne)) {
        //     if ($ligne->montant_total >= 0) {
        //         echo '<p>SIREN : ' . $ligne->SIREN . ' Raison sociale : ' . $ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : green;">' . $ligne->montant_total . '</span> Date : ' . date('Y-m-d', time()) . '</p><br>';
        //     } else {
        //         echo '<p>SIREN : ' . $ligne->SIREN . ' Raison sociale : ' . $ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : red;">' . $ligne->montant_total . '</span> Date : ' . date('Y-m-d', time()) . '</p><br>';
        //     }
        // }


        return $result;
    }

    function showFields($SIREN) // Affiche la raison sociale du client
    {
        global $cnx;

        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, count(num_autorisation) AS nbT, 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+'), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-'), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE SIREN LIKE '%$SIREN'
        GROUP BY SIREN");
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_OBJ);

        return $result;
    }

    ?>

    <div class="treasury_sect">
        <div class="treasury_sep">
            <p class="big_text">Bonsoir <span class="bold"><?= showFields($SIREN)->Raison_sociale ?></span></p>
            <p class="blue75"><?= showFields($SIREN)->SIREN ?></p>
        </div>
        <div class="treasury_sep">
            <p class="important big_text"><?= number_format(showFields($SIREN)->montant_total, 0, ',', ' ') ?> €</p>
        </div>
        <div class="treasury_sep">
            <p class="big_text">Nombre de transactions</p>
            <p class="blue75"><?= showFields($SIREN)->nbT ?></p>
        </div>
    </div>

    <!-- <form action="" method="post">
        <div>
            <div>
                <label for="date">Date</label>
                <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <input type="submit" name="submit" value="Valider" />
    </form> -->

    <?php
    if (isset($_POST['submit'])) {
        if (!empty($_POST['date'])) {
            $date = $_POST['date'];
            array_push($array_export, show_treasury_client_date($SIREN, $date));
        } else {
            $date = date('Y-m-d');
            array_push($array_export, showTreasury($SIREN));
        }
    } else {
        $date = date('Y-m-d');
        array_push($array_export, showTreasury($SIREN));
    }
    echo '
        <p style="margin-left: 18px;">Exporter les résultats en :</p>
        <div class="export_wrap">
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=CSV&date=' . $date . '\', \'_blank\');">CSV</button>
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=XLS&date=' . $date . '\', \'_blank\');">XLS</button>
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=PDF&date=' . $date . '\', \'_blank\');">PDF</button>
        </div>
        ';
    include("user_treasury_graphics.php");
    echo '<div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>';
    ?>
    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>