<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trésorerie</title>
    <meta property="og:description" content="Trésorerie">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <style type="text/css">
        .highcharts-figure,
        .highcharts-data-table table {
            width: 100%;
            margin: 1em auto;
            z-index: 1;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>
</head>

<body>

    <?php
    function showTreasury($SIREN, $date) // fonction profil client
    { // Fonction qui affiche le solde des transactions du jour de la trésorerie du client à une date donnée
        global $cnx;

        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, 
            COALESCE((SELECT count(num_autorisation) FROM Transaction WHERE SIREN = R.SIREN), 0) AS nbT, 
            COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+' AND date_traitement <= :date), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-' AND date_traitement <= :date), 0) AS montant_total
            FROM Commercant AS R NATURAL JOIN Transaction
            WHERE SIREN LIKE :siren
            GROUP BY SIREN");
        $sql->bindParam(':siren', $SIREN);
        $sql->bindParam(':date', $date);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_OBJ);
        $ligne = $result;

        if (!empty($ligne)) {
            displayInterface($ligne[0]->SIREN, $ligne[0]->Raison_sociale, $ligne[0]->montant_total, $ligne[0]->nbT, $date);
        }
        return $result;
    }

    function displayInterface($SIREN, $RSociale, $Montant, $nbT, $date)
    { // fonction qui affiche les informations
        echo '<div class="treasury_sect">
                <div class="treasury_sep">
                    <p class="big_text">Bonsoir <span class="bold">' . $RSociale . '</span></p>
                    <p class="blue75">' . $SIREN . '</p>
                </div>
                <div class="treasury_sep">
                    <p class="important big_text">
                        <span style="color:' . ($Montant > 0 ? 'green;' : 'inherit') . ';">' . number_format($Montant, 0, ',', ' ') . ' €</span>
                    </p>

                <div class="treasury_sep">
                    <p class="big_text">Nombre de transactions</p>
                    <p class="blue75">' . $nbT . '</p>
                </div>

                <form action="" method="post" class="treasury_user__form">
                    <div class="form__group">
                        <label for="date">Date :</label>
                        <div class="input__container">
                            <input type="date" id="date" name="date" value="' . $date . '">

                        </div>
                    </div>
                    <div class="form__group">
                        <button type="submit" name="submit" value="Valider" class="btn">Valider</button>
                    </div>
                </form>
            </div>';
    }
    ?>

    <?php
    if (isset($_POST['submit'])) { // si le formulaire a été validé
        if (isset($_POST['date'])) { // si une date a été choisi
            $date = $_POST['date'];
            array_push($array_export, showTreasury($SIREN, $date)); // ajoute le tableau du compte et son solde à la date choisi dans array_export
        } else {
            $date = date('Y-m-d');
            array_push($array_export, showTreasury($SIREN, $date)); // ajoute le tableau du compte et son solde dans array_export
        }
    } else { // si le formulaire n'a pas été vaidé
        $date = date('Y-m-d');
        array_push($array_export, showTreasury($SIREN, $date)); // ajoute le tableau du compte et son solde dans array_export
    }
    echo '
        <p style="margin-left: 18px;">Exporter les résultats en :</p>
        <div class="export_wrap">
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=CSV&date=' . $date . '\', \'_blank\');">CSV</button>
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=XLS&date=' . $date . '\', \'_blank\');">XLS</button>
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=PDF&date=' . $date . '\', \'_blank\');">PDF</button>
        </div>
        ';
    include("user_treasury_graphics.php"); // include le code du graphique linéaire
    echo '<div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>';
    ?>
    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>