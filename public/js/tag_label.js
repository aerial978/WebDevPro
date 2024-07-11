document.addEventListener('DOMContentLoaded', function() {
    // Récupére le champ caché pour stocker les tags sélectionnés
    const hiddenTagInput = document.getElementById('hidden-tag');
    // Récupére le conteneur principal des éléments de tags
    const tagInputContainer = document.getElementById('tag-input-container');
    // Récupére le conteneur des tags sélectionnés
    const tagContainer = document.getElementById('tag-container');
    // Récupére le champ de sélection pour tous les tags existants
    const allTagsSelect = document.getElementById('all-tags');
    // Limite maximale de tags pouvant être sélectionnés
    const maxTags = 5;
    // Récupére le champ d'ajout de nouveau tag
    const addNewTag = document.getElementById('add-newtag');
    // Récupére l'élément pour afficher les erreurs liées aux tags
    const tagError = document.getElementById('tagError');
    // Obtenir la liste des options de tags existants sous forme de tableau
    let tagsOptions = Array.from(allTagsSelect.options).map(option => option.text);

    // Met à jour la valeur du champ caché en fonction des tags sélectionnés
    function updateHiddenInput() {
        const tags = Array.from(tagContainer.querySelectorAll('.tag-label')).map(tag => tag.dataset.tag);
        hiddenTagInput.value = tags.join(',');
    }

    // Crée un nouveau label tag et ajouter un gestionnaire d'événement pour le bouton de suppression
    function createTagLabel(tagName) {
        const span = document.createElement('span');
        span.classList.add('tag-label');
        span.dataset.tag = tagName;
        span.innerHTML = `${tagName} <span class="remove-tag">x</span>`;
        span.querySelector('.remove-tag').addEventListener('click', function() {
            span.remove();
            updateHiddenInput();
        });
        return span;
    }

    // Vérifie si la limite maximale de tags est atteinte
    function checkTagLimit() {
        const existingTags = tagContainer.querySelectorAll('.tag-label');
        return existingTags.length >= maxTags;
    }

    // Suggestion automatique des tags existants lors de la saisie
    addNewTag.addEventListener('input', function(event) {
        const inputValue = addNewTag.value.toLowerCase();
        const matchingTags = tagsOptions.filter(function(tag){
            if(tag.toLowerCase().startsWith(inputValue) && tag.toLowerCase() != 'select a tag'){
                return true;
            }
            return false;
        });

        if (matchingTags.length > 0 && inputValue !== "") {
            const currentPos = addNewTag.selectionStart;
            const suggestion = matchingTags[0];
            addNewTag.value = inputValue + suggestion.slice(inputValue.length);
            addNewTag.setSelectionRange(currentPos, currentPos);
        }
    });

    // Affiche la liste déroulante des tags existants lorsque le conteneur de labels tags est cliqué
    tagInputContainer.addEventListener('click', function(event) {
        allTagsSelect.style.display = 'block';
    });

    // Ajoute un tag de la liste des tags existants au conteneur de labels tags
    allTagsSelect.addEventListener('change', function() {
        // Alerte si la limite de tags est atteinte
        if (checkTagLimit()) {
            alert('You can only select up to 5 tags.');
            allTagsSelect.style.display = 'none';
            allTagsSelect.selectedIndex = 0;
            return;
        }
        
        // Obtient l'option tag actuellement sélectionnée dans la liste déroulante
        const selectedOption = allTagsSelect.options[allTagsSelect.selectedIndex];
        // Obtient le nom du tag sélectionné
        const tagName = selectedOption.text;
        // Obtient la liste des tags existants dans le conteneur
        const existingTags = Array.from(tagContainer.querySelectorAll('.tag-label')).map(tag => tag.dataset.tag);
        // Si le tag sélectionné n'est pas déjà présent dans le conteneur de labels tags
        if (!existingTags.includes(tagName)) {
            const newTagLabel = createTagLabel(tagName);
            tagContainer.prepend(newTagLabel);
            updateHiddenInput();
        }
        allTagsSelect.style.display = 'none';
        allTagsSelect.selectedIndex = 0;
    });

    // Ajout du tag lors de la saisie et gestion des suggestions automatiques
    addNewTag.addEventListener('input', function(event) {
        const inputValue = addNewTag.value.toLowerCase();
        const matchingTags = tagsOptions.filter(tag => tag.toLowerCase().startsWith(inputValue));

        if (matchingTags.length > 0 && inputValue !== "") {
            const currentPos = addNewTag.selectionStart;
            const suggestion = matchingTags[0];
            addNewTag.value = inputValue + suggestion.slice(inputValue.length);
            addNewTag.setSelectionRange(currentPos, inputValue.length);
        }
    });

    // Gere la touche Enter et Backspace lors de l'ajout d'un nouveau tag
    addNewTag.addEventListener('keydown', function(event) {
        const currentPos = addNewTag.selectionStart;
        const selectionLength = addNewTag.selectionEnd - addNewTag.selectionStart;

        if (event.code === 'Enter') {
            event.preventDefault(); // Empêche l'ajout d'un espace ou d'une nouvelle ligne

            if (checkTagLimit()) {
                alert('You can only select up to 5 tags.');
                return;
            }

            const tagName = addNewTag.value.trim();
            const existingTags = Array.from(tagContainer.querySelectorAll('.tag-label')).map(tag => tag.dataset.tag);

            if (!existingTags.includes(tagName) && tagName !== '') {
                const newTagLabel = createTagLabel(tagName);
                tagContainer.prepend(newTagLabel);
                updateHiddenInput();
            }
            addNewTag.value = '';
        } else if (event.code === 'Backspace') {
            event.preventDefault(); // Empêche le comportement par défaut du retour arrière

            if (selectionLength > 0) {
                // Si du texte est sélectionné (suggéré), effacez-le
                const newInputValue = addNewTag.value.slice(0, currentPos) + addNewTag.value.slice(addNewTag.selectionEnd);
                addNewTag.value = newInputValue;
                addNewTag.setSelectionRange(currentPos, currentPos);
            } else if (currentPos > 0) {
                // Sinon, effacez un caractère à gauche du curseur
                const newInputValue = addNewTag.value.slice(0, currentPos - 1) + addNewTag.value.slice(currentPos);
                addNewTag.value = newInputValue;
                addNewTag.setSelectionRange(currentPos - 1, currentPos - 1);
            }
        }
    });

    // Supprime un label tag dans le conteneur
    tagContainer.querySelectorAll('.remove-tag').forEach(function(removeButton) {
        removeButton.addEventListener('click', function() {
            removeButton.parentElement.remove();
            updateHiddenInput();
        });
    });

    // Cache la liste déroulante si un clic se produit en dehors du conteneur de tags
    document.addEventListener('click', function(event) {
        if (!tagInputContainer.contains(event.target)) {
            allTagsSelect.style.display = 'none';
        }
    });

    // Ajoute un gestionnaire d'événement pour la soumission du formulaire
    document.addEventListener('submit', function(event) {
        if (hiddenTagInput.value.trim() === '') {
            event.preventDefault(); // Empêche la soumission du formulaire
            tagError.textContent = 'Please select one tag at least & press the enter key !';
        } else {
            tagError.textContent = ''; // Réinitialise le message d'erreur
        }
    });
});