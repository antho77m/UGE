<?php
// include("cnx.inc.php");
include(dirname(__FILE__, 2) . "/includes/cnx.inc.php");
session_start();
if (!isset($_SESSION['niveau'])) {
    exit("Erreur 401");
}

include(dirname(__FILE__, 2) . "/includes/components/nav.php");
?>

<!DOCTYPE html>
<html lang="fr">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impayés</title>
</head>

<body>
    <div class="navbar">
        <div class="icon_container" onclick="window.location.href='/graphics'">
            <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/unpaid'">
            <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/home'">
            <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/treasury'">
            <img src="<?= $basepath ?>/src/img/treasury.svg" alt="Treasury icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/remittance'">
            <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
        </div>
    </div>
<section class="unpaid_section">

<p style="font-size: 24px;">Consultation des impayés</p>

<form action="/unpaid" method="POST" class="client__form">
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

    <input type="submit" name="submit" value="Validez" class="btn" style="margin-top: 30px;"/>

</form>


    <?php
    if ((isset($_POST['dd']) && isset($_POST['df'])) && isset($_POST['sens'])) {
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
        
        $impayes = $cnx -> prepare("SELECT SIREN, date_vente, date_traitement, num_carte, reseau, num_dos, montant, libelle FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction NATURAL JOIN Impaye JOIN Motifs_Impaye ON Impaye.code_motif = Motifs_Impaye.code WHERE SIREN LIKE :siren AND date_traitement BETWEEN :dd AND :df ORDER BY $ORDER $SENS");
        $impayes -> bindParam(':siren', $SIREN);
        $impayes -> bindParam(':dd', $dd);
        $impayes -> bindParam(':df', $df);
        $verif = $impayes -> execute();
        if (empty($verif)) {
            exit("Erreur lors de la sélection");
        }
        $impayes = $impayes->fetchAll();
        echo '
            <p style="margin-left: 18px;">Exporter les résultats en :</p>
            <div class="export_wrap_2">
            <button class="export" onclick="window.open(\'/export?format=CSV&detail=0\', \'_blank\');">CSV</button>
            <button class="export" onclick="window.open(\'/export?format=XLSX&detail=0\', \'_blank\');">XLSX</button>
            </div>
            </section>
            ';
        foreach ($impayes as $ligne) {
            echo '
            <section class="unpaid_results_wrap">
                <div class="unpaid_results">
                <div class="unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">SIREN</p>
                <p style="font-size: 18px; font-weight: 500;">' .$ligne['SIREN']  .'</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Date de Vente</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['date_vente']  .'</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Date de Traitement</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['date_traitement']  .'</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Numéro de Carte</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['num_carte']  .'</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Réseau</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['reseau']  .'</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Numéro de Dossier</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['num_dos']  .'</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Montant</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['montant']  .'</p>
                </div>

                <div class= "unpaid_result">
                <p style="font-size: 14px; color: var(--blue75);">Libelle</p>
                <p style="font-size: 18px; font-weight: 500;">'  . $ligne['libelle']  . '</p>
                </div>
                </div>
                </section>';
        }
        echo'<div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>';
        $_SESSION['tab'] = $impayes;
    }
    ?>
</body>

</html>