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

        // include("cnx.inc.php");
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

    function AfficheTresorerie_Client_Date($SIREN) // fonction profil client
    { // Fonction qui affiche le solde des transactions du jour de la trésorerie du client à une date donnée

        global $cnx;

        echo '
            <p style="font-size: 24px; margin-bottom: 20px">Annonces trésorerie du client Profil Client</p>
            <form action="" method="post" class="client__form">
                <div class="form__group">
                    <div class="input__container">
                        <input type="date" id="date3" name="date3" value="2010-01-01">
                    </div>
                </div>

                <input type="submit" name="submit" value="Confirmer" class="btn" style="margin-top:30px;" />
            </form>';

        if (isset($_POST['date3']) && isset($SIREN)) {
            $date3 = $_POST['date3'];

            $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
            $ligne = $command->fetch(PDO::FETCH_OBJ);
            $nb_transac = CountTransac($ligne->SIREN, $date3);
            $montant = CountMontant($nb_transac, $ligne->SIREN, $date3);
            if ($montant > 0) {
                echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : + ' . $montant . 'Date : ' . $date3;
            }
            else {
                echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : ' . $montant . 'Date : ' . $date3;
            }
        }
    }

    function AfficheTresorerie_Client_Date_PO() // Profil PO
    { // Fonction qui affiche le solde des transactions du jour de la trésorerie des clients à une date donnée

        global $cnx;

        echo '
            <p style="font-size: 24px; margin-bottom: 20px">Annonce trésorerie des comptes clients PO</p>
            <form action="" method="post" class="client__form">
                <div class="form__group">
                    <div class="input__container">
                        <input type="date" id="date" name="date" value="2010-01-01">
                    </div>
                </div>

                <input type="submit" name="submit" value="Confirmer" class="btn" style="margin-top:30px;" />
            </form>';


        if (isset($_POST['date'])) {
            $date = $_POST['date'];
        } else {
            $date = "";
        }
        if (isset($date)) {
            if ($date != "") {
                $command = $cnx->query("SELECT * FROM Commercant");
                while ($ligne = $command->fetch(PDO::FETCH_OBJ)) {
                    $nb_transac = CountTransac($ligne->SIREN, $date);
                    $montant = CountMontant($nb_transac, $ligne->SIREN, $date);
                    if ($montant > 0) {
                        echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : +' . $montant . 'Date : ' . $date;
                    }
                    else {
                        echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : ' . $montant . 'Date : ' . $date;
                    }
                }
            }
        }
    }

    function AfficheTresorerie_Par_Client_Date_PO() // profil PO
    { // Fonction qui affiche le solde des transactions du jour de la trésorerie d'un client à une date donnée

        global $cnx;

        echo '
            <p style="font-size: 24px; margin-bottom: 20px">Annonces trésorerie par compte client PO</p>
            <form action="" method="post" class="client__form">
                <div class="form__group">
                    
                    <select name="SIREN1" id="SIREN1">"
                    <option value="">SIREN</option>';
                    $command = $cnx->query("SELECT SIREN FROM Commercant");
                    while ($ligne = $command->fetch(PDO::FETCH_OBJ)) {
                        echo "<option value='$ligne->SIREN'>$ligne->SIREN</option>";
                    }
        echo '
                    </select>
                    <div class="input__container">
                        <input type="date" id="date1" name="date1" value="2010-01-01">
                    </div>
                    
                    
                </div>

                <input type="submit" name="submit" value="Confirmer" class="btn" style="margin-top:30px;" />
            </form>';


        if (isset($_POST['date1']) && $_POST['SIREN1']) {
            $date1 = $_POST['date1'];
            $SIREN1 = $_POST['SIREN1'];
        } else {
            $date1 = "";
            $SIREN1 = "";
        }
        if (isset($date1) && isset($SIREN1)) {
            if ($date1 != "" && $SIREN1 != "") {

                $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN1'");
                $ligne = $command->fetch(PDO::FETCH_OBJ);
                $nb_transac = CountTransac($ligne->SIREN, $date1);
                $montant = CountMontant($nb_transac, $ligne->SIREN, $date1);
                if ($montant > 0) {
                    echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : +' . $montant . 'Date : ' . $date1;
                }
                else {
                    echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : ' . $montant . 'Date : ' . $date1;
                }
            }
        }
    }

    function CountAllTransac($SIREN)
    { // Fonction qui compte le nombre de transaction d'un client

        // include("cnx.inc.php");
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

    function AfficheTresorerie_Client_Solde_PO()
    { // Fonction qui affiche le solde des transactions totale de la trésorerie de tout client

        // include("cnx.inc.php");
        global $cnx;

        echo '<p style="font-size: 24px; margin-bottom: 20px">Annonces trésorerie des comptes clients PO solde négatif</p>';
        $commercant_array = array();
        $i = 0;
        $command = $cnx->query("SELECT * FROM Commercant");

        while ($ligne = $command->fetch(PDO::FETCH_OBJ)) {
            $nb_transac = CountAllTransac($ligne->SIREN);
            $montant = CountMontantOfAllTransac($nb_transac, $ligne->SIREN);
            if ($montant > 0) {
                echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : +' . $montant;
            }
            else {
                echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : <span>'.$montant.'</span>';
            }
        }
        return $commercant_array; // Retourne un tableau d'objet commercant. Penser a afficher le solde en rouge si negatif
    }

    function AfficheTresorerie_Client_Solde_And_SIREN_PO() //Profil PO
    { // Fonction qui affiche le solde des transactions totale de la trésorerie d'un client

        // include("cnx.inc.php");
        global $cnx;

        echo '
            <p style="font-size: 24px; margin-bottom: 20px">Annonces trésorerie des comptes clients PO solde ou SIREN</p>
            <form action="" method="post" class="client__form">
                <div class="form__group">
                    
                    <select name="SIREN" id="SIREN">"
                    <option value="">SIREN</option>';
                    $command = $cnx->query("SELECT SIREN FROM Commercant");
                    while ($ligne = $command->fetch(PDO::FETCH_OBJ)) {
                        echo "<option value='$ligne->SIREN'>$ligne->SIREN</option>";
                    }
        echo '
                    </select>
                    <div style="display: flex; gap:20px">
                        <div class="input__container">
                            <input type="number" name="min_solde" id="min_solde" value="0">
                        </div>
                        <div class="input__container">
                            <input type="number" name="max_solde" id="max_solde" value="0">
                        </div>
                    </div>
                </div>
                <input type="submit" name="submit" value="Confirmer" class="btn" style="margin-top:30px;" />
            </form>'
        ;

        if (isset($_POST['SIREN']) && isset($_POST['min_solde']) && isset($_POST['max_solde'])) {
            $SIREN = $_POST['SIREN'];
            $min_solde = $_POST['min_solde'];
            $max_solde = $_POST['max_solde'];
            $nb_transac = CountAllTransac($SIREN);
            $montant = CountMontantOfAllTransac($nb_transac, $SIREN);
            if ($montant >= $min_solde && $montant <= $max_solde) {
                $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
                $ligne = $command->fetch(PDO::FETCH_OBJ);
                if ($montant > 0) {
                    echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : +' . $montant;
                }
                else {
                    echo '<p>SIREN : '  .$ligne->SIREN. 'Raison sociale : ' .$ligne->Raison_sociale . 'Nombre de transactions : '. $nb_transac . 'Montant total : <span style = "color: var(--red)">' .  $montant . '</span> <<p>';
                }
            } else {
                return null;
            }
        }
    }
    //  affiche le jour d'aujourd'hui
    echo date("d/m/Y");
    echo '<div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>';

    ?>
    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>