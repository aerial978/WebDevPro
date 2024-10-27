document.addEventListener('DOMContentLoaded', function () {
    // Sélectionne tous les éléments avec la classe page-link
    let paginationLinks = document.querySelectorAll('.pagination .page-link');
    // Sélectionne l'élément avec l'ID 'comments-list' où les commentaires seront affichés
    const commentsList = document.getElementById('comments-list');
    // Récupère le numéro de la page actuelle depuis l'URL, ou 1 si aucun paramètre 'page' n'est présent
    const currentPage = new URLSearchParams(window.location.search).get('page') || 1;

    // Initialiser l'état de la pagination
    updatePagination(currentPage);

    // Pour chaque lien de pagination, on ajoute un écouteur d'événement 'click'
    paginationLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            // Empêche le comportement par défaut du lien (qui est de naviguer)
            event.preventDefault();
            // Récupère le numéro de page à partir de l'attribut 'data-page' du lien cliqué
            const page = this.getAttribute('data-page');

            // Effectue une requête GET pour récupérer les commentaires de la page sélectionnée
            fetch(`${window.location.origin}${window.location.pathname}?page=${page}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Indique que la requête est effectuée en AJAX
                }
            })
            .then(response => response.text()) // Convertir la réponse en texte
            .then(data => {
                // Crée un nouveau document HTML à partir du texte de la réponse
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                // Sélectionne la nouvelle liste de commentaires dans le document retourné
                const newCommentsList = doc.querySelector('#comments-list');
                // Remplace le contenu HTML de `commentsList` par le contenu HTML de `newCommentsList`.
                commentsList.innerHTML = newCommentsList.innerHTML;
                // Mise à jour de l'état de la pagination
                updatePagination(page);
                // Mise à jour de l'URL sans recharger la page
                window.history.pushState({}, '', `${window.location.pathname}?page=${page}`);
            });
        });
    });
        
    // Fonction pour mettre à jour l'état de la pagination en fonction de la page actuelle
    function updatePagination(currentPage) {
        paginationLinks.forEach(link => {
            // Récupère le numéro de page associé au lien
            const page = link.getAttribute('data-page');
            // Active le lien correspondant à la page actuelle, désactive les autres
            if (page == currentPage) {
                link.parentElement.classList.add('active');
            } else {
                link.parentElement.classList.remove('active');
            }
        });
    }
});