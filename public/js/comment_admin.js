document.addEventListener('DOMContentLoaded', function() {
    let commentForm = document.querySelector('#editCommentForm');

    if (commentForm !== null) {
        commentForm.addEventListener('submit', function(event) {
            let isValid = true;

            // Récupérer les éléments d'entrée
            let commentContentElement = document.querySelector('#comment-content');
            let statusElement = document.querySelector('#status-comment');
            let moderationReasonElement = document.querySelector('input[name="moderation-reason"]:checked');
            let detailRefusElement = document.querySelector('#detail-refus');

            let commentContent = commentContentElement ? commentContentElement.value : null;
            let statusId = statusElement ? statusElement.value : null;
            let moderationReasonId = moderationReasonElement ? moderationReasonElement.value : null;
            let detailRefus = detailRefusElement ? detailRefusElement.value : null;

            // Réinitialiser les messages d'erreurs et les styles
            document.getElementById('commentContentError').textContent = '';
            document.getElementById('moderationReasonError').textContent = '';
            document.getElementById('refusalDetailError').textContent = '';
            
            // Retirer la classe d'erreur de tous les champs
            commentContentElement.classList.remove('error-field');
            statusElement.classList.remove('error-field');
            if (detailRefusElement) detailRefusElement.classList.remove('error-field');

            // Validation du contenu du commentaire
            if (commentContent.trim() === '') {
                document.getElementById('commentContentError').textContent = 'Content field is required !';
                commentContentElement.classList.add('error-field'); // Ajouter la classe d'erreur
                isValid = false;
            }

            // Validation du statut
            if (statusId === '5') {
                if (moderationReasonElement === null) {
                    if (commentForm.querySelector('#moderationReasonError')) {
                        commentForm.querySelector('#moderationReasonError').textContent = 'Please select a moderation reason!';
                    }
                    isValid = false;
                }
            }

            // Validation du champ "Autre"
            if (moderationReasonId == '6' && detailRefus.trim() === '') {
                if (document.getElementById('refusalDetailError')) {
                    document.getElementById('refusalDetailError').textContent = 'Please specify a reason !';
                    detailRefusElement.classList.add('error-field'); // Ajouter la classe d'erreur
                }
                isValid = false;

                detailRefusElement.style.display = 'block';
            }
            // Empêcher la soumission du formulaire si les données ne sont pas valides
            if (!isValid) {
                event.preventDefault();
            }
        });

        // Écouteurs d'événements pour retirer l'erreur lorsque l'utilisateur clique sur un champ
        document.querySelector('#comment-content').addEventListener('click', function() {
            this.classList.remove('error-field');
            document.getElementById('commentContentError').textContent = ''; // Supprimer le message d'erreur
        });

        document.querySelector('#status-comment').addEventListener('click', function() {
            this.classList.remove('error-field');
            document.getElementById('moderationReasonError').textContent = ''; // Supprimer le message d'erreur
        });

        if (document.querySelector('#detail-refus')) {
            document.querySelector('#detail-refus').addEventListener('click', function() {
                this.classList.remove('error-field');
                document.getElementById('refusalDetailError').textContent = ''; // Supprimer le message d'erreur
            });
        }

        // Ajouter un écouteur sur les boutons radio pour retirer l'erreur quand une option est sélectionnée
        let radioButtons = document.querySelectorAll('input[name="moderation-reason"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('click', function() {
                document.getElementById('moderationReasonError').textContent = ''; // Supprimer le message d'erreur
            });
        });
    }
});

