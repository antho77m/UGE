 
<?php
    include("../includes/cnx.inc.php");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trésorerie</title>
    
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
        $result = $sql->fetch(PDO::FETCH_OBJ);
        $ligne = $result;
        
        if($ligne->montant_total >= 0) {
            echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : green;">' . $ligne->montant_total . '</span> Date : ' . $date . '</p><br>';
        }
        else {
            echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : red;">' . $ligne->montant_total . '</span> Date : ' . $date . '</p><br>';
        }

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
        
        if($ligne->montant_total >= 0) {
            echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : green;">' . $ligne->montant_total . '</span> Date : ' . date('m-d-Y', time()) . '</p><br>';
        }
        else {
            echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : red;">' . $ligne->montant_total . '</span> Date : ' . date('m-d-Y', time()) . '</p><br>';
        }
    }

    showTreasury($SIREN);
    include("user_treasury_graphics.php");
?>

        <form action="" method="post">
            <div>
                <div>
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date">                          
                </div>
            </div>
            <input type="submit" name="submit" value="Valider" />
        </form>
<?php

    if(isset($_POST['submit'])){
        if(isset($_POST['date'])) {
            $date = $_POST['date'];
            array_push($array_export,show_treasury_client_date($SIREN, $date));
            echo "<button class=\"export\" onclick=\"window.open(\'/pages/exports/export_treasury.php?format=CSV&date=$date\', \'_blank\');\">CSV</button>
            <button class=\"export\" onclick=\"window.open(\'/pages/exports/export_treasury.php&format=XLS?date=$date\', \'_blank\');\">XLS</button>
            <button class=\"export\" onclick=\"window.open(\'/pages/exports/export_treasury.php&format=PDF?date=$date\', \'_blank\');\">PDF</button>";
        }
    }

    ?>
    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>