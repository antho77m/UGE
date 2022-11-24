<?php
require_once(dirname(__FILE__, 2) . '/includes/cnx.inc.php');

session_start();
$SIREN = "";
$DATES = "";
if (isset($_SESSION['SIREN']) && isset($_SESSION['niveau']) && $_SESSION['niveau'] == 1) {
    $SIREN = "= '" . $_SESSION['SIREN'] . "'"; // PROBLEME DE SIREN
}
if (isset($_POST['dd']) && isset($_POST['df'])) {
    $DATES = "BETWEEN '" . $_POST['dd'] . "' AND '" . $_POST['df'] . "'";
}
$infos_remises = $cnx->query("SELECT DISTINCT SIREN, date_traitement FROM percevoir NATURAL JOIN Transaction WHERE SIREN $SIREN AND date_traitement " . $DATES);

$montant_total = 0;
$nb_transactions = 0;
while ($ligne = $infos_remises->fetch(PDO::FETCH_OBJ)) { // un par un
    $req = $cnx->prepare("SELECT SIREN, Raison_sociale, num_remise, date_traitement, count(num_autorisation) AS nb_transactions, SUM(montant) AS montant_p, (SELECT SUM(montant)*2 FROM Transaction WHERE num_remise = R.num_remise AND sens = '-') AS montant_n
        FROM Commercant 
        NATURAL JOIN percevoir 
        NATURAL JOIN Transaction AS R
        WHERE SIREN = :siren AND date_traitement = :date
        GROUP BY num_remise");
    $req->execute(array(
        'date' => $ligne->date_traitement,
        'siren' => $ligne->SIREN
    ));
    $total_remises = $req->fetch();

    if ($total_remises) {
        echo "<b>" . $total_remises['SIREN'] . " " . $total_remises['Raison_sociale'] . " " . $total_remises['num_remise'] . " " . $total_remises['date_traitement'] . " " . $total_remises['nb_transactions'] . " ";
        $montant_total = $total_remises['montant_p'] - $total_remises['montant_n'];
        if ($montant_total > 0) {
            echo "+";
        }
        echo "$montant_total</b><br>";

        $details_remise = $cnx->query("SELECT SIREN, date_vente, num_carte, reseau, num_autorisation, montant, sens FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction WHERE SIREN = '$ligne->SIREN' AND date_traitement = '$ligne->date_traitement'");
        while ($remise = $details_remise->fetch(PDO::FETCH_OBJ)) {
            echo "$remise->SIREN $remise->date_vente $remise->num_carte $remise->reseau $remise->num_autorisation ";
            if ($remise->montant > 0) {
                echo "+";
            }
            echo "$remise->montant<br>";
        }
        $details_remise->closeCursor();
    }
    echo "<br>";
}
$infos_remises->closeCursor(); // on ferme le curseur
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
    <form action="/dd" method="post">
        <label for="start">date de début:</label>
        <input type="date" id="dd" name="dd" value="2010-01-01">
        <label for="start">date de fin:</label>
        <input type="date" id="df" name="df" value="2022-12-30">
        <input type="submit" name="submit" value="Validez" class="boutton_formulaire" />
    </form>

</body>

</html>