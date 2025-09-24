// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', () => {
    console.log("JavaScript chargé !");

    // Sélection des éléments HTML
    const passwordInput = document.getElementById('registration_form_plainPassword');
    const togglePasswordButton = document.getElementById('eye');

    if (passwordInput && togglePasswordButton) {
        // Ajouter un événement au clic sur l'image "eye"
        togglePasswordButton.addEventListener('click', function () {
            // Basculer le type entre "password" et "text"
            const isPasswordVisible = passwordInput.type === 'password';
            passwordInput.type = isPasswordVisible ? 'text' : 'password';

        });
    } else {
        console.error("Élément #password ou #eye introuvable dans le DOM !");
    }
});