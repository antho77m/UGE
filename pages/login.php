<?php

session_start();
include(dirname(__FILE__, 2) . "/router.php");

if (isset($_GET['error']) && $_GET['error'] == 1) {
    $try = $_SESSION['try'];

    // $_SESSION['try'] = 0;
    // setcookie('blocked', '', 1);

    echo '<p class="incorrect_pass">mot de passe ou identifiant incorrect, plus que ' . (3 - $try) . ' essais</p>';
    echo "
    <script>

        console.log('test');
        setTimeout(function() {
            document.querySelector('.incorrect_pass').remove();
        }, 1500)
    </script>";
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <meta property="og:description" content="Connexion au site">
    <link rel="stylesheet" href="/src/styles/app.css?<?= sha1(rand()) ?>">
    <link rel="stylesheet" href="/src/styles/pages/login.css?<?= sha1(rand()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="image/svg+xml" href="/src/img/Logo UGE.svg" id="js-favicon" />
</head>

<body>

    <div class="pc_logo__wrapper">

        <div class="PClogo">
            <picture>
                <source srcset="/src/img/logoUGEfull.svg" type="image/svg">
                <img src="/src/img/logoUGEfull.svg" alt="Logo UGE">
            </picture>
            <p>Toutes vos finances, à <span class="important">un seul</span> endroit.</p>
        </div>

        <?php include ROOT . "/includes/components/logo.php";


        if (!isset($_COOKIE['blocked'])) {

        ?>

            <form class="connection__form" action="/pages/verify_login.php" method="post">

                <div class="form__group">
                    <label for="id">Identifiant</label>
                    <div class="input__container">
                        <span class="material-symbols-outlined icon">Login</span>
                        <input type="text" name="login" id="id" placeholder="Mon identifiant" required>
                    </div>
                </div>

                <div class="form__group pass__group">
                    <label for="password">Mot de passe</label>
                    <div class="input__container">
                        <span class="material-symbols-outlined icon">key</span>
                        <input type="password" name="password" id="password" placeholder="Mot de passe" autocomplete="true" required>
                        <span class="material-symbols-outlined icon" id="togglePassword">Visibility</span>
                    </div>
                </div>

                <div class="form__group">
                    <button type="submit" class="btn connection" disabled>CONNEXION</button>
                </div>


            </form>


        <?php
        } else {
            echo '<p class="blocked">Vous avez été bloqué pour 1 heure</p>';
        }
        ?>
    </div>

    <script>
        const inputs = document.querySelectorAll("input");
        inputs.forEach(input => {
            input.addEventListener("input", () => {
                if (input.value.length > 0) {
                    input.previousElementSibling.classList.add("active");
                } else {
                    input.previousElementSibling.classList.remove("active");
                }
            })
        })

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector("#password");
        password.addEventListener("input", () => {
            if (password.value.length > 0) {
                togglePassword.style.display = "block";
            } else {
                togglePassword.style.display = "none";
            }
        })

        togglePassword.addEventListener('click', function(e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            type === 'password' ? togglePassword.textContent = "Visibility_off" : togglePassword.textContent = "Visibility";
        });

        // si chaque champs sont remplis, on affiche le bouton de connexion en rouge
        const connectionBtn = document.querySelector(".connection");
        const id_input = document.querySelector("#id");
        const password_input = document.querySelector("#password");
        const form = document.querySelector(".connection__form");

        form.addEventListener("input", () => {
            if (id_input.value.length > 0 && password_input.value.length > 0) {
                connectionBtn.classList.add("active-btn");
                connectionBtn.disabled = false;
            } else {
                connectionBtn.classList.remove("active-btn");
                connectionBtn.disabled = true;
            }
        })
    </script>

    <script src="/src/scripts/app.js?v=<?= sha1(rand()) ?>" defer></script>
</body>

</html>