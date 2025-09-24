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