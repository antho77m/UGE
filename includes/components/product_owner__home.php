<?php

include "nav.php" ;

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
