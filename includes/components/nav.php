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
        'titre' => 'Accueil',
    ],
];

?>

<?php if ($_SESSION['niveau'] > 1) : ?>
    <section class="privileges">
        <p>Vous avez les permissions <?= $permissions[$_SESSION['niveau']]['status'] ?></p>
    </section>
<?php endif; ?>

<nav>
    <?php include ROOT . "/includes/components/logo.php" ?>
    <p class="privileges__title"><?= $permissions[$_SESSION['niveau']]['titre'] ?></p>

    <div class="user">
        <source>
        <div class="user__pic">
            <?php if ($_SESSION['niveau'] == 1) : ?>
                <p>CO</p>
            <?php elseif ($_SESSION['niveau'] == 2) : ?>
                <p>Ad</p>
            <?php elseif ($_SESSION['niveau'] == 3) : ?>
                <p>PO</p>
            <?php endif; ?>
        </div>
        </source>
    </div>
</nav>
