<?php
// include("cnx.inc.php");
include (dirname(__FILE__, 2) . "/includes/cnx.inc.php");
session_start();
if (!isset($_SESSION['SIREN']) || !isset($_SESSION['niveau']) || $_SESSION['niveau'] != 1) {
    exit("Erreur 401");
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Graphique</title>
        <script src="https://code.highcharts.com/highcharts.js" ></script> 
		<style type="text/css">
            .highcharts-figure,
            .highcharts-data-table table {
                min-width: 360px;
                max-width: 800px;
                margin: 1em auto;
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
        <form action="graphics_user_page.php" method="post">
            <label for="date_debut">date de début:</label>
            <input type="date" id="dates" name="dd" value="2010-01-01">
            <label for="date_fin">date de fin:</label>
            <input type="date" id="dates" name="df" value="2022-12-30">
            <input type="radio" id="1mois" name="mg" value="1">
            <label for="4m">Sur 1 mois glissant</label>
            <input type="radio" id="4mois" name="mg" value="4">
            <label for="4m">Sur 4 mois glissants</label>
            <input type="radio" id="12mois" name="mg" value="12">
            <label for="12m">Sur 12mois glissants</label>

            <label for="graphiques">Type de graphique:</label>
            <select name="graphique" id="graphique" required>
                <option value="lr">Linéaire</option>
                <option value="hm">Histogramme</option>
                <option value="cl">Circulaire</option>
            </select>
            <input type="submit" name="submit" value="Générer un graphique" class = "boutton_formulaire"/>
        </form>
        <?php // fonctions   
            function dateDiffMois($date1, $date2) { // renvoi la différence de mois entre la date 1 et 2
                $date1 = strtotime($date1);
                $date2 = strtotime($date2);
                $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
                $diff = floor((((($diff/60)/60)/24)/7)/4); // (((($diff/minutes)/heures)/jours)/semaines)/mois
                return $diff;
            }
        ?>
        <?php // main
            $SIREN = $_SESSION['SIREN'];
            
            if (isset($_POST['graphique']) && (isset($_POST['mg']) || (isset($_POST['dd']) && isset($_POST['df'])))) {     
                if (isset($_POST['mg'])) { // une des trois options pour mois glissants a été choisis
                    if ($_POST['mg'] == 1) {
                        $dd = date('Y-m-d', strtotime('-1 month')); // date début
                        $df = date('Y-m-d'); // date fin
                    } else if ($_POST['mg'] == 4) {
                        $dd = date('Y-m-d', strtotime('-4 month'));
                        $df = date('Y-m-d');
                    } else {
                        $dd = date('Y-m-d', strtotime('-12 month'));
                        $df = date('Y-m-d');
                    }
                } else if (isset($_POST['dd']) && isset($_POST['df'])) { // si aucune option de mois glissants choisis, on prend les deux champs de dates
                    $dd = $_POST['dd'];
                    $df = $_POST['df'];
                }       
                $GRAPHIQUE = $_POST['graphique']; // lr pour linéaire, hm pour histogramme
                if ($GRAPHIQUE == "cl")  
                {
                    // récupère la somme des motifs des impayés entre deux dates  
                    $motifs = $cnx -> prepare("SELECT libelle, count(libelle) AS nb_motifs FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction NATURAL JOIN Impaye JOIN Motifs_Impaye ON Impaye.code_motif = Motifs_Impaye.code WHERE SIREN = :siren AND date_vente BETWEEN :dd AND :df GROUP BY libelle"); 
                    $motifs -> bindParam(':dd', $dd);
                    $motifs -> bindParam(':df', $df);
                    $motifs -> bindParam(':siren', $SIREN);
                    $verif = $motifs -> execute();
                    if (empty($verif)) {
                        exit("Erreur lors de la sélection");
                    }
                    $motifs = $motifs -> fetchAll();
                    $array_motifs = array("fraude a la carte" => 0, "compte a decouvert" => 0, "compte cloture" => 0, "compte bloque" => 0, "provision insuffisante" => 0, "operation contestee par le debiteur" => 0, "titulaire decede" => 0, "raison non communiquee, contactez la banque du client" => 0);
                    foreach($motifs AS $ligne) { // ajoute le nombre de motifs dans array_motifs
                        $array_motifs[$ligne['libelle']] = $ligne['nb_motifs'];
                    }
                    include("graphics/circular.php");
                } 
                else 
                {    
                    // récupère la somme des montants et la date de vente des chiffre d'affaires entre deux dates
                    $chiffre_affaires = $cnx -> prepare("SELECT SUM(montant) AS montant, date_vente FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction WHERE SIREN = :siren AND date_vente BETWEEN :dd AND :df GROUP BY date_vente ORDER BY date_vente"); 
                    $chiffre_affaires -> bindParam(':dd', $dd);
                    $chiffre_affaires -> bindParam(':df', $df);
                    $chiffre_affaires -> bindParam(':siren',$SIREN);
                    $verif = $chiffre_affaires -> execute();
                    if (empty($verif)) {
                        exit("Erreur lors de la sélection");
                    }
                    $chiffre_affaires = $chiffre_affaires -> fetchAll();
                    $array_chiffre_affaires = array();
                    $array_dates = array();
                    foreach($chiffre_affaires AS $ligne) { // ajoute les montants de chiffre d'affaires dans array_chiffre_affaires et les dates dans array_dates
                        array_push($array_chiffre_affaires, (float)$ligne['montant']);
                        array_push($array_dates, $ligne['date_vente']);
                    }
    
                    // récupère la somme des montants et la date de vente des impayés entre deux dates
                    $impayes = $cnx -> prepare("SELECT SUM(montant) AS montant, date_vente FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction NATURAL JOIN Impaye WHERE SIREN = :siren AND date_vente BETWEEN :dd AND :df GROUP BY date_vente ORDER BY date_vente"); 
                    $impayes -> bindParam(':dd', $dd);
                    $impayes -> bindParam(':df', $df);
                    $impayes -> bindParam(':siren', $SIREN);
                    $verif = $impayes -> execute();
                    if (empty($verif)) {
                        exit("Erreur lors de la sélection");
                    }
                    $impayes = $impayes -> fetchAll();
                    $array_impayes = array();
                    for ($i = 0; $i < count($array_dates); $i++) { // ajoute les montants d'impayés dans array_impayes par rapport aux dates d'actions et met 0 si aucun impayés n'a eu lieu à une date
                        $inserted = 0;
                        foreach($impayes AS $ligne) {
                            if ($ligne['date_vente'] == $array_dates[$i]) {
                                array_push($array_impayes, (float)$ligne['montant']);
                                $inserted = 1;
                            }
                        }
                        if ($inserted == 0) {
                            array_push($array_impayes, (float)0);
                        } 
                    }
    
                    $liste_mois = array("01" => "Jan", "02" => "Fév", "03" => "Mars", "04" => "Avr", "05" => "Mai", "06" => "Juin", "07" => "Jui", "08" => "Août", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Déc");
                    $en_mois = 0;
                    if (count($array_dates) > 2 && dateDiffMois($array_dates[0], $array_dates[count($array_dates)-1]) > 1) {
                        $en_mois = 1;
                    }
    
                    if ($en_mois == 1) { // si la première et la dernière date de array_dates sont supérieurs à 1 mois, on transforme les array par dates/jours en array par mois et additionne les montants d'un mois
                        $array_tmp1 = array();
                        $array_tmp2 = array();
                        $array_tmp3 = array();
                        for ($i = 0; $i < count($array_dates); $i++) {
                            if ($i == 0) 
                            {
                                $mois_actuel = date('Y-m', strtotime($array_dates[$i]));
                                $montant_chiffre_affaires = $array_chiffre_affaires[$i]; 
                                $montant_impayes = $array_impayes[$i];
                            } 
                            else if (date('Y-m', strtotime($array_dates[$i])) != $mois_actuel) 
                            {
                                array_push($array_tmp1, $montant_chiffre_affaires);
                                array_push($array_tmp2, $montant_impayes);
                                array_push($array_tmp3, $liste_mois[date('m', strtotime($mois_actuel))]." ".date('Y', strtotime($mois_actuel)));
    
                                $mois_actuel = date('Y-m', strtotime($array_dates[$i]));
                                $montant_chiffre_affaires = $array_chiffre_affaires[$i]; 
                                $montant_impayes = $array_impayes[$i];
                            } 
                            else 
                            {
                                $montant_chiffre_affaires += $array_chiffre_affaires[$i]; 
                                $montant_impayes += $array_impayes[$i];
                            }
                        }
                        $array_chiffre_affaires = $array_tmp1;
                        $array_impayes = $array_tmp2;
                        $array_dates = $array_tmp3;
                    }
    
                    if ($GRAPHIQUE == "lr") { // si la variable $graphique est égale à lr (linéaire), on include un graphique linéaire, sinon on include un graphique histogramme
                        include("graphics/linear.php");
                    } else {
                        include("graphics/histogram.php");
                    }
                }
            }
        ?>
	</body>
</html>
