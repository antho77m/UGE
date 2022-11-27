
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
        <div class="logos">
            <picture>
                <source srcset="<?= $basepath ?>/src/img/remittanceIcon.svg" type="image/svg">
                <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Logo UGE">
            </picture>
            <picture>
                <source srcset="<?= $basepath ?>/src/img/leaderboard.svg" type="image/svg">
                <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Logo UGE">
            </picture>
        </div>
    </div>
</section>


<section class="clients_accounts">
    <div class="label">
        <p class="blue75">Nom & SIREN</p>
        <p class="blue75">Nb de trans.</p>
        <p class="blue75">Montant tot.</p>
    </div>

    <?php
    $clients = load_clients();
    ?>

    <?php foreach ($clients as $client) : ?>
        <?php $montant_total = ($client['montant_total']) - ($client['montant_impayes']); ?>
        <div class="client_item">

            <div class="flex flex1">
                <div class="nameSIREN">
                    <p><?= $client['SIREN'] ?></p>
                    <p class="blue75"><?= $client['Raison_sociale'] ?></p>
                </div>
            </div>

            <div class="flex flex2">
                <p><?= $client['nbTransactions'] ?></p>
            </div>

            <div class="flex flex3">
                <p class="<?= ($montant_total < 0) ? 'red' : '' ?>"><?= $montant_total ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<div class="PO_nav">
    <div class="icon_container" onclick="window.location.href='/graphics'">
        <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
    </div>

    <div class="icon_container" onclick="window.location.href='/home'">
        <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
    </div>

    <div class="icon_container" onclick="window.location.href='/remittance'">
        <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
    </div>
</div>

<script>
    const search = document.getElementById('search');
    const client_item = document.querySelectorAll('.client_item');

    search.addEventListener('keyup', (e) => {
        const searchValue = e.target.value.toLowerCase();

        client_item.forEach((client) => {
            const clientName = client.querySelector('.nameSIREN').textContent.toLowerCase();

            if (clientName.includes(searchValue)) {
                client.style.display = 'flex';
            } else {
                client.style.display = 'none';
            }
        });
    });
</script>