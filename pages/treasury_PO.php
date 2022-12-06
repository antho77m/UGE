
<?php require_once("commercant.php");?> 
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

    function show_treasury_all_client_date($date) { // Show the solde of all client has a date

        include("cnx.inc.php");
        //require_once("commercant.php");
        $i = 0;
        $commercant_array[] = array();
        $command = $cnx->query("SELECT * FROM Commercant");
        while ($ligne = $command->fetch(PDO::FETCH_OBJ)) {
            $nb_transac = CountTransac($ligne->SIREN, $date);
            $montant = CountMontant($nb_transac, $ligne->SIREN, $date);
            if ($montant >= 0) {
                echo '<p>SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : +' . $montant . 'Date : ' . $date . '</p><br>';
            }
            else {
                echo '<p>SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : <span style="color : red;">' . $montant . '</span> Date : ' . $date . '</p><br>';
            }
            $commercant_array[$i] =  new commercant($ligne->SIREN, $ligne->Raison_sociale, $nb_transac, $montant, null);;
            $i++;
        }
        return $commercant_array; // return an array of commercant
            
    }

    function show_treasury_client_date($SIREN, $date) { // Show the solde of a client has a date

        include("cnx.inc.php");

        $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
        $ligne = $command->fetch(PDO::FETCH_OBJ);

        if(empty($ligne)){
            echo "Le SIREN n'existe pas";
            return null;
        }
        $nb_transac = CountTransac($ligne->SIREN, $date);
        $montant = CountMontant($nb_transac, $ligne->SIREN, $date);
        if ($montant >= 0) {
            echo '<p>SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : +' . $montant . 'Date : ' . $date . '</p><br>';
        }
        else {
            echo '<p>SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : ' . $montant . 'Date : ' . $date . '</p><br>';
        }

        return new commercant($ligne->SIREN, $ligne->Raison_sociale, $nb_transac, $montant, null); // return a commercant object
    }


    function show_treasury_client($SIREN) { // Show the solde of a client
        include ("connexion.inc.php");

        $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
        $ligne = $command->fetch(PDO::FETCH_OBJ);
        if (empty($ligne)) {
            echo "Le SIREN n'existe pas";
            return null;
        }
        $nb_transac = CountAllTransac($ligne->SIREN);
        $montant = CountMontantOfAllTransac($nb_transac, $ligne->SIREN);
        if ($montant >= 0) {
            echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $nb_transac . ' Montant total : +' . $montant . '</p><br>';
        }
        else {
            echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $nb_transac . ' Montant total : ' . $montant . '</p><br>';
        }
        return new commercant($ligne->SIREN, $ligne->Raison_sociale, $nb_transac, $montant, null); // return a commercant object
    }


    function show_treasury_all_client($trie)
    { // Fonction qui affiche le solde des transactions totale de la trésorerie de tout client

        include("cnx.inc.php");

        $commercant_array = array();
        $command = $cnx->query("SELECT * FROM Commercant $trie"); // trie the result
        $i = 0;
        while ($ligne = $command->fetch(PDO::FETCH_OBJ)) {
            $nb_transac = CountAllTransac($ligne->SIREN);
            $montant = CountMontantOfAllTransac($nb_transac, $ligne->SIREN);
            $commercant_array[$i] = new commercant ($ligne->SIREN, $ligne->Raison_sociale, $nb_transac, $montant, null);
            if ($montant >= 0) {
                echo '<p>SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $nb_transac . ' Montant total : + ' . $montant. '</p>  <br>';
            }
            else {
                echo '<p>SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $nb_transac . ' Montant total : <span style="color : red;">'.$montant.'</span> </p> <br>';
            }
            $i++;
        }
        return $commercant_array; // Retourne un tableau d'objet commercant. Penser a afficher le solde en rouge si negatif
    }

    function show_treasury_all_client_order_solde() { // Show the solde of all client order by solde

        include("cnx.inc.php");

        $commercant_array = array();
        $montant_array = array();
        $command = $cnx->query("SELECT * FROM Commercant");
        $i = 0;
        while ($ligne = $command->fetch(PDO::FETCH_OBJ)) {
            $nb_transac = CountAllTransac($ligne->SIREN);
            $montant = CountMontantOfAllTransac($nb_transac, $ligne->SIREN);
            $montant_array[$i] = $montant;
            $commercant_array[$i] = new commercant($ligne->SIREN, $ligne->Raison_sociale, $nb_transac, $montant, null);
            $i++;
        }
        arsort($montant_array);
        $commercant_array_return = array();
        $i = 0;
        foreach ($montant_array as $value) {
            $i++;
            $index = 0;
            foreach($commercant_array as $commercant) {
                
                if ($commercant->getMontant() == $value) {
                    $commercant_array_return[$i] = $commercant;
                    if ($commercant->getMontant() >= 0) {
                        echo '<p>SIREN : ' .$commercant->getSIREN(). 'Raison sociale : '.$commercant->getRaison_social() . ' Nombre de transactions : ' . $commercant->getNb_transaction() . ' Montant total : + ' . $commercant->getMontant(). '</p>  <br>';
                    }
                    else {
                        echo '<p>SIREN : ' .$commercant->getSIREN(). 'Raison sociale : '.$commercant->getRaison_social() . ' Nombre de transactions : ' . $commercant->getNb_transaction() . ' Montant total : <span style="color : red;">'.$commercant->getMontant().'</span> </p> <br>';
                    }
                    unset($commercant_array[$index]); 
                }
                $index++;
            }
            
        }

        return $commercant_array_return; // return a array of commercant object
    }
    
    echo '
        <form action="" method="post">
            <p>Date : <input type="date" name="date" id="date"></p>
            <p>
            SIREN : <input type="text" name="SIREN" id="SIREN" maxlength="9">
            </p>
            <p>
            Afficher les soldes des clients :
            <select id="trie" name="trie">
                <option value="">Trie par :</option>
                <option value="Aucun">Aucun</option>
                <option value="SIREN">SIREN</option>
                <option value="Montant">Montant</option>
            </select>
            </p>
            <input type="submit" name="submit" value="Envoyer">
        </form>';


    if(isset($_POST['submit'])) {
        if (!empty($_POST['date'])) {

            if (!empty($_POST['date']) && !empty($_POST['SIREN'])) {
                echo '<h3>Solde d\'un client à une date donné</h3> <br>';
                $date = $_POST['date'];
                $SIREN = $_POST['SIREN'];
                show_treasury_client_date($SIREN, $date);
                echo '<br>';
            }
            else {
                $date = $_POST['date'];
                echo '<h3>Solde des clients à une date donné</h3> <br>';
                show_treasury_all_client_date($date);
                echo '<br>';
            }
        }
        if (!empty($_POST['SIREN'] && empty($_POST['date']))) {
            echo '<h3>Solde d\'un client</h3> <br>';
            $SIREN = $_POST['SIREN'];
            show_treasury_client($SIREN);
            echo '<br>';
        }
        if (!empty($_POST['trie']) != '') {
            if ($_POST['trie'] == 'SIREN') {
                echo '<h3>Trier par SIREN </h3><br>';
                show_treasury_all_client("ORDER BY SIREN");
                echo '<br>';
            }
            if ($_POST['trie'] == 'Montant') {
                echo '<h3>Trier par Montant </h3><br>';
                show_treasury_all_client_order_solde();
                echo '<br>';
            }
            elseif($_POST['trie'] == 'Aucun') {
                echo '<h3>Solde des clients</h3> <br>';
                show_treasury_all_client("");
                echo '<br>';
            }
            echo '<br>';
        }
        
    }
	
    320367139
    ?>
</body>

</html>