document.addEventListener('DOMContentLoaded', function() {
    let postForm = document.querySelector('#editPostForm');
    if (postForm !== null) {
        postForm.addEventListener('submit', function(event) {
            let isValid = true;

            // Sélection des éléments du formulaire
            let titleElement = document.querySelector('#title');
            let postContentElement = document.querySelector('#post-content');
            let postStatusElement = document.querySelector('#status-post');
            let moderationReasonElement = document.querySelector('input[name="moderation-reason"]:checked');
            let detailRevisionElement = document.querySelector('#detail-revision');

            // Récupération des valeurs
            let title = titleElement ? titleElement.value.trim() : '';
            let postContent = postContentElement ? postContentElement.value.trim() : '';
            let statusId = postStatusElement ? postStatusElement.value : '';
            let moderationReasonId = moderationReasonElement ? moderationReasonElement.value : '';
            let detailRevision = detailRevisionElement ? detailRevisionElement.value.trim() : '';

            // Réinitialiser les messages d'erreurs et les styles
            document.getElementById('titleError').textContent = '';
            document.getElementById('postContentError').textContent = '';
            document.getElementById('moderationReasonError').textContent = '';
            document.getElementById('revisionDetailError').textContent = '';

            // Retirer la classe d'erreur de tous les champs
            titleElement.classList.remove('error-field');
            postContentElement.classList.remove('error-field');
            postStatusElement.classList.remove('error-field');
            if (detailRevisionElement) detailRevisionElement.classList.remove('error-field');

            // Validation du titre
            if (title.trim() === '') {
                if (document.getElementById('titleError')) {
                    document.getElementById('titleError').textContent = 'Title field is required !';
                    titleElement.classList.add('error-field');
                }
                isValid = false;
            }

            // Validation du contenu du message
            if (postContent.trim() === '') {
                postForm.querySelector('#postContentError').textContent = 'Content field is required!';
                postContentElement.classList.add('error-field');
                isValid = false;
            }

            // Validation du statut et de la raison de refus
            if (statusId === '5') {
                if (moderationReasonElement === null) {
                    if (postForm.querySelector('#moderationReasonError')) {
                        postForm.querySelector('#moderationReasonError').textContent = 'Please select a moderation reason!';
                    }
                    isValid = false;
                }
            }

            // Validation du champ "Autre" de la raison de refus
            if (moderationReasonId == '6' && detailRevision.trim() === '') {
                if (document.getElementById('revisionDetailError')) {
                    document.getElementById('revisionDetailError').textContent = 'Please specify a reason !';
                    detailRevisionElement.classList.add('error-field'); // Ajouter la classe d'erreur
                }
                isValid = false;

                detailRevisionElement.style.display = 'block';
            }
            // Empêcher la soumission du formulaire si les données ne sont pas valides
            if (!isValid) {
                event.preventDefault();
            }
        });

        // Écouteurs d'événements pour retirer les erreurs quand on clique sur les champs
        if (postForm.querySelector('#title')) {
            postForm.querySelector('#title').addEventListener('click', function() {
                this.classList.remove('error-field');
                postForm.querySelector('#titleError').textContent = '';
            });
        }

        if (postForm.querySelector('#post-content')) {
            postForm.querySelector('#post-content').addEventListener('click', function() {
                this.classList.remove('error-field');
                postForm.querySelector('#postContentError').textContent = '';
            });
        }

        if (postForm.querySelector('#status-post')) {
            postForm.querySelector('#status-post').addEventListener('click', function() {
                this.classList.remove('error-field');
                postForm.querySelector('#moderationReasonError').textContent = '';
            });
        }

        if (postForm.querySelector('.detail-revision')) {
            postForm.querySelector('#detail-revision').addEventListener('click', function() {
                this.classList.remove('error-field');
                postForm.querySelector('#revisionDetailError').textContent = '';
            });
        }

        let radioButtons = postForm.querySelectorAll('input[name="moderation-reason"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('click', function() {
                if (postForm.querySelector('#moderationReasonError')) {
                    postForm.querySelector('#moderationReasonError').textContent = '';
                }
            });
        });
    };
});
