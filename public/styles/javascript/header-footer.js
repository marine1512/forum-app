// Sélectionnez l'icône et les liens
const icon = document.getElementById('icon'); // L'icône de menu
const nav = document.getElementById('nav'); // Le conteneur nav
const links = document.querySelectorAll('nav ul li a'); // Chaque lien dans le menu

// Basculer l'affichage du menu avec l'icône
icon.addEventListener('click', function () {
    nav.classList.toggle('active'); // Ajout/suppression de la classe "active"
});

// Ajouter un événement pour chaque lien de menu
links.forEach((link) => {
    link.addEventListener('click', function (event) {
        event.preventDefault(); // Empêcher le comportement par défaut du lien

        // Masquer le menu
        nav.classList.remove('active'); // Retirer la classe 'active'

        // Attendez la durée de la transition avant de rediriger
        const href = this.getAttribute('href'); // Extraire le lien
        setTimeout(() => {
            window.location.href = href; // Rediriger après la transition
        }, 500); // Correspond à la durée de la transition CSS (0.3s ici)
    });
});

document.querySelectorAll('nav li').forEach(item => {
    item.addEventListener('click', () => {
        document.querySelectorAll('nav li').forEach(el => el.classList.remove('active'));
        
        item.classList.add('active');
    });
});