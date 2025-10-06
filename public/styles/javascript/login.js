document.addEventListener('DOMContentLoaded', () => {
    console.log("JavaScript chargé !");

    const passwordInput = document.getElementById('registration_form_plainPassword');
    const togglePasswordButton = document.getElementById('eye');

    if (passwordInput && togglePasswordButton) {
        togglePasswordButton.addEventListener('click', function () {
            const isPasswordVisible = passwordInput.type === 'password';
            passwordInput.type = isPasswordVisible ? 'text' : 'password';

        });
    } else {
        console.error("Élément #password ou #eye introuvable dans le DOM !");
    }
});