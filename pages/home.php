<?php include "includes/components/nav.php" ?>

<?php

// // si le niveau de l'utilisateur est 1, on inclut le fichier client__account.php
// if ($_SESSION['niveau'] == 1) {
//     // include "includes/components/client__account.php";
// } else if ($_SESSION['niveau'] == 2) {
//     include "includes/components/admin__clients_manage.php";
// } else if ($_SESSION['niveau'] == 3) {
//     include "includes/components/product_owner__home.php";
// }

?>

<section class="clients_manage">

    <div class="clients_manage__search">
        <p>Liste des comptes (<span class="important"><?= count_clients(); ?></span>)</p>
        <form action="" method="post">

            <div class="form__group">
                <div class="input__container po_search">
                    <span class="material-symbols-outlined icon">search</span>
                    <input type="text" name="search" id="search" placeholder="Recherche">
                </div>
            </div>
        </form>
    </div>



</section>