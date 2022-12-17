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
</head>

<body>

    <?php
    function showTreasuryAllClients($SIREN, $date, $ORDER, $SENS)
    { // Show the solde of all client with options like order and sens and/or date
        global $cnx;
        $sql = $cnx->prepare("SELECT SIREN, Raison_sociale, 
        COALESCE((SELECT count(num_autorisation) FROM Transaction WHERE SIREN = R.SIREN), 0) AS nbT, 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+' AND date_traitement <= :date), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-' AND date_traitement <= :date), 0) AS montant_total
        FROM Commercant AS R
        WHERE SIREN LIKE :siren
        GROUP BY SIREN ORDER BY $ORDER $SENS");
        $sql->bindParam(':siren', $SIREN);
        $sql->bindParam(':date', $date);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_OBJ);
        foreach ($result as $ligne) {
            if ($ligne->montant_total >= 0) {
                $green = 'green';
            } else {
                $green = 'red';
            }
            echo '
                <section class="remittance_results_wrap">
                    <div class="remittance_results">
                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">SIREN</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $ligne->SIREN . '</p>
                        </div>
                        
                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">Raison sociale</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $ligne->Raison_sociale . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">Nombre de transactions</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $ligne->nbT . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">Montant total</p>
                            <p style="font-size: 18px; font-weight: 500; color: var(--red75);"><span style="color:' .$green. ';">' . $ligne->montant_total . '</span></p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">Date</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $date . '</p>
                        </div>
                    </div>
                </section>
            ';
        }
        return $result;
    }
    ?>
    
    <section class="graphics_section">
        <form action="" method="post" class="client_form">
            <div class="form_group" style="width: 100%;">
                <label for="">Date : </label>
                <div class="input__container">
                    <input type="date" name="date" id="date" value=<?= date('Y-m-d') ?>>
                </div>
            </div>

            <div class="form_group" style="width: 100%;">
                <label for="">SIREN :</label>
                <div class="input__container">
                    <input type="text" name="SIREN" id="SIREN" maxlength="9">
                </div>
            </div>

            <div class="form_group" style="width: 100%; margin-bottom: 10px;">
                <label for="">Afficher les soldes des clients :</label>
                <select id="trie_type" name="trie_type">
                    <option value="SIREN">SIREN</option>
                    <option value="montant_total">Montant</option>
                </select>
            </div>

            <div class="form_group" style="width: 100%;">
                <div class="form_radio">
                    <div class="form_select">
                        <input type="radio" id="croissant" name="sens" value="ASC">
                        <label for="">Croissant</label>
                    </div>

                    <div class="form_select">
                        <input type="radio" id="decroissant" name="sens" value="DESC">
                        <label for="">Décroissant</label>
                    </div>
                </div>
            </div>

            <div class="form_group" style="margin-top: 30px;">
                <button class="btn" type="submit" name="submit" value="Valider">Envoyer</button>
            </div>
        </form>
    </section>

    <?php
    if (isset($_POST['submit'])) {
        $date = date('Y-m-d');
        $SIREN = '%';
        $ORDER = '';
        $SENS = 'ASC';
        if (isset($_POST['date'])) {
            $date = $_POST['date'];
        }
        if (!empty($_POST['SIREN'])) {
            $SIREN = $_POST['SIREN'];
        }
        if (isset($_POST['trie_type'])) {
            $ORDER = $_POST['trie_type'];
        }
        if (isset($_POST['sens'])) {
            $SENS = $_POST['sens'];
        }
        echo '
        <p style="margin-left: 18px;">Exporter les résultats en :</p>
        <div class="export_wrap">
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=CSV&date=' . $date . '\', \'_blank\');">CSV</button>
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=XLS&date=' . $date . '\', \'_blank\');">XLS</button>
        <button class="export" onclick="window.open(\'/pages/exports/export_treasury.php?format=PDF&date=' . $date . '\', \'_blank\');">PDF</button>
        </div>
        ';
        array_push($array_export, showTreasuryAllClients($SIREN, $date, $ORDER, $SENS));
    }
    ?>

    <div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>


    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>