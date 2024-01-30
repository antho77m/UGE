<?php

session_start();
if (isset($_SESSION['niveau']) && isset($_SESSION['SIREN'])) {
    if ($_SESSION['niveau'] != 1) { // si pas connecté en tant que Commerçant
        header("Location: login.php"); // redirige vers la page de connexion
    }
} else {
    header("Location: login.php"); // redirige vers la page de connexion
}
include(dirname(__FILE__, 2) . "/router.php");

include ROOT . "/includes/cnx.inc.php";

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphique</title>
    <meta property="og:description" content="Graphique">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />

    <script src="https://code.highcharts.com/highcharts.js"></script>

    <style type="text/css">
        .highcharts-figure,
        .highcharts-data-table table {
            width: 100%;
            margin: 1em auto;
            z-index: 1;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>
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

                <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
                    <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Graphics icon">
                </div>

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

            <!-- <div class="radio__container">
                <p class="options_text"> Ou une option </p>
                <div class="radio__graphics">
                    <label for="4m">Sur 4 mois glissants</label>
                    <input type="radio" id="4mois" name="mg" value="4">
                    <label for="12m">Sur 12 mois glissants</label>
                    <input type="radio" id="12mois" name="mg" value="12">

                </div>
            </div> -->

            <div class="form__group radioformgroup">
                <input type="radio" id="4mois" name="mg" value="4" class="radioinput">
                <label for="4m">Sur 4 mois glissants</label>
            </div>

            <div class="form__group radioformgroup">
                <input type="radio" id="12mois" name="mg" value="12" class="radioinput">
                <label for="12m">Sur 12 mois glissants</label>
            </div>

            <script>
                // selectionne la radio quand on clique sur le form__group
                const radioformgroup = document.querySelectorAll('.radioformgroup');
                radioformgroup.forEach((formgroup) => {
                    formgroup.addEventListener('click', () => {
                        formgroup.querySelector('input').checked = true;
                    })
                })

            </script>

            <div class="select__group" id="select_graphics" style="margin-top:30px;">
                <label for="graphiques">Type de graphique:</label>
                <div class="select__container">
                    <select name="graphique" id="graphique" required>
                        <option value="lr">Linéaire</option>
                        <option value="hm">Histogramme</option>
                        <option value="cl">Circulaire</option>
                    </select>
                </div>
            </div>

            <input type="submit" name="submit" value="Générer un graphique" class="btn" style="margin-top:30px;" id="button_graphics" />
        </form>
        <?php // fonctions   
        function dateDiffMois($date1, $date2)
        { // renvoi la différence de mois entre la date 1 et 2
            $date1 = strtotime($date1);
            $date2 = strtotime($date2);
            $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
            $diff = floor((((($diff / 60) / 60) / 24) / 7) / 4); // (((($diff/minutes)/heures)/jours)/semaines)/mois
            return $diff;
        }
        ?>
        <?php // main
        $SIREN = $_SESSION['SIREN'];

        if (isset($_POST['graphique']) && (isset($_POST['mg']) || (isset($_POST['dd']) && isset($_POST['df'])))) {
            if (isset($_POST['mg'])) { // une des deux options pour mois glissants a été choisis
                if ($_POST['mg'] == 4) {
                    $dd = date('Y-m-d', strtotime('-4 month'));
                    $df = date('Y-m-d');
                } else {
                    $dd = date('Y-m-d', strtotime('-12 month'));
                    $df = date('Y-m-d');
                }
            } else if (isset($_POST['dd']) && isset($_POST['df'])) { // si aucune option de mois glissants choisi/existe, on prend les deux champs de dates
                $dd = $_POST['dd'];
                $df = $_POST['df'];
            }
            $GRAPHIQUE = $_POST['graphique']; // lr pour linéaire, hm pour histogramme
            if ($GRAPHIQUE == "cl") {
                // récupère la somme des motifs des impayés entre deux dates  
                $motifs = $cnx->prepare("SELECT libelle, count(libelle) AS nb_motifs FROM Commercant NATURAL JOIN Transaction NATURAL JOIN Impaye JOIN Motifs_Impaye ON Impaye.code_motif = Motifs_Impaye.code WHERE SIREN = :siren AND date_vente BETWEEN :dd AND :df GROUP BY libelle");
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
                echo '<div class="graphics">';
                include("graphics/circular_graphics.php");
                echo '</div>';
            } else {
                // récupère la somme des montants et la date de vente des chiffre d'affaires entre deux dates
                $chiffre_affaires = $cnx->prepare("SELECT SUM(montant) AS montant, date_vente FROM Commercant NATURAL JOIN Transaction WHERE SIREN = :siren AND date_vente BETWEEN :dd AND :df GROUP BY date_vente ORDER BY date_vente");
                $chiffre_affaires->bindParam(':dd', $dd);
                $chiffre_affaires->bindParam(':df', $df);
                $chiffre_affaires->bindParam(':siren', $SIREN);
                $verif = $chiffre_affaires->execute();
                if (empty($verif)) {
                    exit("Erreur lors de la sélection");
                }
                $chiffre_affaires = $chiffre_affaires->fetchAll();
                $array_chiffre_affaires = array();
                $array_dates = array();
                foreach ($chiffre_affaires as $ligne) { // ajoute les montants de chiffre d'affaires dans array_chiffre_affaires et les dates dans array_dates
                    array_push($array_chiffre_affaires, (float)$ligne['montant']);
                    array_push($array_dates, $ligne['date_vente']);
                }

                // récupère la somme des montants et la date de vente des impayés entre deux dates
                $impayes = $cnx->prepare("SELECT SUM(montant) AS montant, date_vente FROM Commercant NATURAL JOIN Transaction NATURAL JOIN Impaye WHERE SIREN = :siren AND date_vente BETWEEN :dd AND :df GROUP BY date_vente ORDER BY date_vente");
                $impayes->bindParam(':dd', $dd);
                $impayes->bindParam(':df', $df);
                $impayes->bindParam(':siren', $SIREN);
                $verif = $impayes->execute();
                if (empty($verif)) {
                    exit("Erreur lors de la sélection");
                }
                $impayes = $impayes->fetchAll();
                $array_impayes = array();
                for ($i = 0; $i < count($array_dates); $i++) { // ajoute les montants d'impayés dans array_impayes par rapport aux dates d'actions et met 0 si aucun impayés n'a eu lieu à une date
                    $inserted = 0;
                    foreach ($impayes as $ligne) { // parcours les impayés ligne par ligne
                        if ($ligne['date_vente'] == $array_dates[$i]) { // s'il y a un impayé à une date, on insère l'impayé
                            array_push($array_impayes, (float)$ligne['montant']);
                            $inserted = 1;
                        }
                    }
                    if ($inserted == 0) { // s'il n'y a pas d'impayé à une date, on insère 0
                        array_push($array_impayes, (float)0);
                    }
                }

                // liste contenant les mois d'une année avec pour identifiant les mois en numérique
                $liste_mois = array("01" => "Jan", "02" => "Fév", "03" => "Mars", "04" => "Avr", "05" => "Mai", "06" => "Juin", "07" => "Jui", "08" => "Août", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Déc");
                $en_mois = 0;
                if (count($array_dates) > 2 && dateDiffMois($array_dates[0], $array_dates[count($array_dates) - 1]) > 1) { // vérifie si il y a plus de deux dates et si la différence entre la première et dernière date est supérieur à un mois
                    $en_mois = 1;
                }

                if ($en_mois == 1) { // si la première et la dernière date de array_dates sont supérieurs à 1 mois, on transforme les array par dates/jours en array par mois et additionne les montants par mois
                    $array_tmp1 = array();
                    $array_tmp2 = array();
                    $array_tmp3 = array();
                    for ($i = 0; $i < count($array_dates); $i++) { // parcours la liste array_dates
                        if ($i == 0) { // si on est au premier élément de array_dates
                            $mois_actuel = date('Y-m', strtotime($array_dates[$i])); // récupère le premier mois de array_dates
                            $montant_chiffre_affaires = $array_chiffre_affaires[$i]; // met dans la variable montant_chiffre_affaires la première valeur de array_chiffre_affaires
                            $montant_impayes = $array_impayes[$i]; // met dans la variable montant_impayes la première valeur de array_impayes
                        } else if (date('Y-m', strtotime($array_dates[$i])) != $mois_actuel) { // si la date à l'indice i de array_dates est différent du mois actuel dans mois_actuel
                            array_push($array_tmp1, $montant_chiffre_affaires); // ajoute dans array_tmp1 la valeur de montant_chiffre_affaires
                            array_push($array_tmp2, $montant_impayes); // ajoute dans array_tmp2 la valeur de montant_impayes
                            array_push($array_tmp3, $liste_mois[date('m', strtotime($mois_actuel))] . " " . date('Y', strtotime($mois_actuel))); // ajoute dans array_tmp3 le mois non numérique en sélectionnant dans liste_mois le mois via la variable mois_actuel

                            $mois_actuel = date('Y-m', strtotime($array_dates[$i])); // met dans mois_actuel le nouveau mois sélectionné
                            $montant_chiffre_affaires = $array_chiffre_affaires[$i]; // met dans la variable montant_chiffre_affaires la valeur à l'indice i de array_chiffre_affaires
                            $montant_impayes = $array_impayes[$i]; // met dans la variable montant_impayes la première valeur de array_impayes
                        } else { // si i != 0 et que le mois sélectionné n'est pas différent de mois_actuel
                            $montant_chiffre_affaires += $array_chiffre_affaires[$i];
                            $montant_impayes += $array_impayes[$i];
                        }
                    }
                    $array_chiffre_affaires = $array_tmp1; // remplace array_chiffre_affaires par array_tmp1 contenant la liste des chiffre d'affaires par mois
                    $array_impayes = $array_tmp2; // remplace array_impayes par array_tmp1 contenant la liste des impayés par mois
                    $array_dates = $array_tmp3; // remplace array_dates par array_tmp3 contenant la liste des mois où il y a eu des transactions
                }

                echo '<div class="graphics">';
                if ($GRAPHIQUE == "lr") { // si la variable $graphique est égale à lr (linéaire), on include un graphique linéaire, sinon on include un graphique histogramme
                    include("graphics/linear_graphics.php");
                } else {
                    include("graphics/histogram_graphics.php");
                }
                echo '</div>';
            }
        }
        echo '<div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>';
        ?>
    </section>
</body>

</html>