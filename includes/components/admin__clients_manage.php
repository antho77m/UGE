<section class="graphics_section">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <p style="font-size: 24px;">Réalisez une action sur les comptes</p>
    </div>

    <div class="actions__wrapper">
        <!-- <button class="actions_btn">
            <span class="material-symbols-outlined icon">add</span>
            Ajouter un compte
        </button>
        <button class="actions_btn">
            <span class="material-symbols-outlined icon">delete</span>
            Supprimer un compte
        </button> -->
        <div class="action__self">
            <p class="action__self_title">Ajout de compte</p>
            <p class="action__self_desc">Permet l’ajout d’un compte commerçant dans la base de données suite aux informations transmises par le Product Owner.</p>
            <div class="action__self_btn add">
                <button class="action_btn btn_add">
                    <span class="material-symbols-outlined icon">add</span>
                    Ajouter
                </button>
            </div>
        </div>

        <div class="action__self">
            <p class="action__self_title">Supression de compte</p>
            <p class="action__self_desc">Permet la suppression d’un compte commerçant de la base de données suite aux informations transmises par le Product Owner.</p>
            <div class="action__self_btn del">
                <button class="action_btn btn_del">
                    <span class="material-symbols-outlined icon">delete</span>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    const btnAdd = document.querySelector('.add');
    const btnDelete = document.querySelector('.del');

    btnAdd.addEventListener('click', () => {
        window.location.href = "<?= $basepath ?>/pages/client.php?add";
    });

    btnDelete.addEventListener('click', () => {
        window.location.href = "<?= $basepath ?>/pages/client.php?delete";
    });
</script>