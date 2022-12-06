
<?php require_once("/includes/class/commercant.php"); 

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

        include("cnx.inc.php");

            $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
            $ligne = $command->fetch(PDO::FETCH_OBJ);
            $nb_transac = CountTransac($ligne->SIREN, $date);
            if($nb_transac == 0){
                echo "Vous n'avez pas de transaction à cette date";
                return null;
            }
            $montant = CountMontant($nb_transac, $ligne->SIREN, $date);
            if ($montant > 0) {
                echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : + ' . $montant . 'Date : ' . $date;
            }
            else {
                echo 'SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $nb_transac . 'Montant total : ' . $montant . 'Date : ' . $date;
            }
            return new commercant($SIREN, $ligne->Raison_sociale, $nb_transac, $montant, $date);
        }

    function showTreasury($SIREN) // Affiche la trésorerie du client au jour même
    {
        include("cnx.inc.php");

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

        return new commercant($SIREN, $ligne->Raison_sociale, $nb_transac, $montant, null);
    }

    function show_treasury_client_datemax($SIREN, $date){

        include("cnx.inc.php");
        
        $command = $cnx->query("SELECT * FROM Commercant WHERE SIREN = '$SIREN'");
        $ligne = $command->fetch(PDO::FETCH_OBJ);

        $nb_transac = CountAllTransac($SIREN);
        if($nb_transac > 0){
            $montant = 0;
            $command2 = $cnx->prepare("SELECT montant, sens, date_vente FROM Transaction, percevoir WHERE percevoir.SIREN = '$SIREN' AND percevoir.num_autorisation = Transaction.num_autorisation AND Transaction.date_vente <= '$date'");
            $command2->execute();
            $result = $command2->fetchAll();
            if(!empty($result)){
                foreach($result as $res){
                    if ($res[1] == "-") {

                        $montant = $montant - $res[0];
                    } else {
        
                        $montant = $montant + $res[0];
                    }
                }
            }
                    echo 'Solde : '.$montant;
                    $commercant = new commercant($SIREN, $ligne->Raison_sociale, $nb_transac, $montant,null);
                    
                    return $commercant;
        }
    }

    include("/pages/user_treasury.php");
    echo '
        <form action="" method="post">
            <div>
                <div>
                    Solde du début à une date précise <input type="date" id="date" name="date">                          
                    Solde des transaction 
                    <select name="type" id="type">
                        <option value="1">Choix</option>
                        <option value="2">à cette date</option>
                        <option value="3">jusqu\'à cette date</option>
                    </select>
                </div>
            </div>
            <input type="submit" name="submit" value="Confirmer" />
            </form>';
    
    if(isset($_POST['submit'])){

        if ($_POST['type'] != 1){
            if ($_POST['type'] == 2){
                echo '<h1>A cette date</h1>';
                show_treasury_client_date($SIREN, $_POST['date']);

            }
            else {
                echo '<h1>Jusqu\'à cette date</h1>';
                show_treasury_client_datemax($SIREN, $_POST['date']);
            }
        }
    }

    ?>
    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>