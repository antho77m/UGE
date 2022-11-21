<?php

    function add_account($name,$siren,$password,$id){

        //verify if the $siren have 9 digits
        if(strlen($siren)!=9 || is_int($siren)){
            echo "Le numéro de SIREN doit contenir 9 chiffres";
        }
        else{
            require_once(dirname(__FILE__,2).'/cnx.inc.php');
            
            $req = $cnx->prepare("SELECT * FROM commercant WHERE siren = :siren");
            $req->bindParam(":siren", $siren);
            $resultSIREN = $req->execute();
            
            $req = $cnx->prepare("SELECT * FROM compte WHERE id = :id");
            $req->bindParam(":id", $siren);
            $resultID = $req->execute();


            if(!$resultSIREN || !$resultID){
                echo "Le numéro de SIREN ou l'identifiant est déjà utilisé";
            }else{ //on peut ajouter le compte

                //insertion dans la base de données du compte
                //hash password with sha256
                $password = hash("sha256", $password);
                $password = strtoupper($password);
                $cnx->exec("START TRANSACTION");
                
                
                //ajout du compte

                $req = $cnx -> prepare("INSERT INTO compte VALUES (:id, :password, 1)");   
                $req->bindParam(":id", $id);
                $req->bindParam(":password", $password);
                
                if(!$req->execute()){   //une erreur est survenue
                    $cnx->exec("ROLLBACK");
                    echo "Erreur lors de l'ajout du compte";
                }

                //ajout du commercant
                
                $req = $cnx->prepare("INSERT INTO commercant VALUES (:siren,:name,:id)");
                $req->bindParam(":siren", $siren);
                $req->bindParam(":name", $name);
                $req->bindParam(":id", $id);
                if(!$req->execute()){   //une erreur est survenue
                    $cnx->exec("ROLLBACK");
                    echo "Erreur lors de l'ajout du commercant";
                }
                echo "Compte ajouté avec succès";
                $cnx->exec("COMMIT");
            }
        }
    
    }

    function delete_account($name,$siren,$id){
        require_once(dirname(__FILE__,2).'/cnx.inc.php');
        $req = $cnx->prepare("SELECT * FROM commercant WHERE id = :id AND siren = :siren AND name = :name");
        $req->bindParam(":id", $id);
        $req->bindParam(":siren", $siren);
        $req->bindParam(":name", $name);
        $result = $req->execute();
        if(!$result){
            echo "Le compte n'existe pas";
        }else{
            $cnx->exec("START TRANSACTION");
            $req = $cnx->prepare("DELETE FROM impaye,percevoir,commercant 
            WHERE impaye.num_autorisation=percevoir.num_autorisation 
              AND percevoir.siren=commercant.siren
              AND transaction.num_autorisation=impaye.num_autorisation
              AND id = :id 
              AND siren = :siren 
              AND name = :name");
        }






    }

?>