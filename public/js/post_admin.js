document.addEventListener('DOMContentLoaded', function() {
    let postForm = document.querySelector('#createPostForm');

    if (postForm !== null) {
        postForm.addEventListener('submit', function(event) {
            let isValid = true;

            let titleElement =  document.querySelector('#title');
            let postContentElement =  document.querySelector('#content-post');
            let categoryElement = document.querySelector('#category');
            let postImageElement = document.querySelector('#image-post');
            
            let title = titleElement ? titleElement.value.trim() : '';
            let postContent = postContentElement ? postContentElement.value.trim() : '';
            let category = categoryElement ? categoryElement.value : '';
            let postImage = postImageElement ? postImageElement.value : '';

            // Vérifiez que les éléments de message d'erreur existent avant de les réinitialiser
            if (document.getElementById('titleError')) document.getElementById('titleError').textContent = '';
            if (document.getElementById('postContentError')) document.getElementById('postContentError').textContent = '';
            if (document.getElementById('categoryError')) document.getElementById('categoryError').textContent = '';
            if (document.getElementById('postImageError')) document.getElementById('postImageError').textContent = '';

            titleElement.classList.remove('error-field');
            postContentElement.classList.remove('error-field');
            categoryElement.classList.remove('error-field');
            postImageElement.classList.remove('error-field');

            // Validation du titre
            if (title === '') {
                if (document.getElementById('titleError')) {
                    document.getElementById('titleError').textContent = 'Title field is required !';
                    titleElement.classList.add('error-field');
                }
                isValid = false;
            }

            // Validation du contenu du message
            if (postContent === '') {
                if (document.getElementById('postContentError')) {
                    document.getElementById('postContentError').textContent = 'Content field is required !';
                    postContentElement.classList.add('error-field');
                }
                isValid = false;
            }

            // Validation de la catégorie
            if (category === '') {
                if (document.getElementById('categoryError')) {
                    document.getElementById('categoryError').textContent = 'Please select a category !';
                    categoryElement.classList.add('error-field');
                }
                isValid = false;
            }

            // Validation de l'image (uniquement si un fichier est sélectionné dans le formulaire create post)
            if (postForm.id === 'createPostForm' && (postImage === '' || postImage === null)) {
                if (document.getElementById('postImageError')) {
                    document.getElementById('postImageError').textContent = 'Please upload an image !';
                    postImageElement.classList.add('error-field');
                }
                isValid = false;
            }

            // Empêcher la soumission du formulaire si les données ne sont pas valides
            if (!isValid) {
                event.preventDefault();
            }
        });

        if (postForm.querySelector('#title')) {
            postForm.querySelector('#title').addEventListener('click', function() {
                this.classList.remove('error-field');
                postForm.querySelector('#titleError').textContent = '';
            });
        }

        if (postForm.querySelector('#content-post')) {
            postForm.querySelector('#content-post').addEventListener('click', function() {
                this.classList.remove('error-field');
                postForm.querySelector('#postContentError').textContent = '';
            });
        }

        if (postForm.querySelector('#category')) {
            postForm.querySelector('#category').addEventListener('click', function() {
                this.classList.remove('error-field');
                postForm.querySelector('#categoryError').textContent = '';
            });
        }

        if (postForm.querySelector('#image-post')) {
            postForm.querySelector('#image-post').addEventListener('click', function() {
                this.classList.remove('error-field');
                postForm.querySelector('#postImageError').textContent = '';
            });
        }
    }
});
