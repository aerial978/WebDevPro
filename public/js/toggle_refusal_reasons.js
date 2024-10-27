document.addEventListener('DOMContentLoaded', function () {
    // Sélectionner tous les formulaires de modération
    let moderationForms = document.querySelectorAll('#editCommentForm');

    moderationForms.forEach(function (form) {
        //let formType = form.getAttribute('data-form-type'); // "comment" ou "post"
        
        // Récupérer les éléments en utilisant des classes CSS partagées
        let statusSelect = form.querySelector('#status-comment');
        let moderationReasonContainer = form.querySelector('.moderation-reason');
        let detailRefusTextarea = form.querySelector('.detail-refus-container');
        let refusalReasonRadio = form.querySelectorAll('input[name="moderation-reason"]');
        let otherRadio = form.querySelector('input[name="moderation-reason"][value="6"]'); // "6" correspond à "Autre (à préciser)"

        // Fonction pour afficher/masquer les raisons de refus en fonction du statut
        function toggleRefusalReasons() {
            if (statusSelect && statusSelect.value === '5') { // ID 5 correspond à "Refusé"
                if (moderationReasonContainer) {
                    moderationReasonContainer.style.display = 'block';
                }
            } else {
                if (moderationReasonContainer) {
                    moderationReasonContainer.style.display = 'none';
                }
                if (detailRefusTextarea) {
                    detailRefusTextarea.style.display = 'none'; // Masquer le champ de texte si le statut n'est pas "Rejeté"
                }
            }
        }

        // Fonction pour afficher/masquer le champ textarea si "Autre" est sélectionné
        function toggleDetailRefus() {
            if (otherRadio && otherRadio.checked) {
                if (detailRefusTextarea) {
                    detailRefusTextarea.style.display = 'block';
                }
            } else {
                if (detailRefusTextarea) {
                    detailRefusTextarea.style.display = 'none';
                }
            }
        }

        // Appeler les fonctions au chargement initial de la page pour chaque formulaire
        if (statusSelect) {
            toggleRefusalReasons();
        }
        if (refusalReasonRadio) {
            refusalReasonRadio.forEach(radio => {
                if (radio.checked) {
                    toggleDetailRefus();
                }
            });
        }

        // Ajouter des écouteurs d'événements pour le changement de statut et les boutons radio de raisons
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                toggleRefusalReasons();
            });
        }

        // Ajouter un écouteur sur chaque radio pour déclencher l'affichage du textarea si "Autre" est sélectionné
        if (refusalReasonRadio) {
            refusalReasonRadio.forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleDetailRefus();
                });
            });
        }
    });
});
