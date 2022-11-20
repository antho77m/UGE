<?php
if (isset($_POST["name"])&&isset($_POST["siren"])&&isset($_POST["password"])){
    $name = $_POST["name"];
    $siren = $_POST["siren"];
    $password = $_POST["password"];
    //verify if the $siren have 9 digits
    if(strlen($siren)!=9 || is_int($siren)){
        echo "Le numéro de SIREN doit contenir 9 chiffres";
    }
    else{
        require_once("../includes/cnx.inc.php");
        
        //ajout du compte
        $req = $cnx->prepare("SELECT * FROM commercant WHERE siren = :siren");
        $req->bindParam(":siren", $siren);
        $result = $req->execute();
        
        if(!$result){
            echo "Le numéro de SIREN est déjà utilisé";
        }else{ //on peut ajouter le compte

            //récupération du prochain id
            
            $req = $cnx->query("SELECT MAX(id) FROM compte");
            $id = $req->fetch();
            $newID = $id[0]+1;
            //insertion dans la base de données du compte
            //hash password with sha256
            $password = hash("sha256", $password);
            $password = strtoupper($password);
            $cnx->exec("START TRANSACTION");
            
            
            //ajout du compte

            $req = $cnx -> prepare("INSERT INTO compte VALUES (:id, :password, 1)");   
            $req->bindParam(":id", $newID);
            $req->bindParam(":password", $password);
            
            if(!$req->execute()){   //une erreur est survenue
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de l'ajout du compte";
            }

            //ajout du commercant
            
            $req = $cnx->prepare("INSERT INTO commercant VALUES (:siren,:name,:id)");
            $req->bindParam(":siren", $siren);
            $req->bindParam(":name", $name);
            $req->bindParam(":id", $newID);
            if(!$req->execute()){   //une erreur est survenue
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de l'ajout du commercant";
            }
            echo "Compte ajouté avec succès";
            $cnx->exec("COMMIT");
        }
    }
}
?>

<form action="" method="post">
    
    Raison sociale :
    <input type="text" name="name" id="name" placeholder="Raison sociale" autocomplete="off"> <br>
    
    SIREN :
    <input type="number" name="siren" id="siren" placeholder="SIREN" autocomplete="off"> <br>
    
    Mot de passe :
    <input type="password" name="password" id="password" placeholder="Mot de passe" autocomplete="off"> <br>

    <input type="submit" value="Ajouter">
</form>

