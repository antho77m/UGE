<?php

function CountTransac($SIREN, $date) { // Count the number of transactions has a date 

    // include("cnx.inc.php");
    include("cnx.inc.php");

    $command2 = $cnx->query("SELECT COUNT(percevoir.num_autorisation) AS nb_transaction FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND Transaction.num_autorisation = percevoir.num_autorisation AND Transaction.date_vente = '$date'");
    while ($ligne2 = $command2->fetch(PDO::FETCH_OBJ)) // un par un
    {
        $nb_transac = $ligne2->nb_transaction;
    }

    return $nb_transac;
}

function CountMontant($nb_transac, $SIREN, $date) // Count the solde has a date
{

    include("cnx.inc.php");

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
        return $montant; // return the solde
    }
    return $montant;
}



function CountAllTransac($SIREN) { // Count the numbers of all transaction  

    // include("cnx.inc.php");
    include("cnx.inc.php");

    $command = $cnx->query("SELECT COUNT(percevoir.num_autorisation) AS nb_transaction FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND Transaction.num_autorisation = percevoir.num_autorisation");
    $ligne = $command->fetch(PDO::FETCH_OBJ);
    return $ligne->nb_transaction; // return the number of transaction
}

function CountMontantOfAllTransac($nb_transac, $SIREN) { // Count the solde of all transaction

    include("cnx.inc.php");

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
    return $montant; // return the solde of all transaction
}

?>