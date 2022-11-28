<?php

// include("/Laragon/www/Tran/includes/cnx.inc.php");
include(dirname(__FILE__, 2) . "/includes/cnx.inc.php");


if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = htmlspecialchars($_POST['login']);
    $password = htmlspecialchars($_POST['password']);
    $password = hash("sha256", $password);

    //make password uppercase
    $password = strtoupper($password);

    $req = $cnx->prepare("SELECT niveau FROM Compte WHERE id = :login AND mdp = :password");
    $req->bindParam(':login', $login);
    $req->bindParam(':password', $password);
    $req->execute();

    $result = $req->fetch();
    session_start();
    if (!isset($_SESSION['try'])) {
        $_SESSION['try'] = 1;
    }else{
        $_SESSION['try']++;
    }
    

    if ($result) {
        $_SESSION['niveau'] = $result[0];

        $req = $cnx->prepare("SELECT SIREN FROM Commercant NATURAL JOIN Compte WHERE Compte.id = :login AND Compte.mdp = :password");
        $req->bindParam(':login', $login);
        $req->bindParam(':password', $password);
        $req->execute();

        $result = $req->fetch();
        if (($result)) {
            $_SESSION['SIREN'] = $result[0];
        }

        header('Location: /home');    // si connecté, on redirige vers la page d'accueil
        exit();
    } else {


        if($_SESSION['try'] >= 3){
            setcookie('blocked', true, time() + 3600);
        }
        

        header('Location: /login?error=1');   // si erreur, on redirige vers la page de connexion
        exit();
    }
}
header('Location: /login'); // si pas de données envoyées, on redirige vers la page de connexion
