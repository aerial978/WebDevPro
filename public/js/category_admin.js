document.addEventListener('DOMContentLoaded', function() {
    let categoryForm = document.querySelector('#createCategoryForm, #editCategoryForm');

    if (categoryForm !== null) {
        categoryForm.addEventListener('submit', function(event) {
            let isValid = true;
            let nameCategory = document.querySelector('#name_category').value;
            let descriptionCategory = document.querySelector('#editor').value;

            // Reset error messages
            document.getElementById('nameCategoryError').textContent = '';
            document.getElementById('descriptionCategoryError').textContent = '';

            // Validation du titre
            if (nameCategory.trim() === '') {
                document.getElementById('nameCategoryError').textContent = 'Category name field is required !';
                isValid = false;
            }

            // Validation de l'introduction
            if (descriptionCategory.trim() === '') {
                document.getElementById('descriptionCategoryError').textContent = 'Category description field is required !';
                isValid = false;
            }

            // Empêcher la soumission du formulaire si les données ne sont pas valides
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});
