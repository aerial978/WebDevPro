document.addEventListener('DOMContentLoaded', function() {
    let tagForm = document.querySelector('#createTagForm');

    if (tagForm !== null) {
        tagForm.addEventListener('submit', function(event) {
            let isValid = true;
            let nameTag = document.querySelector('#name_tag').value;
            
            // Reset error messages
            document.getElementById('nameTagError').textContent = '';

            // Validation du titre
            if (nameTag.trim() === '') {
                document.getElementById('nameTagError').textContent = 'Tag name field is required !';
                isValid = false;
            }

            // Empêcher la soumission du formulaire si les données ne sont pas valides
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});
