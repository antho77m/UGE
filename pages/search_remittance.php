<?php
// include("../includes/cnx.inc.php");

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
    <title>Remises</title>
</head>

<body>
    <!-- <form action="search_remittance.php" method="post"> -->

    <section class="remittance_sect">

        <p style="font-size: 24px;">Consulation des remises</p>

        <form action="/remittance" method="POST" class="client__form">

            <?php if ($_SESSION['niveau'] == 3) : ?>
                <div class="form__group">
                    <label for="name">N° de SIREN</label>
                    <div class="input__container">
                        <input type="number" name="siren" id="siren" placeholder="SIREN" autocomplete="off">
                    </div>
                </div>

                <div class="form__group">
                    <label for="name">Nom du client</label>
                    <div class="input__container">
                        <input type="text" name="rsociale" id="rsociale" placeholder="Raison sociale" autocomplete="off">
                    </div>
                </div>
            <?php endif; ?>

            <div class="form__group">
                <label for="name">Date de début</label>
                <div class="input__container">
                    <input type="date" name="dd" id="dd" value="2010-01-01">
                </div>
            </div>

            <div class="form__group">
                <label for="name">Date de fin</label>
                <div class="input__container">
                    <input type="date" name="df" id="df" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <button type="submit" name="submit" value="Validez" class="btn" style="margin-top: 30px;">Confirmer</button>

        </form>
    </section>

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
            $remises = $cnx->prepare("SELECT DISTINCT SIREN, date_traitement FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction WHERE SIREN LIKE :siren AND Raison_sociale LIKE :raison_sociale AND date_traitement BETWEEN :dd AND :df");
            $remises->bindParam(':siren', $SIREN);
            $remises->bindParam(':raison_sociale', $Raison_Sociale);
            $remises->bindParam(':dd', $dd);
            $remises->bindParam(':df', $df);
        } else {
            $remises = $cnx->prepare("SELECT DISTINCT SIREN, date_traitement FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction WHERE SIREN LIKE :siren AND date_traitement BETWEEN :dd AND :df");
            $remises->bindParam(':siren', $SIREN);
            $remises->bindParam(':dd', $dd);
            $remises->bindParam(':df', $df);
        }
        $verif = $remises->execute();
        $remises = $remises->fetchAll();
        echo '<b style="margin-left: 18px;">Résultat: ' . count($remises) . '</b><br><br>';

        // EXPORT DES DONNEES
        if (count($remises) > 0) {
            echo '
            <p style="margin-left: 18px;">Exporter les résultats en :</p>
            <div class="export_wrap">
            <button class="export" onclick="window.open(\'/export?format=CSV&detail=0\', \'_blank\');">CSV</button>
            <button class="export" onclick="window.open(\'/export?format=XLSX&detail=0\', \'_blank\');">XLSX</button>
            <button class="export" onclick="window.open(\'/export?format=CSV&detail=1\', \'_blank\');">CSV détaillé</button>
            <button class="export" onclick="window.open(\'/export?format=XLSX&detail=1\', \'_blank\');">XLSX détaillé</button>
            </div>
            ';
        }
        $array_remises = array();
        $array_remises_detailles = array();
        foreach ($remises as $ligne) { // un par un
            $total_remises = $cnx->prepare("SELECT SIREN, Raison_sociale, num_remise, date_traitement, count(num_autorisation) AS nb_transactions, SUM(montant) AS montant_total, (SELECT SUM(montant)*2 FROM Transaction WHERE num_remise = R.num_remise AND sens = '-') AS montant_impayes
            FROM Commercant 
            NATURAL JOIN percevoir 
            NATURAL JOIN Transaction AS R
            WHERE SIREN = :siren AND date_traitement = :date
            GROUP BY num_remise");
            $total_remises->bindParam(':siren', $ligne['SIREN']);
            $total_remises->bindParam(':date', $ligne['date_traitement']);
            $verif = $total_remises->execute();
            if (empty($verif)) {
                exit("Erreur lors de la sélection");
            }
            $total_remises = $total_remises->fetch();

            if (!empty($total_remises)) {
                // echo "<b>" . $total_remises['SIREN'] . " " . $total_remises['Raison_sociale'] . " " . $total_remises['num_remise'] . " " . $total_remises['date_traitement'] . " " . $total_remises['nb_transactions'] . " EUR ";
                $montant_total = $total_remises['montant_total'] - $total_remises['montant_impayes'];
                
                echo '
                <div class="remittance_results">
                    <div class="remittance_result">
                        <p style="font-size: 16px;">SIREN</p>
                        <p style="font-size: 18px;">' . $total_remises['SIREN'] . '</p>
                    </div>

                    <div class="remittance_result">
                        <p style="font-size: 16px;">Raison sociale</p>
                        <p style="font-size: 18px;">' . $total_remises['Raison_sociale'] . '</p>
                    </div>

                    <div class="remittance_result">
                        <p style="font-size: 16px;">Numéro de remise</p>
                        <p style="font-size: 18px;">' . $total_remises['num_remise'] . '</p>
                    </div>

                    <div class="remittance_result">
                        <p style="font-size: 16px;">Date de traitement</p>
                        <p style="font-size: 18px;">' . $total_remises['date_traitement'] . '</p>
                    </div>

                    <div class="remittance_result">
                        <p style="font-size: 16px;">Nombre de transactions</p>
                        <p style="font-size: 18px;">' . $total_remises['nb_transactions'] . '</p>
                    </div>

                    <div class="remittance_result">
                        <p style="font-size: 16px;">Montant</p>
                        <p style="font-size: 18px;">' . $total_remises['montant_total'] . '</p>
                    </div>

                    <div class="remittance_result">
                        <p style="font-size: 16px;">Montant impayés</p>
                        <p style="font-size: 18px;">' . ($total_remises['montant_impayes'] == 0 ? "0" : $total_remises['montant_impayes']) . '</p>
                    </div>
                    
                    <div class="remittance_result' . ($montant_total >= 0 ? ' positive' : ' negative') . '">
                        <p style="font-size: 16px;">Montant total</p>
                        <p class="montant" style="font-size: 18px;">' . ($montant_total >= 0 ? ('+' . $montant_total) : $montant_total) . '</p>
                    </div>
                </div>';

                // if ($montant_total >= 0) {
                //     echo "+";
                // }
                // echo "$montant_total</b><br>";
                array_push($array_remises, [$total_remises['SIREN'], $total_remises['Raison_sociale'], $total_remises['num_remise'], $total_remises['date_traitement'], $total_remises['nb_transactions'], "EUR", $montant_total]);

                $details_remises = $cnx->prepare("SELECT SIREN, date_vente, date_traitement, num_carte, reseau, num_autorisation, montant, sens FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction WHERE SIREN = :siren AND date_traitement = :date");
                $details_remises->bindParam(':siren', $ligne['SIREN']);
                $details_remises->bindParam(':date', $ligne['date_traitement']);
                $verif = $details_remises->execute();
                if (empty($verif)) {
                    exit("Erreur lors de la sélection");
                }
                $details_remises = $details_remises->fetchAll();
                array_push($array_remises_detailles, $details_remises);
                foreach ($details_remises as $ligne) {
                    // echo $ligne['SIREN'] . " " . $ligne['date_vente'] . " " . $ligne['num_carte'] . " " . $ligne['reseau'] . " " . $ligne['num_autorisation'] . " EUR " . $ligne['sens'] . $ligne['montant'] . "<br>";

                    echo '
                    <div class="remittance_results">
                        <div class="remittance_result">
                            <p style="font-size: 16px;">Date de vente</p>
                            <p style="font-size: 18px;">' . $ligne['date_vente'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 16px;">N° de carte</p>
                            <p style="font-size: 18px;">' . $ligne['num_carte'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 16px;">Réseau</p>
                            <p style="font-size: 18px;">' . $ligne['reseau'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 16px;">N° d\'autorisation</p>
                            <p style="font-size: 18px;">' . $ligne['num_autorisation'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 16px;">Devise</p>
                            <p style="font-size: 18px;">EUR</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 16px;">Sens</p>
                            <p style="font-size: 18px;">' . $ligne['sens'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 16px;">Montant</p>
                            <p style="font-size: 18px;">' . $ligne['montant'] . '</p>
                        </div>
                    </div>';
                }
            }
            echo "<br><br>";
        }
        $_SESSION['tab_remises'] = $array_remises;
        $_SESSION['tab_remises_detailled'] = $array_remises_detailles;
        // echo "<button onclick=\"window.open('exports/export_remittance.php?format=CSV&detail=0', '_blank');\">CSV</button>";
        // echo "<button onclick=\"window.open('exports/export_remittance.php?format=XLSX&detail=0', '_blank');\">XLSX</button>";
        // echo "<button onclick=\"window.open('exports/export_remittance.php?format=CSV&detail=1', '_blank');\">CSV détaillé</button>";
        // echo "<button onclick=\"window.open('exports/export_remittance.php?format=XLSX&detail=1', '_blank');\">XLSX détaillé</button>";

        
    }
    ?>

</body>

</html>