<?php
// TODO: redirect vers cette page quand clic sur graphs navbar
session_start();
include(dirname(__FILE__, 2) . "/router.php");
if (isset($_SESSION['niveau'])) {
    if ($_SESSION['niveau'] != 3) {
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
    <title>Graphiques</title>
    <meta property="og:description" content="Graphiques">
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

    <section class="graphics_section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 24px;">Consultation des graphiques</p>
            <div class="logos pc-nav">
                <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
                    <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
                </div>

                <?php if ($_SESSION['niveau'] == 1) : ?>
                    <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
                        <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Graphics icon">
                    </div>
                <?php elseif ($_SESSION['niveau'] == 3) : ?>
                    <div class="icon_container" onclick="window.location.href='/pages/PO_graphics.php'">
                        <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Graphics icon">
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

        <form action="" method="POST" class="graphics__form">
            <div class="form__group">
                <p style="margin-bottom:20px;"> Choisissez un intervalle </p>
                <label for="date_debut">date de début:</label>
                <div class="input__container">
                    <input type="date" id="dates" name="dd" value="2010-01-01">
                </div>
            </div>

            <div class="form__group">
                <label for="date_fin">date de fin:</label>
                <div class="input__container">
                    <input type="date" id="dates" name="df" value="2022-12-30">
                </div>
            </div>

            <input type="submit" name="submit" value="Générer un graphique" class="btn" style="margin-top:30px;" />
        </form>
    </section>

    <?php
    if (isset($_POST['dd']) && isset($_POST['df'])) {
        // récupère la somme des motifs des impayés entre deux dates  
        $motifs = $cnx->prepare("SELECT libelle, count(libelle) AS nb_motifs FROM Commercant NATURAL JOIN Transaction NATURAL JOIN Impaye JOIN Motifs_Impaye ON Impaye.code_motif = Motifs_Impaye.code WHERE date_vente BETWEEN :dd AND :df GROUP BY libelle");
        $motifs->bindParam(':dd', $dd);
        $motifs->bindParam(':df', $df);
        $motifs->bindParam(':siren', $SIREN);
        $verif = $motifs->execute();
        if (empty($verif)) {
            exit("Erreur lors de la sélection");
        }
        $motifs = $motifs->fetchAll();
        // liste contenant le nombre d'itérations de chaque motif d'impayé
        $array_motifs = array("fraude a la carte" => 0, "compte a decouvert" => 0, "compte cloture" => 0, "compte bloque" => 0, "provision insuffisante" => 0, "operation contestee par le debiteur" => 0, "titulaire decede" => 0, "raison non communiquee, contactez la banque du client" => 0);
        foreach ($motifs as $ligne) { // ajoute le nombre d'itérations de chaque motif d'impayé dans array_motifs
            $array_motifs[$ligne['libelle']] = $ligne['nb_motifs'];
        }
        include("graphics/circular_graphics.php");
    }
    ?>