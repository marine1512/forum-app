  window.addEventListener('load', () => {
    const loader = document.getElementById('app-loader');
    const main = document.querySelector('main');

    setTimeout(() => {
      // Masquer le loader
      loader.classList.add('hidden');
      main.style.opacity = 1;

      // 🔁 Redirection après disparition
      window.location.href = "/home"; 
      // ⬆️ remplace 'home' par ta route d’accueil exacte si besoin
    }, 7000); // 7 secondes
  });