<?php 
include(dirname(__FILE__, 2) . "/includes/cnx.inc.php");
include (dirname(__FILE__, 2) . "/includes/components/nav2.php") ;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Remises</title>
    </head>
    <body>
    
<?php
    if ((isset($_POST['dd']) && isset($_POST['df']))) {
        $SIREN;
        $Raison_Sociale;
        $dd = $_POST['dd'];
        $df = $_POST['df'];
        if ($_SESSION['niveau'] == 3) {
            if (!isset($_POST['siren']) || !isset($_POST['rsociale'])) {
                exit("Erreur 401");
            }
            if (!empty($_POST['siren'])) {
                $SIREN = $_POST['siren'];
            } else {
                $SIREN = "%";
            }
            if (!empty($_POST['rsociale'])) {
                $Raison_Sociale = $_POST['rsociale'];
            } else {
                $Raison_Sociale = "%";
            }
        } else if ($_SESSION['niveau'] == 1) {
            if (!isset($_SESSION['SIREN'])) {
                exit("Erreur 401");
            }
            $SIREN = $_SESSION['SIREN'];
        } else {
            exit("Erreur 401");
        }
        
        if (isset($_POST['rsociale'])) {
            $remises = $cnx -> prepare("SELECT DISTINCT SIREN, date_traitement FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction WHERE SIREN LIKE :siren AND Raison_sociale LIKE :raison_sociale AND date_traitement BETWEEN :dd AND :df");
            $remises -> bindParam(':siren', $SIREN);
            $remises -> bindParam(':raison_sociale', $Raison_Sociale);
            $remises -> bindParam(':dd', $dd);
            $remises -> bindParam(':df', $df);
        } else {
            $remises = $cnx -> prepare("SELECT DISTINCT SIREN, date_traitement FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction WHERE SIREN LIKE :siren AND date_traitement BETWEEN :dd AND :df");
            $remises -> bindParam(':siren', $SIREN);
            $remises -> bindParam(':dd', $dd);
            $remises -> bindParam(':df', $df);
        }
        $verif = $remises -> execute();
        $remises = $remises -> fetchAll();
        echo "<b>Résultat: ".count($remises)."</b><br>";
        
        $array_remises = array();
        $array_remises_detailles = array();
        foreach($remises AS $ligne) { // un par un
            $total_remises = $cnx->prepare("SELECT SIREN, Raison_sociale, num_remise, date_traitement, count(num_autorisation) AS nb_transactions, SUM(montant) AS montant_total, (SELECT SUM(montant)*2 FROM Transaction WHERE num_remise = R.num_remise AND sens = '-') AS montant_impayes
            FROM Commercant 
            NATURAL JOIN percevoir 
            NATURAL JOIN Transaction AS R
            WHERE SIREN = :siren AND date_traitement = :date
            GROUP BY num_remise");
            $total_remises -> bindParam(':siren', $ligne['SIREN']);
            $total_remises -> bindParam(':date', $ligne['date_traitement']);
            $verif = $total_remises -> execute();
            if (empty($verif)) {
                exit("Erreur lors de la sélection");
            }
            $total_remises = $total_remises->fetch();
            
            if (!empty($total_remises)) {
                echo "<b>".$total_remises['SIREN']." ".$total_remises['Raison_sociale']." ".$total_remises['num_remise']." ".$total_remises['date_traitement']." ".$total_remises['nb_transactions']." EUR ";
                $montant_total = $total_remises['montant_total']-$total_remises['montant_impayes'];
                if ($montant_total >= 0) {
                    echo "+";
                }
                echo "$montant_total</b><br>";
                array_push($array_remises, [$total_remises['SIREN'], $total_remises['Raison_sociale'], $total_remises['num_remise'], $total_remises['date_traitement'], $total_remises['nb_transactions'], "EUR", $montant_total]);
                
                $details_remises = $cnx -> prepare("SELECT SIREN, date_vente, date_traitement, num_carte, reseau, num_autorisation, montant, sens FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction WHERE SIREN = :siren AND date_traitement = :date");
                $details_remises -> bindParam(':siren', $ligne['SIREN']);
                $details_remises -> bindParam(':date', $ligne['date_traitement']);
                $verif = $details_remises -> execute();
                if (empty($verif)) {
                    exit("Erreur lors de la sélection");
                }
                $details_remises = $details_remises -> fetchAll();
                array_push($array_remises_detailles, $details_remises);
                foreach($details_remises AS $ligne) {
                    echo $ligne['SIREN']." ".$ligne['date_vente']." ".$ligne['num_carte']." ".$ligne['reseau']." ".$ligne['num_autorisation']." EUR ".$ligne['sens'].$ligne['montant']."<br>";
                }
            }
            echo "<br>";
        }
        $_SESSION['tab1'] = $array_remises;
        $_SESSION['tab2'] = $array_remises_detailles;
        echo "<button onclick=\"window.open('exports/export_remittance.php?format=CSV&detail=0', '_blank');\">CSV</button>";
        echo "<button onclick=\"window.open('exports/export_remittance.php?format=XLSX&detail=0', '_blank');\">XLSX</button>";
        echo "<button onclick=\"window.open('exports/export_remittance.php?format=CSV&detail=1', '_blank');\">CSV détaillé</button>";
        echo "<button onclick=\"window.open('exports/export_remittance.php?format=XLSX&detail=1', '_blank');\">XLSX détaillé</button>";
    }
    else{
        ?>
            <div class="form">
            <form action="" method="post">
                <?php
                if ($_SESSION['niveau'] == 3) {?>
                    
                      <label for="name">SIREN :</label>
                      <input type="text" id="siren" name="siren" class="input_siren">
                      <label for="name">Raison Sociale :</label>
                      <input type="text" id="rsociale" name="rsociale" class="input_siren">
                      <?php
                }
                ?>
                <div class="form_options">
                <label for="start">date de début:</label>
                <input type="date" id="dd" name="dd" value="2010-01-01">
                <label for="start">date de fin:</label>
                <input type="date" id="df" name="df" value="2022-12-30">
                <input type="submit" name="submit" value="Validez" class = "boutton_formulaire"/>
            </form>
            </div>
            </div>
        <?php
    }
        ?>

</body>
</html>