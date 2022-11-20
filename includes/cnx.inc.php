<?php

/*
 * création d'objet PDO de la connexion qui sera représenté par la variable $cnx
 */
$user =  "root";
$pass =  "";
try {
    //$cnx = new PDO("mysql:host=sqletud.u-pem.fr;dbname=anthonin.bleuse_db", $user, $pass);
    $cnx = new PDO("mysql:host=localhost;dbname=tran", $user, $pass);
}
catch (PDOException $e) {
    echo "ERREUR : La connexion a échouée";

 /* Utiliser l'instruction suivante pour afficher le détail de erreur sur la
 * page html. Attention c'est utile pour débugger mais cela affiche des
 * informations potentiellement confidentielles donc éviter de le faire pour un
 * site en production.*/
    //echo "Error: " . $e;

}

?>

