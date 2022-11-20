<?php
session_start();

$action = $_SERVER['QUERY_STRING'];
$actions_title = [
    'add' => 'Ajout de compte',
    'edit' => 'Modification de compte',
    'delete' => 'Suppression de compte',
];

include(dirname(__FILE__, 2) . "/includes/components/nav.php");
?>

<section class="clients_manage">

    <p><?= $actions_title[$action] ?></p>

    <form action="" method="get" class="client__form">
        <div class="form__group">
            <label for="name">Nom du client</label>
            <div class="input__container">
                <input type="text" name="name" id="name" placeholder="Raison sociale" autocomplete="off">
            </div>
        </div>

        <div class="form__group">
            <label for="name">N° de SIREN</label>
            <div class="input__container">
                <input type="number" name="siren" id="siren" placeholder="SIREN" autocomplete="off">
            </div>
        </div>

        <div class="form__group">
            <label for="name">Identifiant du compte</label>
            <div class="input__container">
                <input type="number" name="id" id="id" placeholder="Identifiant du compte" autocomplete="off">
            </div>
        </div>

        <?php if ($action !== 'delete') : ?>
            <div class="form__group">
                <label for="name">Mot de passe</label>
                <div class="input__container">
                    <input type="password" name="password" id="password" placeholder="Mot de passe" autocomplete="off">
                </div>
            </div>
        <?php endif; ?>

        <div class="form__group form__action">
            <button class="btn__cancel actions_btn"><span class="material-symbols-outlined icon">close</span>Annuler</button>
            <?php if ($action === 'add') : ?>
                <button type="sumbit" class="btn__confirm actions_btn"><span class="material-symbols-outlined icon">add</span>Confirmer</button>
            <?php elseif ($action === 'edit') : ?>
                <button type="sumbit" class="btn__confirm actions_btn"><span class="material-symbols-outlined icon">edit</span>Modification</button>
            <?php elseif ($action === 'delete') : ?>
                <button type="sumbit" class="btn__delete actions_btn"><span class="material-symbols-outlined icon">delete</span>Suppression</button>
            <?php endif; ?>
        </div>
    </form>


</section>

<script>
    const btnCancel = document.querySelector('.btn__cancel');

    btnCancel.addEventListener('click', (e) => {
        // prevent default behavior
        e.preventDefault();
        // redirect to home page
        window.location.href = "<?= $basepath ?>/home";
    });
</script>