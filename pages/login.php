<?php
    if (isset($_GET['error']) && $_GET['error'] == 1){
        echo "mot de passe ou identifiant incorrect";
    }
?>

<?php include "includes/components/logo.php" ?>

<form class="connection__form" action="php_proc/verify_login.php" method="post">

    <div class="form__group">
        <label for="email">Adresse e-mail</label>
        <div class="input__container">
            <span class="material-symbols-outlined icon">mail</span>
            <input type="email" name="email" id="email" placeholder="monadresse@xyz.com" required>
        </div>
    </div>

    <div class="form__group">
        <label for="login">Login</label>
        <div class="input__container">
            <span class="material-symbols-outlined icon">Login</span>
            <input type="text" name="login" id="login" placeholder="monidentifiant" required>
        </div>
    </div>

    <div class="form__group pass__group">
        <label for="password">Mot de passe</label>
        <div class="input__container">
            <span class="material-symbols-outlined icon">key</span>
            <input type="password" name="password" id="password" placeholder="Mot de passe" autocomplete="true" required>
            <span class="material-symbols-outlined icon" id="togglePassword">Visibility</span>
        </div>
        <a href="/forgot-password" class="forgot__password">Mot de passe oublié ?</a>
    </div>

    <div class="form__group">
        <button type="submit" class="btn connection" disabled>CONNEXION</button>
    </div>


</form>

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
    const email_input = document.querySelector("#email");
    const login_input = document.querySelector("#login");
    const password_input = document.querySelector("#password");
    const form = document.querySelector(".connection__form");

    form.addEventListener("input", () => {
        if (email_input.value.length > 0 && login_input.value.length > 0 && password_input.value.length > 0) {
            connectionBtn.classList.add("active-btn");
            connectionBtn.disabled = false;
        } else {
            connectionBtn.classList.remove("active-btn");
            connectionBtn.disabled = true;
        }
    })

</script>