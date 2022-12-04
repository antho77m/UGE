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
    <title>Impayés</title>
    <meta property="og:description" content="Impayés">
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

    <section class="unpaid_section">

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 24px;">Consultation des impayés</p>
            <div class="logos pc-nav">
                <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
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

        
        <form action="/pages/search_unpaid.php" method="POST" class="client__form">
            <div class="form__group">
                <label for="start">date de début:</label>
                <div class="input__container">
                    <input type="date" id="dd" name="dd" value="2010-01-01">
                </div>
            </div>

            <div class="form__group">
                <label for="start">date de fin:</label>
                <div class="input__container">
                    <input type="date" id="df" name="df" value="2022-12-30">
                </div>
            </div>
            <div class="form__radio">
                <div class="form__select">
                    <input type="radio" id="desc" name="sens" value="DESC" required>
                    <label for="">décroissant</label>
                </div>

                <div class="form__select">
                    <input type="radio" id="asc" name="sens" value="ASC" required>
                    <label for="">croissant</label>
                </div>
            </div>

            <input type="submit" name="submit" value="Validez" class="btn" style="margin-top: 30px;" />

        </form>

        <?php
        if ((isset($_POST['dd']) && isset($_POST['df'])) && isset($_POST['sens']) && isset($_SESSION['niveau'])) {
            $SIREN;
            $Raison_Sociale;
            $dd = $_POST['dd'];
            $df = $_POST['df'];
            $ORDER;
            $SENS = $_POST['sens'];
            if ($_SESSION['niveau'] == 1) {
                if (!isset($_SESSION['SIREN'])) {
                    exit("Erreur 401");
                }
                $SIREN = $_SESSION['SIREN'];
                $ORDER = "montant";
            } else if ($_SESSION['niveau'] == 3) {
                $SIREN = "%";
                $ORDER = "SIREN";
            } else {
                exit("Erreur 401");
            }

            $impayes = $cnx->prepare("SELECT SIREN, date_vente, date_traitement, num_carte, reseau, num_dos, montant, libelle FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction NATURAL JOIN Impaye JOIN Motifs_Impaye ON Impaye.code_motif = Motifs_Impaye.code WHERE SIREN LIKE :siren AND date_traitement BETWEEN :dd AND :df ORDER BY $ORDER $SENS");
            $impayes->bindParam(':siren', $SIREN);
            $impayes->bindParam(':dd', $dd);
            $impayes->bindParam(':df', $df);
            $verif = $impayes->execute();
            if (empty($verif)) {
                exit("Erreur lors de la sélection");
            }
            $impayes = $impayes->fetchAll();

            echo '<b style="margin-left: 18px;">Résultat: ' . count($impayes) . '</b>';

            // EXPORT DES DONNEES
            if (count($impayes) > 0) {
                echo '
                <p style="margin-left: 18px;">Exporter les résultats en :</p>
                <div class="export_wrap_2">
                <button class="export" onclick="window.open(\'/pages/exports/export_unpaid.php?format=CSV\', \'_blank\');">CSV</button>
                <button class="export" onclick="window.open(\'/pages/exports/export_unpaid.php?format=XLS\', \'_blank\');">XLS</button>
                <button class="export" onclick="window.open(\'/pages/exports/export_unpaid.php?format=PDF\', \'_blank\');">PDF</button>
                </div>
                </section>
                <section class="unpaid_results_wrap">
                ';
            }
            foreach ($impayes as $ligne) {
                echo '
                <div class="unpaid_results">
                <div class="unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">SIREN</p>
                <p style="font-size: 18px; font-weight: 500;">' . $ligne['SIREN']  . '</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Date de Vente</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['date_vente']  . '</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Date de Traitement</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['date_traitement']  . '</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Numéro de Carte</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['num_carte']  . '</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Réseau</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['reseau']  . '</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Numéro de Dossier</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['num_dos']  . '</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Montant</p>
                <p style="font-size: 18px; font-weight: 500;">-'  . $ligne['montant']  . '</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Libelle</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['libelle']  . '</p>
                </div>
                </div>';
            }
            echo '</section> <div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>';
            $_SESSION['tab_unpaids'] = $impayes;
        }
        ?>
        <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>