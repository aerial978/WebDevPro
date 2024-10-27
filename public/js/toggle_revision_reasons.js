/*document.addEventListener('DOMContentLoaded', function () {
    let statusSelect = document.getElementById('status-post');
    let detailRevisionTextarea = document.getElementById('detail-revision');

    
    function toggleDetailRevision() {
        if(statusSelect.value === '8') {
            detailRevisionTextarea.style.display = 'block';
        } else {
            detailRevisionTextarea.style.display = 'none';
        }
    }

    toggleDetailRevision();

    statusSelect.addEventListener('change', toggleDetailRevision);
});*/

document.addEventListener('DOMContentLoaded', function () {
    // Sélectionner tous les formulaires de modération
    let moderationForms = document.querySelectorAll('#editPostForm');

    moderationForms.forEach(function (form) {
        //let formType = form.getAttribute('data-form-type'); // "comment" ou "post"
        
        // Récupérer les éléments en utilisant des classes CSS partagées
        let statusSelect = form.querySelector('#status-post');
        let moderationReasonContainer = form.querySelector('.moderation-reason');
        let detailRevisionTextarea = form.querySelector('.detail-revision-container');
        let revisionReasonRadio = form.querySelectorAll('input[name="moderation-reason"]');
        let otherRadio = form.querySelector('input[name="moderation-reason"][value="6"]'); // "6" correspond à "Autre (à préciser)"

        // Fonction pour afficher/masquer les raisons de refus en fonction du statut
        function toggleRevisionReasons() {
            if (statusSelect && statusSelect.value === '8') { // ID 5 correspond à "Needs to revision"
                if (moderationReasonContainer) {
                    moderationReasonContainer.style.display = 'block';
                }
            } else {
                if (moderationReasonContainer) {
                    moderationReasonContainer.style.display = 'none';
                }
                if (detailRevisionTextarea) {
                    detailRevisionTextarea.style.display = 'none'; // Masquer le champ de texte si le statut n'est pas "Needs to revision"
                }
            }
        }

        // Fonction pour afficher/masquer le champ textarea si "Autre" est sélectionné
        function toggleDetailRevision() {
            if (otherRadio && otherRadio.checked) {
                if (detailRevisionTextarea) {
                    detailRevisionTextarea.style.display = 'block';
                }
            } else {
                if (detailRevisionTextarea) {
                    detailRevisionTextarea.style.display = 'none';
                }
            }
        }

        // Appeler les fonctions au chargement initial de la page pour chaque formulaire
        if (statusSelect) {
            toggleRevisionReasons();
        }
        if (revisionReasonRadio) {
            revisionReasonRadio.forEach(radio => {
                if (radio.checked) {
                    toggleDetailRevision();
                }
            });
        }

        // Ajouter des écouteurs d'événements pour le changement de statut et les boutons radio de raisons
        if (statusSelect) {
            statusSelect.addEventListener('change', toggleRevisionReasons);
        }

        // Ajouter un écouteur sur chaque radio pour déclencher l'affichage du textarea si "Autre" est sélectionné
        if (revisionReasonRadio) {
            revisionReasonRadio.forEach(radio => {
                radio.addEventListener('change', toggleDetailRevision);
            });
        }
    });
});




