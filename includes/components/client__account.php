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
            <div class="icon_container" onclick="window.location.href='/graphics'">
                <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
            </div>

            <div class="icon_container" onclick="window.location.href='/remittance'">
                <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
            </div>
        </div>
    </div>
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





<div class="navbar">
    <div class="icon_container" onclick="window.location.href='/graphics'">
        <img src="<?= $basepath ?>/src/img/leaderboard.svg" alt="Home icon">
    </div>
    
    <div class="icon_container" onclick="window.location.href='/unpaid'">
        <img src="<?= $basepath ?>/src/img/unpaid.svg" alt="Unpaid icon">
    </div>

    <div class="icon_container" onclick="window.location.href='/home'">
        <img src="<?= $basepath ?>/src/img/home.svg" alt="Home icon">
    </div>

    <div class="icon_container" onclick="window.location.href='/treasury'">
        <img src="<?= $basepath ?>/src/img/treasury.svg" alt="Treasury icon">
    </div>

    <div class="icon_container" onclick="window.location.href='/remittance'">
        <img src="<?= $basepath ?>/src/img/remittanceIcon.svg" alt="Remittance icon">
    </div>
</div>