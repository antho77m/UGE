<?php
include("cnx.inc.php");
session_start();
if (!isset($_SESSION['niveau'])) {
    exit("Erreur 401");
}
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
    <form action="search_unpaid.php" method="post">
        <label for="start">date de début:</label>
        <input type="date" id="dd" name="dd" value="2010-01-01">
        <label for="start">date de fin:</label>
        <input type="date" id="df" name="df" value="2022-12-30">
        <input type="radio" id="desc" name="sens" value="DESC" required>
        <label for="">décroissant</label>
        <input type="radio" id="asc" name="sens" value="ASC" required>
        <label for="">croissant</label>
        <input type="submit" name="submit" value="Validez" class = "boutton_formulaire"/>
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
        $impayes = $impayes -> fetchAll();
<<<<<<< Updated upstream
=======
        echo '
            <p style="margin-left: 18px;">Exporter les résultats en :</p>
            <div class="export_wrap_2">
            <button class="export" onclick="window.open(\'/export?format=CSV&detail=0\', \'_blank\');">CSV</button>
            <button class="export" onclick="window.open(\'/export?format=XLSX&detail=0\', \'_blank\');">XLSX</button>
            </div>
            ';
>>>>>>> Stashed changes
        foreach($impayes AS $ligne) {
            echo $ligne['SIREN']." ".$ligne['date_vente']." ".$ligne['date_traitement']." ".$ligne['num_carte']." ".$ligne['reseau']." ".$ligne['num_dos']." EUR ".$ligne['montant']." ".$ligne['libelle']."<br>";
        }
        $_SESSION['tab'] = $impayes;
        echo "<button onclick=\"window.open('exports/export_unpaid.php?format=CSV', '_blank');\">CSV</button>";
        echo "<button onclick=\"window.open('exports/export_unpaid.php?format=XLSX', '_blank');\">XLSX</button>";
    }
?>
</body>
</html>