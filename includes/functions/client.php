<?php

function add_account($name, $siren, $password, $id)
{

    //verify if the $siren have 9 digits
    if (strlen($siren) != 9 || is_int($siren)) {
        echo "Le numéro de SIREN doit contenir 9 chiffres";
    } else {
        require_once(dirname(__FILE__, 2) . '/cnx.inc.php');

        $req = $cnx->prepare("SELECT * FROM commercant WHERE siren = :siren");
        $req->bindParam(":siren", $siren);
        $resultSIREN = $req->execute();

        $req = $cnx->prepare("SELECT * FROM compte WHERE id = :id");
        $req->bindParam(":id", $siren);
        $resultID = $req->execute();


        if (!$resultSIREN || !$resultID) {
            echo "Le numéro de SIREN ou l'identifiant est déjà utilisé";
        } else { //on peut ajouter le compte

            //insertion dans la base de données du compte
            //hash password with sha256
            $password = hash("sha256", $password);
            $password = strtoupper($password);
            $cnx->exec("START TRANSACTION");


            //ajout du compte

            $req = $cnx->prepare("INSERT INTO compte VALUES (:id, :password, 1)");
            $req->bindParam(":id", $id);
            $req->bindParam(":password", $password);

            if (!$req->execute()) {   //une erreur est survenue
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de l'ajout du compte";
            }

            //ajout du commercant

            $req = $cnx->prepare("INSERT INTO commercant VALUES (:siren,:name,:id)");
            $req->bindParam(":siren", $siren);
            $req->bindParam(":name", $name);
            $req->bindParam(":id", $id);
            if (!$req->execute()) {   //une erreur est survenue
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de l'ajout du commercant";
            }
            echo "Compte ajouté avec succès";
            $cnx->exec("COMMIT");
        }
    }
}

function delete_account($name, $siren, $id)
{
    if (strlen($siren) != 9 || is_int($siren)) {
        echo "Le numéro de SIREN doit contenir 9 chiffres";
    } else {

        require_once(dirname(__FILE__, 2) . '/cnx.inc.php');
        $req = $cnx->prepare("SELECT * FROM commercant WHERE id = :id AND siren = :siren AND raison_sociale = :name");
        $req->bindParam(":id", $id);
        $req->bindParam(":siren", $siren);
        $req->bindParam(":name", $name);
        $result = $req->execute();

        if (!$result) {
            echo "Le compte n'existe pas";
        } else {
            $cnx->exec("START TRANSACTION");

            //recuperation des numéros d'autorisation (avant la suppression du lien)
            $req_autorisation = $cnx->prepare("SELECT num_autorisation FROM percevoir NATURAL JOIN commercant WHERE id = :id AND commercant.siren = :siren AND raison_sociale = :name");
            $req_autorisation->bindParam(":id", $id);
            $req_autorisation->bindParam(":siren", $siren);
            $req_autorisation->bindParam(":name", $name);
            $req_autorisation->execute();





            //suppression impayés
            $req = $cnx->prepare("DELETE impaye FROM commercant 
            INNER JOIN percevoir ON percevoir.SIREN=commercant.SIREN 
            INNER JOIN impaye ON impaye.num_autorisation=percevoir.num_autorisation
            INNER JOIN transaction ON transaction.num_autorisation=percevoir.num_autorisation 
            WHERE commercant.id = :id 
            AND commercant.siren = :siren 
            AND commercant.raison_sociale = :name;");
            $req->bindParam(":id", $id);
            $req->bindParam(":siren", $siren);
            $req->bindParam(":name", $name);
            if (!$req->execute()) {
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de la suppression des impayés";
            }



            //suppression des percevoir
            $req = $cnx->prepare("DELETE percevoir FROM commercant 
            INNER JOIN percevoir ON percevoir.SIREN=commercant.SIREN 
            INNER JOIN transaction ON transaction.num_autorisation=percevoir.num_autorisation 
            WHERE commercant.id = :id 
            AND commercant.siren = :siren 
            AND commercant.raison_sociale = :name;");
            $req->bindParam(":id", $id);
            $req->bindParam(":siren", $siren);
            $req->bindParam(":name", $name);
            if (!$req->execute()) {
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de la suppression des perçus";
            }

            //suppression des transactions
            while ($autorisation = $req_autorisation->fetch()) {
                $req = $cnx->prepare("DELETE FROM transaction WHERE num_autorisation = :num_autorisation");
                $req->bindParam(":num_autorisation", $autorisation[0]);
                if (!$req->execute()) {
                    $cnx->exec("ROLLBACK");
                    echo "Erreur lors de la suppression des transactions";
                }
            }

            //suppression du commerçant
            $req = $cnx->prepare("DELETE FROM commercant WHERE id = :id AND siren = :siren AND raison_sociale = :name");
            $req->bindParam(":id", $id);
            $req->bindParam(":siren", $siren);
            $req->bindParam(":name", $name);
            if (!$req->execute()) {
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de la suppression du commerçant";
            }

            //suppression du compte
            $req = $cnx->prepare("DELETE FROM compte WHERE id = :id");
            $req->bindParam(":id", $id);
            if (!$req->execute()) {
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de la suppression du compte";
            }
            $cnx->exec("COMMIT");
            echo "Compte supprimé avec succès";
        }
    }
}

function count_clients()
{
    require_once(dirname(__FILE__, 2) . '/cnx.inc.php');
    $sql = "SELECT COUNT(*) FROM commercant";
    
    $req = $cnx->prepare($sql);
    $req->execute();
    $result = $req->fetch();
    return $result[0];
}

