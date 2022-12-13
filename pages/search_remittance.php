<?php

session_start();
include(dirname(__FILE__, 2) . "/router.php");

if (isset($_SESSION['niveau'])) { 
    if ($_SESSION['niveau'] == 2) { // si connecté en tant qu'admin
        header("Location: login.php"); // redirige vers la page de connexion
    }
} else {
    header("Location: login.php"); // redirige vers la page de connexion
}

include ROOT . "/includes/cnx.inc.php";

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remises</title>
    <meta property="og:description" content="Remises">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
</head>

<body>
    <?php include ROOT . "/includes/components/nav.php"; ?>

    <div class="navbar mobile-nav">
        <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
            <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
        </div>

        <?php if ($_SESSION['niveau'] == 1) : ?>
            <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
                <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
            </div>
        <?php elseif ($_SESSION['niveau'] == 3) : ?>
            <div class="icon_container" onclick="window.location.href='/pages/PO_graphics.php'">
                <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
            </div>
        <?php endif; ?>

        <div class="icon_container" onclick="window.location.href='/pages/search_unpaid.php'">
            <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
        </div>

        <div class="icon_container" onclick="window.location.href='/pages/search_remittance.php'">
            <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
        </div>
    </div>

    <section class="remittance_sect">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 24px;">Consultation des remises</p>
            <div class="logos pc-nav">
                <div class="icon_container" onclick="window.location.href='/pages/treasury.php'">
                    <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
                </div>

                <?php if ($_SESSION['niveau'] == 1) : ?>
                    <div class="icon_container" onclick="window.location.href='/pages/user_graphics.php'">
                        <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
                    </div>
                <?php elseif ($_SESSION['niveau'] == 3) : ?>
                    <div class="icon_container" onclick="window.location.href='/pages/PO_graphics.php'">
                        <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
                    </div>
                <?php endif; ?>

                <div class="icon_container" onclick="window.location.href='/pages/search_unpaid.php'">
                    <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
                </div>

                <div class="icon_container" onclick="window.location.href='/pages/search_remittance.php'">
                    <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
                </div>
            </div>
        </div>
    </section>

    <form action="/pages/search_remittance.php" method="post" class="client__form">

        <?php if ($_SESSION['niveau'] == 3) : ?>
            <div class="form__group">
                <label for="name">N° de SIREN</label>
                <div class="input__container">
                    <input type="number" name="siren" id="siren" placeholder="SIREN" autocomplete="off">
                </div>
            </div>

            <div class="form__group">
                <label for="name">Nom du client</label>
                <div class="input__container">
                    <input type="text" name="rsociale" id="rsociale" placeholder="Raison sociale" autocomplete="off">
                </div>
            </div>
        <?php endif; ?>

        <div class="form__group">
            <label for="name">N° de REMISE</label>
            <div class="input__container">
                <input type="number" name="remise" id="remise" placeholder="numéro de remise" autocomplete="off">
            </div>
        </div>

        <div class="form__group">
            <label for="name">Date de début</label>
            <div class="input__container">
                <input type="date" name="dd" id="dd" value="2010-01-01">
            </div>
        </div>

        <div class="form__group">
            <label for="name">Date de fin</label>
            <div class="input__container">
                <input type="date" name="df" id="df" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <button type="submit" name="submit" value="Validez" class="btn" style="margin-top: 30px;">Confirmer</button>

    </form>
    </section>

    <?php
    if ((isset($_POST['dd']) && isset($_POST['df']) && isset($_POST['remise'])) && isset($_SESSION['niveau'])) {
        $SIREN;
        $Raison_Sociale;
        $dd = $_POST['dd'];
        $df = $_POST['df'];
        if ($_SESSION['niveau'] == 3) { // si connecté en tant que PO
            if (!isset($_POST['siren']) || !isset($_POST['rsociale'])) {
                header("Location: login.php");
            }
            if (!empty($_POST['siren'])) {
                $SIREN = $_POST['siren'];
            } else {
                $SIREN = '%';
            }
            if (!empty($_POST['rsociale'])) {
                $Raison_Sociale = $_POST['rsociale'];
            } else {
                $Raison_Sociale = '%';
            }
        } else if ($_SESSION['niveau'] == 1) { // si connecté en tant que Commerçant
            if (!isset($_SESSION['SIREN'])) {
                header("Location: login.php");
            }
            $SIREN = $_SESSION['SIREN'];
        }
        if (!empty($_POST['remise'])) { 
            $num_remise = $_POST['remise'];
        } else {
            $num_remise = '%';
        }

        if (isset($_POST['rsociale'])) { // si le champ raison sociale existe
            // récupère tous les siren et dates de traitement de remises présente entre les dates dd et df 
            $remises = $cnx->prepare("SELECT DISTINCT SIREN, date_traitement FROM Commercant NATURAL JOIN Transaction WHERE SIREN LIKE :siren AND num_remise LIKE :num_remise AND Raison_sociale LIKE :raison_sociale AND date_traitement BETWEEN :dd AND :df");
            $remises->bindParam(':siren', $SIREN); // SIREN, '%' si aucun siren renseigné, permettant de rechercher tous les sirens
            $remises->bindParam(':raison_sociale', $Raison_Sociale); // Raison_sociale, '%' si aucune raison sociale renseigné, permettant de rechercher toutes les raisons sociales
            $remises->bindParam(':num_remise', $num_remise); // num_remise, '%' si aucun numéro de remise renseigné, permettant de rechercher tous les numéros de remises
            $remises->bindParam(':dd', $dd); // date début
            $remises->bindParam(':df', $df); // date fin
        } else {
            // récupère tous les siren et dates de traitement de remises présente entre les dates dd et df
            $remises = $cnx->prepare("SELECT DISTINCT SIREN, date_traitement FROM Commercant NATURAL JOIN Transaction WHERE SIREN LIKE :siren AND num_remise LIKE :num_remise AND date_traitement BETWEEN :dd AND :df");
            $remises->bindParam(':siren', $SIREN); // SIREN, '%' si aucun siren renseigné, permettant de rechercher tous les sirens
            $remises->bindParam(':num_remise', $num_remise); // num_remise, '%' si aucun numéro de remise renseigné, permettant de rechercher tous les numéros de remises
            $remises->bindParam(':dd', $dd); // date début
            $remises->bindParam(':df', $df); // date fin
        }
        $verif = $remises->execute();
        $remises = $remises->fetchAll();
        echo '<b style="margin-left: 18px;">Résultat: ' . count($remises) . '</b><br><br>';

        // EXPORT DES DONNEES
        if (count($remises) > 0) {
            echo '
            <p style="margin-left: 18px;">Exporter les résultats en :</p>
            <div class="export_wrap">
            <button class="export" onclick="window.open(\'/pages/exports/export_remittance.php?format=CSV&detail=0\', \'_blank\');">CSV</button>
            <button class="export" onclick="window.open(\'/pages/exports/export_remittance.php?format=XLS&detail=0\', \'_blank\');">XLS</button>
            <button class="export" onclick="window.open(\'/pages/exports/export_remittance.php?format=PDF&detail=0\', \'_blank\');">PDF</button>
            <button class="export" onclick="window.open(\'/pages/exports/export_remittance.php?format=CSV&detail=1\', \'_blank\');">CSV détaillé</button>
            <button class="export" onclick="window.open(\'/pages/exports/export_remittance.php?format=XLS&detail=1\', \'_blank\');">XLS détaillé</button>
            <button class="export" onclick="window.open(\'/pages/exports/export_remittance.php?format=PDF&detail=1\', \'_blank\');">PDF détaillé</button>
            </div>
            ';
        }

        $array_remises = array();
        $array_remises_detailles = array();
        echo '<p style="margin-top: 10px; text-align: center; display: flex; align-items: center; justify-content: center;">Cliquez pour avoir les détails <span class="material-symbols-outlined">touch_app</span></p>';
        foreach ($remises as $ligne) { // un par un
            // récupère les informations de la remise à la date de traitement, le numéro de remise et le SIREN indiqué
            $total_remises = $cnx->prepare("SELECT SIREN, Raison_sociale, num_remise, date_traitement, count(num_autorisation) AS nb_transactions, SUM(montant) AS montant_total, (SELECT SUM(montant)*2 FROM Transaction WHERE num_remise = R.num_remise AND sens = '-') AS montant_impayes
            FROM Commercant 
            NATURAL JOIN Transaction AS R
            WHERE SIREN = :siren AND num_remise LIKE :num_remise AND date_traitement = :date
            GROUP BY num_remise");
            $total_remises->bindParam(':siren', $ligne['SIREN']);
            $total_remises->bindParam(':date', $ligne['date_traitement']);
            $total_remises->bindParam(':num_remise', $num_remise);
            $verif = $total_remises->execute();
            if (empty($verif)) {
                exit("Erreur lors de la sélection");
            }
            $total_remises = $total_remises->fetch();

            if (!empty($total_remises)) {
                $montant_total = $total_remises['montant_total'] - $total_remises['montant_impayes']; // calcul du montant total
                echo '
                <section class="remittance_results_wrap">
                    <div class="remittance_results">
                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">SIREN</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $total_remises['SIREN'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">Raison sociale</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $total_remises['Raison_sociale'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">N° de remise</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $total_remises['num_remise'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">Date de traitement</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $total_remises['date_traitement'] . '</p>
                        </div>

                        <div class="remittance_result">
                            <p style="font-size: 14px; color: var(--blue75);">Nb transac.</p>
                            <p style="font-size: 18px; font-weight: 500;">' . $total_remises['nb_transactions'] . '</p>
                        </div>
                        
                        <div class="remittance_result' . ($montant_total >= 0 ? ' positive' : ' negative') . '">
                            <p style="font-size: 14px; color: var(--blue75);">Montant total</p>
                            <p class="montant" style="font-size: 18px; font-weight: 500;">' . ($montant_total >= 0 ? ('+' . $montant_total) : $montant_total) . '</p>
                        </div>
                    </div>';

                array_push($array_remises, [$total_remises['SIREN'], $total_remises['Raison_sociale'], $total_remises['num_remise'], $total_remises['date_traitement'], $total_remises['nb_transactions'], "EUR", $montant_total]); // ajoute dans array_remises les caractéristiques de la remise (SIREN, num_remise, ..)

                // récupère le détail des transactions de la remise (SIREN, date_vente, date_traitement, num_carte, reseau, num_autorisation, montant, sens)
                $details_remises = $cnx->prepare("SELECT SIREN, date_vente, date_traitement, num_carte, reseau, num_autorisation, montant, sens FROM Commercant NATURAL JOIN Transaction WHERE SIREN = :siren AND num_remise LIKE :num_remise AND date_traitement = :date");
                $details_remises->bindParam(':siren', $ligne['SIREN']);
                $details_remises->bindParam(':date', $ligne['date_traitement']);
                $details_remises->bindParam(':num_remise', $num_remise);
                $verif = $details_remises->execute();
                if (empty($verif)) {
                    exit("Erreur lors de la sélection");
                }
                $details_remises = $details_remises->fetchAll();
                array_push($array_remises_detailles, $details_remises); // ajoute dans array_remises_detailles details_remises contenant les transactions de la remise
                foreach ($details_remises as $ligne) {
                    echo '
                        <div class="remittance_results__details hidden">
                            <div class="remittance_result">
                                <p style="font-size: 14px; color: var(--blue75);">Date de vente</p>
                                <p style="font-size: 16px;">' . $ligne['date_vente'] . '</p>
                            </div>

                            <div class="remittance_result">
                                <p style="font-size: 14px; color: var(--blue75);">Réseau</p>
                                <p style="font-size: 16px;">' . $ligne['reseau'] . '</p>
                            </div>

                            <div class="remittance_result">
                                <p style="font-size: 14px; color: var(--blue75);">N° d\'autorisation</p>
                                <p style="font-size: 16px;">' . $ligne['num_autorisation'] . '</p>
                            </div>

                            <div class="remittance_result">
                                <p style="font-size: 14px; color: var(--blue75);">Devise</p>
                                <p style="font-size: 16px;">EUR</p>
                            </div>

                            <div class="remittance_result">
                                <p style="font-size: 14px; color: var(--blue75);">Sens</p>
                                <p style="font-size: 16px;">' . $ligne['sens'] . '</p>
                            </div>

                            <div class="remittance_result">
                                <p style="font-size: 14px; color: var(--blue75);">Montant</p>
                                <p style="font-size: 16px;">' . $ligne['montant'] . '</p>
                            </div>

                            <div class="remittance_result">
                                <p style="font-size: 14px; color: var(--blue75);">N° de carte</p>
                                <p style="font-size: 16px;">' . $ligne['num_carte'] . '</p>
                            </div>
                        </div>
                        ' . ($ligne == end($details_remises) ? '</section>' : '');
                }
            }
        }
        echo '<div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>';
        // VARIABLES SESSIONS pour l'export de données
        $_SESSION['tab_remises'] = $array_remises; // créer la variable de session tab_remises pour stocker le tableau des remises
        $_SESSION['tab_remises_detailles'] = $array_remises_detailles; // créer une variable tab_remises_detailles pour stocker le tableau des remises avec le détail de chaque transaction
    }
    ?>

    <script>
        const remittance_results_wrap = document.querySelectorAll('.remittance_results_wrap');

        remittance_results_wrap.forEach((ele) => {
            let remittance_results = ele.querySelector('.remittance_results');
            remittance_results.addEventListener('click', () => {
                remittance_results.classList.toggle('remittance_results-active');
                ele.querySelectorAll('.remittance_results__details').forEach((ele) => {
                    ele.classList.toggle('hidden');
                });
            });
        });
    </script>
    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>