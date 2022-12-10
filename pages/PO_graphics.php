<?php
// TODO: redirect vers cette page quand clic sur graphs navbar
session_start();
if (isset($_SESSION['niveau'])) {
    if ($_SESSION['niveau'] != 1) {
        header("Location: login.php");
    }
} else {
    exit("Erreur 401");
}
include(dirname(__FILE__, 2) . "/router.php");

include ROOT . "/includes/cnx.inc.php";

?>

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

    <input type="submit" name="submit" value="Générer un graphique" class="btn" style="margin-top:30px;" />
</form>

<?php
if (isset($_POST['dd']) && isset($_POST['df'])) {
    // récupère la somme des motifs des impayés entre deux dates  
    $motifs = $cnx->prepare("SELECT libelle, count(libelle) AS nb_motifs FROM Commercant NATURAL JOIN Transaction NATURAL JOIN Impaye JOIN Motifs_Impaye ON Impaye.code_motif = Motifs_Impaye.code WHERE date_vente BETWEEN :dd AND :df GROUP BY libelle");
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
    include("graphics/circular_graphics.php");
}
?>