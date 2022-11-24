<section class="clients_manage">

    <p>Réalisez une action sur les comptes</p>

    <div class="actions__wrapper">
        <button class="actions_btn btn__add">
            <span class="material-symbols-outlined icon">add</span>
            Ajouter un compte
        </button>
        <button class="actions_btn btn__edit">
            <span class="material-symbols-outlined icon">edit</span>
            Modifier un compte
        </button>
        <button class="actions_btn btn__delete">
            <span class="material-symbols-outlined icon">delete</span>
            Supprimer un compte
        </button>
    </div>

</section>

<script>
    const btnAdd = document.querySelector('.btn__add');
    const btnEdit = document.querySelector('.btn__edit');
    const btnDelete = document.querySelector('.btn__delete');

    btnAdd.addEventListener('click', () => {
        window.location.href = "<?= $basepath ?>/client?add";
    });

    btnEdit.addEventListener('click', () => {
        window.location.href = "<?= $basepath ?>/client?edit";
    });

    btnDelete.addEventListener('click', () => {
        window.location.href = "<?= $basepath ?>/client?delete";
    });
</script>