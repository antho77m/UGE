<?php
// debug($_SESSION);

$permissions = [
    1 => [
        'status' => 'commercant',
        'titre' => 'Mon compte',
    ],
    2 => [
        'status' => 'administateur',
        'titre' => 'Gestion des comptes',
    ],
    3 => [
        'status' => 'product owner',
        'titre' => 'Acceuil',
    ],
];

?>

<section class="privileges">
    <p>Vous avez les permissions <?= $permissions[$_SESSION['niveau']]['status'] ?></p>
</section>

<nav>
    <?php include "includes/components/logo.php" ?>
    <p class="privileges__title"><?= $permissions[$_SESSION['niveau']]['titre'] ?></p>

    <div class="user">
        <source>
        <div class="user__pic">
            <p><?= $permissions[$_SESSION['niveau']]['status'] == 'administateur' ? 'Ad' : 'PO' ?></p>
        </div>
        </source>
    </div>
</nav>
