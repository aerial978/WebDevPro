document.addEventListener('DOMContentLoaded', function() {
    let postCreateForm = document.getElementById('postCreateForm');

    if (postCreateForm !== null) {
        postCreateForm.addEventListener('submit', function(event) {
            let isValid = true;
            let title = document.querySelector('#title').value;
            let introduction = document.querySelector('#introduction').value;
            let postContent = document.querySelector('#postContent').value;
            let category = document.querySelector('#category').value;
            let postStatus = document.querySelector('#postStatus').value;
            let postImage = document.querySelector('#postImage').value;

            // Reset error messages
            document.getElementById('titleError').textContent = '';
            document.getElementById('introductionError').textContent = '';
            document.getElementById('postContentError').textContent = '';
            document.getElementById('categoryError').textContent = '';
            document.getElementById('postStatusError').textContent = '';
            document.getElementById('postImageError').textContent = '';

            // Validation du titre
            if (title.trim() === '') {
                document.getElementById('titleError').textContent = 'The title field is required !';
                isValid = false;
            }

            // Validation de l'introduction (exemple)
            if (introduction.trim() === '') {
                document.getElementById('introductionError').textContent = 'The introduction field is required !';
                isValid = false;
            }

            // Validation du contenu du message (exemple)
            if (postContent.trim() === '') {
                document.getElementById('postContentError').textContent = 'The content field is required !';
                isValid = false;
            }

            // Validation de la catégorie
            if (category === '') {
                document.getElementById('categoryError').textContent = 'Please select a category !';
                isValid = false;
            }

            // Validation du statut du message
            if (postStatus === '') {
                document.getElementById('postStatusError').textContent = 'Please select a status !';
                isValid = false;
            }

            // Validation de l'image (exemple)
            if (postImage.trim() === '') {
                document.getElementById('postImageError').textContent = 'Please upload an image !';
                isValid = false;
            }

            // Empêcher la soumission du formulaire si les données ne sont pas valides
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});