<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="">
        <title>Tresorerie</title>
    </head>
    <body>
        <?php 

            class commercant { // Création de la classe commercant
                public $SIREN;
                public $raison_social;
                public $nb_transaction;
                public $montant;
                public $date;

                function __construct($SIREN, $raison_social, $nb_transaction, $montant, $date){ // Constructeur de la classe commercant
                    $this->SIREN = $SIREN;
                    $this->raison_social = $raison_social;
                    $this->nb_transaction = $nb_transaction;
                    $this->montant = $montant;
                    $this->date = $date;
                }
            }

            function CountTransac($SIREN, $date){ // Fonction qui compte le nombre de transaction d'un commercant à une date donnée

                include("cnx.inc.php");
                $command2=$cnx -> query("SELECT COUNT(percevoir.num_autorisation) AS nb_transaction FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND Transaction.num_autorisation = percevoir.num_autorisation AND Transaction.date_vente = '$date'");
                while( $ligne2 = $command2->fetch(PDO::FETCH_OBJ) ) // un par un
                {
                    $nb_transac = $ligne2->nb_transaction;
                }
                
                return $nb_transac;
            }

            function CountMontant($nb_transac, $SIREN, $date){ // Fonction qui compte le montant total des transactions d'un commercant à une date donnée
                include("cnx.inc.php");
                $montant = 0;
                if($nb_transac != 0){

                    $command3=$cnx -> query("SELECT sens, montant FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND percevoir.num_autorisation = Transaction.num_autorisation AND date_vente = '$date'");
                    while( $ligne3 = $command3->fetch(PDO::FETCH_OBJ) ) // un par un
                    {
                        if($ligne3->sens == "-"){

                            $montant = $montant - $ligne3->montant;
                        }
                        else{

                            $montant = $montant + $ligne3->montant;
                        }
                    }
                    return $montant;
                }
                return $montant;
            }



            function AfficheTresorerie_Client_Date($SIREN){ // Fonction qui affiche le solde des transactions du jour de la trésorerie du client à une date donnée
                include("cnx.inc.php");
                echo "<h1>Annonces trésorerie du client Profil Client</h1><br>";
                echo "<form action='' method='post'>";
                echo "<input type='date' name='date3' id='date3'>";
                echo "<input type='submit' value='Valider'>";
                echo "</form>";
                echo "<br>";
    
                if(isset($_POST['date3']) && isset($SIREN)){
                    $date3 = $_POST['date3'];
    
                    $command=$cnx -> query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
                    $ligne = $command->fetch(PDO::FETCH_OBJ);
                    $nb_transac = CountTransac($ligne->SIREN, $date3);
                    $montant = CountMontant($nb_transac, $ligne->SIREN, $date3);
                    $commercant = new commercant($ligne->SIREN, $ligne->raison_social, $nb_transac, $montant, null);
                    return $commercant; // Retourne un objet commercant
                }
            }
        


        function AfficheTresorerie_Client_Date_PO(){ // Fonction qui affiche le solde des transactions du jour de la trésorerie des client à une date donnée
            include("cnx.inc.php");
            echo "<h1>Annonce trésorerie des comptes clients PO</h1>";
            echo "<form action='' method='post'>";
            echo "<input type='date' name='date' id='date'>";
            echo "<input type='submit' value='Valider'>";
            echo "</form>";
            echo "<br>";
            

            if(isset($_POST['date'])){
                $date = $_POST['date'];
            }
            else {
                $date = "";
            }
            if (isset($date)) {
                if($date != ""){
                    $commercant_array = array();
                    $i = 0;
                    $command=$cnx -> query("SELECT * FROM Commercant");
                    while( $ligne = $command->fetch(PDO::FETCH_OBJ) )
                    {
                        $nb_transac = CountTransac($ligne->SIREN, $date);
                        $montant = CountMontant($nb_transac, $ligne->SIREN, $date);
                        $commercant_array[$i] = new commercant($ligne->SIREN, $ligne->raison_social, $nb_transac, $montant, null);
                        $i++;
                    }
                    return $commercant_array; // Retourne un tableau d'objet commercant
                }
            }   
        }

        function AfficheTresorerie_Par_Client_Date_PO(){ // Fonction qui affiche le solde des transactions du jour de la trésorerie d'un client à une date donnée
            include("cnx.inc.php");
            echo "<h1>Annonces trésorerie par compte client PO</h1><br>";
            echo "<form action='' method='post'>";
                
                echo "<select name='SIREN1' id='SIREN1'>";
                    echo "<option value=''>SIREN</option>";
                    $command=$cnx -> query("SELECT SIREN FROM Commercant");
                    while( $ligne = $command->fetch(PDO::FETCH_OBJ) )
                    {
                        echo "<option value='$ligne->SIREN'>$ligne->SIREN</option>";
                    }
                echo "</select>";
                echo "<input type='date' name='date1' id='date1'>";
                echo "<input type='submit' value='Valider'>";
                echo "<br>";
            echo "<form>";
            
            
                if(isset($_POST['date1']) && $_POST['SIREN1']){
                    $date1 = $_POST['date1'];
                    $SIREN1 = $_POST['SIREN1'];
                }
                else {
                    $date1 = "";
                    $SIREN1 = "";
                }
                if (isset($date1) && isset($SIREN1)) {
                    if($date1 != "" && $SIREN1 != ""){

                        $command=$cnx -> query("SELECT * FROM Commercant WHERE SIREN = '$SIREN1'");
                        $ligne = $command->fetch(PDO::FETCH_OBJ);
                        $nb_transac = CountTransac($ligne->SIREN, $date1);
                        $montant = CountMontant($nb_transac, $ligne->SIREN, $date1);
                        $commercant = new commercant($ligne->SIREN, $ligne->raison_social, $nb_transac, $montant, null);
                        return $commercant; // Retourne un objet commercant
                    }
                }
        }

        function CountAllTransac($SIREN){ // Fonction qui compte le nombre de transaction d'un client
            include("cnx.inc.php");
            $command=$cnx -> query("SELECT COUNT(percevoir.num_autorisation) AS nb_transaction FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND Transaction.num_autorisation = percevoir.num_autorisation");
            $ligne = $command->fetch(PDO::FETCH_OBJ);
            return $ligne->nb_transaction;
        }

        function CountMontantOfAllTransac($nb_transac, $SIREN){ // Fonction qui compte le montant de toutes les transactions d'un client
            include("cnx.inc.php");
            if ($nb_transac == 0){
                return 0;
            }
            $montant = 0;
            $command=$cnx -> query("SELECT sens, montant FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND percevoir.num_autorisation = Transaction.num_autorisation");
            while( $ligne = $command->fetch(PDO::FETCH_OBJ) ) // un par un
                {

                    if($ligne->sens == "-"){

                        $montant = $montant - $ligne->montant;
                    }
                    else{

                        $montant = $montant + $ligne->montant;
                    }
                }
            return $montant;
        }

        function AfficheTresorerie_Client_Solde_PO(){ // Fonction qui affiche le solde des transactions totale de la trésorerie de tout client
            include("cnx.inc.php");
            echo "<h1>Annonces trésorerie des comptes clients PO solde négatif</h1><br>";
            $commercant_array = array();
            $i = 0;
            $command=$cnx -> query("SELECT * FROM Commercant");
            while( $ligne = $command->fetch(PDO::FETCH_OBJ) )
            {
                $nb_transac = CountAllTransac($ligne->SIREN);
                $montant = CountMontantOfAllTransac($nb_transac, $ligne->SIREN);
                $commercant_array[$i] = new commercant($ligne->SIREN, $ligne->raison_social, $nb_transac, $montant, null);
                $i++;
            } 
            return $commercant_array; // Retourne un tableau d'objet commercantpenser a afficher le solde en rouge si negatif
        }

        function AfficheTresorerie_Client_Solde_And_SIREN_PO(){ // Fonction qui affiche le solde des transactions totale de la trésorerie d'un client
            include("cnx.inc.php");
            echo "<h1>Annonces trésorerie des comptes clients PO solde ou SIREN</h1><br>";
            echo "<form action='' method='post'>";
            echo "<select type='text' name='SIREN' id='SIREN'>";

                echo "<option value=''>SIREN</option>";
                $command=$cnx -> query("SELECT SIREN FROM Commercant");
                while( $ligne = $command->fetch(PDO::FETCH_OBJ) )
                {
                    echo "<option value='$ligne->SIREN'>$ligne->SIREN</option>";
                }
            echo "</select>";
            echo "<input type='number' name='min_solde' id='min_solde'>";
            echo "<input type='number' name='max_solde' id='max_solde'>";
            echo "<input type='submit' value='Valider'>";
            echo "</form>";
            echo "<br>";
            
            if(isset($_POST['SIREN']) && isset($_POST['min_solde']) && isset($_POST['max_solde'])){
                $SIREN = $_POST['SIREN'];
                $min_solde = $_POST['min_solde'];
                $max_solde = $_POST['max_solde'];
                $nb_transac = CountAllTransac($SIREN);
                $montant = CountMontantOfAllTransac($nb_transac, $SIREN);
                if ($montant >= $min_solde && $montant <= $max_solde){
                    $command = $cnx -> query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
                    $ligne = $command->fetch(PDO::FETCH_OBJ);
                    $commercant = new commercant($ligne->SIREN, $ligne->raison_social, $nb_transac, $montant, null);
                    return $commercant; // Retourne un objet commercant
                }
                else{
                    return null;
                }
            }
            else{
                echo "Veuillez remplir les champs s'il vous plait";
            }
        }

        AfficheTresorerie_Client_Date_PO();
        AfficheTresorerie_Par_Client_Date_PO();
        AfficheTresorerie_Client_Date("320367139"); 
        AfficheTresorerie_Client_Solde_PO();
        AfficheTresorerie_Client_Solde_And_SIREN_PO();

        ?>
    </body>
</html>