<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trésorerie</title>
    <meta property="og:description" content="Remises">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
</head>

<body>


    <?php

    function show_treasury_all_client_date($date) { // Show the solde of all client has a date
        global $cnx;

        //require_once("commercant.php");
        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, count(num_autorisation) AS nbT, 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+' AND date_traitement <= '$date'), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-' AND date_traitement <= '$date'), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE date_traitement <= '$date' AND SIREN LIKE '%'
        GROUP BY SIREN ORDER BY ''");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_OBJ);
        foreach ($result as $ligne) {
            if($ligne->montant_total >= 0) {
                echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : green;">' . $ligne->montant_total . '</span> Date : ' . $date . '</p><br>';
            }
            else {
                echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : red;">' . $ligne->montant_total . '</span> Date : ' . $date . '</p><br>';
            }
        }
        return $result;
    }

    function show_treasury_client_date($SIREN, $date) { // Show the solde of a client has a date
        global $cnx;

        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, count(num_autorisation) AS nbT, 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+' AND date_traitement <= '$date'), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-' AND date_traitement <= '$date'), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE date_traitement <= '$date' AND SIREN LIKE '%$SIREN'
        GROUP BY SIREN");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_OBJ);
        foreach ($result as $ligne) {
            if($ligne->montant_total >= 0) {
                echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : green;">' . $ligne->montant_total . '</span> Date : ' . $date . '</p><br>';
            }
            else {
                echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : red;">' . $ligne->montant_total . '</span> Date : ' . $date . '</p><br>';
            }
        }
        return $result;
    }


    function show_treasury_client($SIREN) { // Show the solde of a client
        global $cnx;
        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, count(num_autorisation) AS nbT, 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+'), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-'), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE SIREN LIKE '%$SIREN'
        GROUP BY SIREN ORDER BY ''");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_OBJ);
        foreach ($result as $ligne) {
            if($ligne->montant_total >= 0) {
                echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : green;">' . $ligne->montant_total . '</span> Date : '.date('m-d-Y', time()).'</p><br>';
            }
            else {
                echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : red;">' . $ligne->montant_total . '</span> Date : ' . date('m-d-Y', time()) . '</p><br>';
            }
        }
        return $result;
    }


    function show_treasury_all_client($trie)
    { // Fonction qui affiche le solde des transactions totale de la trésorerie de tout client
        global $cnx;

        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, count(num_autorisation) AS nbT, 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+'), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-'), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE SIREN LIKE '%'
        GROUP BY SIREN $trie");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_OBJ);
        foreach ($result as $ligne) {
            if($ligne->montant_total >= 0) {
                echo '<p>SIREN : ' .$ligne->SIREN. ' Raison sociale : '.$ligne->Raison_sociale . ' Nombre de transactions : ' . $ligne->nbT . ' Montant total : <span style="color : green;">' . $ligne->montant_total . '</span> Date : '.date('m-d-Y', time()).'</p><br>';
            }
            else {
                echo '<p>SIREN : ' .$ligne->SIREN. 'Raison sociale : '.$ligne->Raison_sociale . 'Nombre de transactions : ' . $ligne->nbT . 'Montant total : <span style="color : red;">' . $ligne->montant_total . '</span> Date : ' . date('m-d-Y', time()) . '</p><br>';
            }
        }
        return $result;

    }
?>
    
        <form action="" method="post" class="client_form">
            <div class="form_group">
                <label for="">Date : </label>
                <input type="date" name="date" id="date">
            </div>

            <div class="form_group">
                <label for="">SIREN :</label>
                <input type="text" name="SIREN" id="SIREN" maxlength="9">
            </div>

            <div class="form_group">
                <label for="">Afficher les soldes des clients :</label>
                <select id="trie_type" name="trie_type">
                    <option value="Aucun">Aucun</option>
                    <option value="SIREN">SIREN</option>
                    <option value="Montant">Montant</option>
                </select>
            </div>

            <div class="form_radio">
                <div class="form_select">
                    <input type="radio" id="croissant" name="sens" value="croissant">
                    <label for="">Croissant</label>
                </div>

                <div class="form_select">
                    <input type="radio" id="decroissant" name="sens" value="decroissant">
                    <label for="">Décroissant</label>
                </div>

            </div>
            <input type="submit" name="submit" value="Envoyer">
        </form>
<?php
    if(isset($_POST['submit'])) {
        $date = date('Y-m-d');
        if (!empty($_POST['date'])) {
            $date = $_POST['date'];
            if (!empty($_POST['SIREN'])) {
                echo '<h3>Solde d\'un client à une date donné</h3> <br>';
                $date = $_POST['date'];
                $SIREN = $_POST['SIREN'];
                array_push($array_export,show_treasury_client_date($SIREN, $date));
                echo '<br>';
            }
            else {
                $date = $_POST['date'];
                echo '<h3>Solde des clients à une date donné</h3> <br>';
                array_push($array_export,show_treasury_all_client_date($date));
                echo '<br>';
            }
        } else if (!empty($_POST['SIREN'])) {
            echo '<h3>Solde d\'un client</h3> <br>';
            $SIREN = $_POST['SIREN'];
            array_push($array_export,show_treasury_client($SIREN));
            echo '<br>';
        } else if (!empty($_POST['trie_type']) != '') {
            if ($_POST['trie_type'] == 'SIREN') {
                if (isset($_POST['sens'])) {
                    if ($_POST['sens'] == 'croissant') {
                        echo '<h3>Trier par SIREN croissant</h3><br>';
                        array_push($array_export, show_treasury_all_client("ORDER BY SIREN ASC"));
                        echo '<br>';
                    } elseif ($_POST['sens'] == 'decroissant') {
                        echo '<h3>Trier par SIREN décroissant</h3><br>';
                        array_push($array_export, show_treasury_all_client("ORDER BY SIREN DESC"));
                        echo '<br>';
                    }    
                } else {
                    echo '<h3>Solde clients </h3><br>';
                    array_push($array_export, show_treasury_all_client(''));
                    echo '<br>';
                }
            } else if ($_POST['trie_type'] == 'Montant') {
                if (isset($_POST['sens'])) {
                    if ($_POST['sens'] == 'croissant') {
                        echo '<h3>Trier par Montant croissant</h3><br>';
                        array_push($array_export, show_treasury_all_client("ORDER BY montant_total ASC"));
                        echo '<br>';
                    } elseif ($_POST['sens'] == 'decroissant') {
                        echo '<h3>Trier par Montant décroissant</h3><br>';
                        array_push($array_export, show_treasury_all_client("ORDER BY montant_total DESC"));
                        echo '<br>';
                    }
                } else {
                    echo '<h3>Trier par Montant </h3><br>';
                    array_push($array_export, show_treasury_all_client(''));
                    echo '<br>';
                }
            }
            elseif($_POST['trie_type'] == 'Aucun') {
                echo '<h3>Solde des clients</h3> <br>';
                array_push($array_export,show_treasury_all_client(''));
                echo '<br>';
            }
            echo '<br>';
        }
        echo '
        <p style="margin-left: 18px;">Exporter les résultats en :</p>
        <div class="export_wrap">
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=CSV&date='.$date.'\', \'_blank\');">CSV</button>
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=XLS&date='.$date.'\', \'_blank\');">XLS</button>
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=PDF&date='.$date.'\', \'_blank\');">PDF</button>
        </div>
        ';
    }
?>

<script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>