<?php
    include("../../includes/cnx.inc.php");
    if(isset($_POST['login']) && isset($_POST['password'])){
        $login = $_POST['login'];
        $password = hash("sha256",$_POST['password']);

        //make password uppercase
        $password = strtoupper($password);

        $req = $cnx->prepare("SELECT niveau FROM Compte WHERE id = :login AND mdp = :password");
        $req->bindParam(':login', $login);
        $req->bindParam(':password', $password);
        $req->execute();

        $result = $req->fetch();
        
        if(!empty($result)){
            session_start();
            $_SESSION['niveau'] = $result[0];
            
            $req = $cnx->prepare("SELECT SIREN FROM Commercant NATURAL JOIN Compte WHERE Compte.id = :login AND Compte.mdp = :password");
            $req->bindParam(':login', $login);
            $req->bindParam(':password', $password);
            $req->execute();

            $result = $req->fetch();
            if(!empty($result)){
                $_SESSION['SIREN'] = $result[0];
            }

            header('Location: ../home.php');    // si connecté, on redirige vers la page d'accueil
            exit();
        }
        else{
            header('Location: ../login.php?error=1');   // si erreur, on redirige vers la page de connexion
            exit();
        }
    }
    header('Location: ../login.php'); // si pas de données envoyées, on redirige vers la page de connexion
    
    ?>