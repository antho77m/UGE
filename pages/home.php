<?php include "includes/components/nav.php";
$_SESSION['try'] = 0;

// si le niveau de l'utilisateur est 1, on inclut le fichier client__account.php
if ($_SESSION['niveau'] == 1) {
    include "includes/components/client__account.php";
} else if ($_SESSION['niveau'] == 2) {
    include "includes/components/admin__clients_manage.php";
} else if ($_SESSION['niveau'] == 3) {
    include "includes/components/product_owner__home.php";
}

?>

