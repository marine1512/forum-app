const modal = document.getElementById("modal");
    const createSubjectButton = document.getElementById("create-subject");
    const closeModalButton = document.querySelector(".close");

    // Ouvrir la modale
    createSubjectButton.addEventListener("click", () => {
        modal.style.display = "block";
    });

    // Fermer la modale
    closeModalButton.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Clic en dehors de la modale pour la fermer
    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });

  document.querySelectorAll('.premier-bloc-forum ul li a').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.premier-bloc-forum ul li a')
        .forEach(el => el.classList.remove('active'));
      item.classList.add('active');
      // pas de preventDefault: si la page recharge, Twig re-appliquera l’état actif
    });
  });