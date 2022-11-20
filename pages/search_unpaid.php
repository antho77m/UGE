<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remises</title>
</head>
<body>
    <form action="search_unpaid.php" method="post">
        <label for="start">date de début:</label>
        <input type="date" id="dd" name="dd" value="2010-01-01">
        <label for="start">date de fin:</label>
        <input type="date" id="df" name="df" value="2022-12-30">
        <input type="submit" name="submit" value="Validez" class = "boutton_formulaire"/>
    </form>

<?php
    include("cnx.inc.php");

    session_start();
    $SIREN = "";
    $DATES = "";
    if (isset($_SESSION['SIREN']) && isset($_SESSION['niveau']) && $_SESSION['niveau'] == 1) {
        $SIREN = "= '".$_SESSION['SIREN']."'"; // PROBLEME DE SIREN
    }
    if (isset($_POST['dd']) && isset($_POST['df'])) {
        $DATES = "BETWEEN '".$_POST['dd']."' AND '".$_POST['df']."'";
    }
    $impayes = $cnx -> query("SELECT SIREN, date_vente, date_traitement, num_carte, reseau, num_dos, montant, libelle FROM Commercant NATURAL JOIN percevoir NATURAL JOIN Transaction NATURAL JOIN Impaye WHERE SIREN $SIREN AND date_traitement ".$DATES." ORDER BY SIREN");

    $montant_total = 0;
    $nb_transactions = 0;
    while ( $ligne = $impayes -> fetch(PDO::FETCH_OBJ) ) { // un par un
        echo "$ligne->SIREN $ligne->date_vente $ligne->date_traitement $ligne->num_carte $ligne->reseau $ligne->num_dos $ligne->montant $ligne->libelle <br>";
    }
    $impayes -> closeCursor();
?>

</body>
</html>