<?php

session_start();
include(dirname(__FILE__, 2) . "/router.php");

include ROOT . "/includes/cnx.inc.php";

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trésorerie</title>
    <meta property="og:description" content="Trésorerie">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
</head>

<body>

    <?php include ROOT . "/includes/components/nav.php"; ?>

    <section class="graphics_section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 24px;">Trésorerie</p>
            <div class="logos pc-nav">
                <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
                    <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
                </div>

                <div class="icon_container" onclick="window.location.href='/pages/search_unpaid.php'">
                    <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
                </div>

                <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
                    <img src="<?= $basepath ?>/src/img/treasury.svg" alt="Treasury icon">
                </div>

                <div class="icon_container" onclick="window.location.href='/pages/search_remittance.php'">
                    <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
                </div>
            </div>
        </div>

        <?php
        AfficheTresorerie_Client_Date_PO();
        AfficheTresorerie_Par_Client_Date_PO();
        AfficheTresorerie_Client_Date("320367139");
        AfficheTresorerie_Client_Solde_PO();
        AfficheTresorerie_Client_Solde_And_SIREN_PO();
        ?>

    </section>
    <div class="navbar mobile-nav">
        <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
            <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/search_unpaid.php'">
            <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/home.php'">
            <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
            <img src="<?= $basepath ?>/src/img/treasury.svg" alt="Treasury icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/search_remittance.php'">
            <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
        </div>
    </div>
    <?php

    function CountTransac($SIREN, $date)
    { // Fonction qui compte le nombre de transaction d'un commercant à une date donnée

    
        global $cnx;

        $command2 = $cnx->query("SELECT COUNT(percevoir.num_autorisation) AS nb_transaction FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND Transaction.num_autorisation = percevoir.num_autorisation AND Transaction.date_vente = '$date'");
        while ($ligne2 = $command2->fetch(PDO::FETCH_OBJ)) // un par un
        {
            $nb_transac = $ligne2->nb_transaction;
        }

        return $nb_transac;
    }

    function CountMontant($nb_transac, $SIREN, $date)
    { // Fonction qui compte le montant total des transactions d'un commercant à une date donnée

        global $cnx;

        $montant = 0;
        if ($nb_transac != 0) {

            $command3 = $cnx->query("SELECT sens, montant FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND percevoir.num_autorisation = Transaction.num_autorisation AND date_vente = '$date'");
            while ($ligne3 = $command3->fetch(PDO::FETCH_OBJ)) // un par un
            {
                if ($ligne3->sens == "-") {

                    $montant = $montant - $ligne3->montant;
                } else {

                    $montant = $montant + $ligne3->montant;
                }
            }
            return $montant;
        }
        return $montant;
    }

    function show_treasury_client_date($SIREN) // fonction profil client
    { // Fonction qui affiche le solde des transactions du jour de la trésorerie du client à une date donnée

        require_once("include/class/commercant.php");
        global $cnx;

        echo '
            <p style="font-size: 24px; margin-bottom: 20px">Annonces trésorerie du client Profil Client</p>
            <form action="" method="post" class="client__form">
                <div class="form__group">
                    <div class="input__container">
                        <input type="date" id="date" name="date" value="2010-01-01">
                    </div>
                </div>

                <input type="submit" name="submit" value="Confirmer" class="btn" style="margin-top:30px;" />
            </form>';

        if (isset($_POST['date']) && isset($SIREN)) {
            $date = $_POST['date'];

            $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
            $ligne = $command->fetch(PDO::FETCH_OBJ);
            $nb_transac = CountTransac($ligne->SIREN, $date);
            $montant = CountMontant($nb_transac, $ligne->SIREN, $date);
            if ($montant > 0) {
                echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : + ' . $montant . 'Date : ' . $date;
            }
            else {
                echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : ' . $montant . 'Date : ' . $date;
            }
        }
    }


    function CountAllTransac($SIREN)
    { // Fonction qui compte le nombre de transaction d'un client

        global $cnx;

        $command = $cnx->query("SELECT COUNT(percevoir.num_autorisation) AS nb_transaction FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND Transaction.num_autorisation = percevoir.num_autorisation");
        $ligne = $command->fetch(PDO::FETCH_OBJ);
        return $ligne->nb_transaction;
    }

    function CountMontantOfAllTransac($nb_transac, $SIREN)
    { // Fonction qui compte le montant de toutes les transactions d'un client

        global $cnx;

        if ($nb_transac == 0) {
            return 0;
        }
        $montant = 0;
        $command = $cnx->query("SELECT sens, montant FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND percevoir.num_autorisation = Transaction.num_autorisation");
        while ($ligne = $command->fetch(PDO::FETCH_OBJ)) // un par un
        {

            if ($ligne->sens == "-") {

                $montant = $montant - $ligne->montant;
            } else {

                $montant = $montant + $ligne->montant;
            }
        }
        return $montant;
    }

    function showTreasury($SIREN) // Affiche la trésorerie du client au jour même
    {
        require_once("include/class/commercant.php");
        global $cnx;

        $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
        $ligne = $command->fetch(PDO::FETCH_OBJ);

        $nb_transac = CountAllTransac($SIREN);
        $montant = CountMontantOfAllTransac($nb_transac, $SIREN);
        if ($montant > 0) {
            echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : + ' . $montant;
        }
        else {
            echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : ' . $montant;
        }
    }

    function show_treasury_client_datemax($SIREN, $date){
        require_once("include/class/commercant.php");
        global $cnx;
        
        $command = $cnw->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
        $ligne = $command->fetch(PDO::FETCH_OBJ);

        $nb_transac = CountAllTransac($SIREN);
        if($nb_transac > 0){
            $montant = 0;
            $command2 = $cnx->query("SELECT montant, sens, date_vente FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND percevoir.num_autorisation = Transaction.num_autorisation AND Transaction.date_transaction <= '$date'");
            while($ligne2 = $command2->fetch(PDO::FETCH_OBJ)){
                if($ligne2->sens == "-"){
                    $montant = $montant - $ligne2->montant;
                }
                else{
                    $montant = $montant + $ligne2->montant;
                }
            }
            $commercant = new commercant($SIREN, $ligne->Raison_sociale, $nb_transac, $montant,null);
            
            return $commercant;
        }

    }
    //  affiche le jour d'aujourd'hui
    echo date("d/m/Y");
    echo '<div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>';

    ?>
    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>