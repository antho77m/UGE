<?php

function add_account($name, $siren, $password, $id)
{

    //verifie que le siren est bien un nombre et qu'il fait 9 chiffres
    if (strlen($siren) != 9 || is_int($siren)) {
        echo "Le numéro de SIREN doit contenir 9 chiffres";
    } else {

        include ROOT . "/includes/cnx.inc.php";

        $req_co = $cnx->prepare("SELECT * FROM tran_commercant WHERE siren = :siren");
        $req_co->bindParam(":siren", $siren);
        $req_co->execute();

        $req_id = $cnx->prepare("SELECT * FROM tran_compte WHERE id = :id");
        $req_id->bindParam(":id", $id);
        $req_id->execute();


        if ($req_id->rowCount() != 0 || $req_co->rowCount() != 0) { //le siren ou l'id est déjà utilisé
            echo "Le numéro de SIREN ou l'identifiant est déjà utilisé";
        } else { //on peut ajouter le compte


            //hash du mot de passe avec sha256
            $password = hash("sha256", $password);
            $password = strtoupper($password);
            $cnx->exec("START TRANSACTION");


            //ajout du compte

            $req = $cnx->prepare("INSERT INTO tran_compte VALUES (:id, :password, 1)");
            $req->bindParam(":id", $id);
            $req->bindParam(":password", $password);

            if (!$req->execute()) {   //une erreur est survenue
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de l'ajout du compte";
            }

            //ajout du commercant

            $req = $cnx->prepare("INSERT INTO tran_commercant VALUES (:siren,:name,:id)");
            $req->bindParam(":siren", $siren);
            $req->bindParam(":name", $name);
            $req->bindParam(":id", $id);
            if (!$req->execute()) {   //une erreur est survenue
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de l'ajout du commercant";
            }
            echo "Compte ajouté avec succès";
            header("Refresh: 2; url=/pages/home.php");
            $cnx->exec("COMMIT");
        }
    }
}

function delete_account($name, $siren, $id)
{
    if (strlen($siren) != 9 || is_int($siren)) {
        echo "Le numéro de SIREN doit contenir 9 chiffres";
    } else {
        include ROOT . "/includes/cnx.inc.php";
        $req = $cnx->prepare("SELECT * FROM tran_commercant WHERE id = :id AND siren = :siren AND raison_sociale = :name");
        $req->bindParam(":id", $id);
        $req->bindParam(":siren", $siren);
        $req->bindParam(":name", $name);
        $req->execute();

        if ($req->rowCount() == 0) {
            echo "Le compte n'existe pas";
        } else {
            $cnx->exec("START TRANSACTION");

            //suppression impayés
            $req = $cnx->prepare("DELETE impaye FROM tran_commercant             
            INNER JOIN tran_transaction ON tran_transaction.SIREN=tran_commercant.SIREN 
            INNER JOIN tran_impaye ON tran_impaye.num_autorisation=tran_transaction.num_autorisation 
            WHERE tran_commercant.id = :id 
            AND tran_commercant.siren = :siren 
            AND tran_commercant.raison_sociale = :name;");
            $req->bindParam(":id", $id);
            $req->bindParam(":siren", $siren);
            $req->bindParam(":name", $name);
            if (!$req->execute()) {
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de la suppression des impayés";
            }

            //suppression des transactions
            $req = $cnx->prepare("DELETE FROM tran_transaction WHERE SIREN = :SIREN");
            $req->bindParam(":SIREN", $siren);
            if (!$req->execute()) {
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de la suppression des transactions";
            }


            //suppression du commerçant
            $req = $cnx->prepare("DELETE FROM tran_commercant WHERE id = :id AND siren = :siren AND raison_sociale = :name");
            $req->bindParam(":id", $id);
            $req->bindParam(":siren", $siren);
            $req->bindParam(":name", $name);
            if (!$req->execute()) {
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de la suppression du commerçant";
            }

            //suppression du compte
            $req = $cnx->prepare("DELETE FROM tran_compte WHERE id = :id");
            $req->bindParam(":id", $id);
            if (!$req->execute()) {
                $cnx->exec("ROLLBACK");
                echo "Erreur lors de la suppression du compte";
            }
            $cnx->exec("COMMIT");
            echo "Compte supprimé avec succès";
            header("Refresh: 2; url=/pages/home.php");
        }
    }
}

function count_clients()
{
    require(dirname(__FILE__, 2) . '/cnx.inc.php');
    $sql = "SELECT COUNT(id) FROM tran_commercant";

    $req = $cnx->prepare($sql);
    $req->execute();
    $result = $req->fetch();
    return $result[0];
}

function load_clients()
{
    require(dirname(__FILE__, 2) . '/cnx.inc.php');
    $sql = "SELECT SIREN, Raison_sociale, count(num_autorisation) AS nbTransactions, SUM(montant) AS montant_total, (SELECT SUM(montant)*2 FROM tran_Commercant NATURAL JOIN tran_percevoir NATURAL JOIN tran_Transaction WHERE SIREN = T.SIREN AND sens = '-') AS montant_impayes
    FROM tran_Commercant
    NATURAL JOIN tran_percevoir AS T
    NATURAL JOIN tran_Transaction
    GROUP BY SIREN";

    $req = $cnx->prepare($sql);
    $req->execute();
    $result = $req->fetchAll();
    return $result;
}

function display_clients()
{

    require(dirname(__FILE__, 2) . '/cnx.inc.php');

    $req = $cnx->prepare("SELECT SIREN, Raison_sociale, id FROM tran_commercant");
    $req->execute();
    while ($result = $req->fetch()) {
        echo '
        <div class="remittance_results__details">
            <div class="remittance_result">
                <p style="font-size: 14px; color: var(--blue75);">SIREN</p>
                <p style="font-size: 16px;">' . $result['SIREN'] . '</p>
            </div>

            <div class="remittance_result">
                <p style="font-size: 14px; color: var(--blue75);">Raison sociale</p>
                <p style="font-size: 16px;">' . $result['Raison_sociale'] . '</p>
            </div>

            <div class="remittance_result">
                <p style="font-size: 14px; color: var(--blue75);">Identifiant</p>
                <p style="font-size: 16px;">' . $result['id'] . '</p>
            </div>
        </div>';
    }
}
