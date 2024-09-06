<?php

namespace src\Core;

class Form
{
    private $formCode = '';

    /**
     * Génére le formulaire HTML
     * @return string
     */
    public function create()
    {
        return $this->formCode;
    }

    /**
     * Valide si tous les champs proposés sont remplis
     *
     * @param array $form Tableau issu du formulaire ($_POST, $_GET)
     * @param array $fields Tableau listant les champs obligatoires
     * @return void
     */
    public static function validate(array $form, array $fields)
    {
        // On   parcourt les champs
        foreach ($fields as $field) {
            // Si le champ est absent ou vide dans le formulaire
            if (!isset($form[$field]) || empty($form[$field])) {
                // On sort en retournant false
                return false;
            }
        }
        return true;
    }

    /**
     * Ajout les attributs envoyés à la balise
     *
     * @param array $attributes Tableau associatif ['class' =>
     * 'form-control', 'required' => true]
     * @return string Chaine de caractères générée
     */
    public function addAttributes(array $attributes): string
    {
        // On initialise une chaine de caractères
        $str = '';

        foreach ($attributes as $attribute => $value) {
            // Si l'attribut est un attribut court sans valeur
            if (is_int($attribute)) {
                $str .= " $value";
            } else {
                // Si l'attribut est fourni avec une valeur spécifique
                if ($value === true) {
                    $str .= " $attribute";
                } else {
                    $str .= " $attribute=\"$value\"";
                }
            }
        }
        return $str;
    }

    /**
     * Balise d'ouverture du formulaire
     * @param string $methode Méthode du formulaire (post ou get)
     * @param string $action Action du formulaire
     * @param array $attributes Attributs
     * @return Form
     */
    public function startForm(string $method = 'post', string $action = '#', array $attributes = []): self
    {
        // On crée la balise form
        $this->formCode .= "<form action='$action' method='$method'";

        // On ajoute les attributs éventuels
        $this->formCode .= $attributes ? $this->addAttributes($attributes) . '>' : '>';

        return $this;
    }

    /**
     * Balise de fermeture du formulaire
     *
     * @return Form
     */
    public function endForm(): self
    {
        $this->formCode .= '</form>';

        return $this;
    }

    public function addLabelFor(string $for, string $text, array $attributes = []): self
    {
        // On ouvre la balise
        $this->formCode .= "<label for='$for'";

        // On ajoute les attributs
        $this->formCode .= $attributes ? $this->addAttributes($attributes) : '';

        // On ajoute le texte
        $this->formCode .= ">$text</label>";

        return $this;
    }

    public function addInput(string $type, string $name, array $attributes = []): self
    {
        // On ouvre la balise
        $this->formCode .= "<input type='$type' name='$name'";

        // On ajoute les attributs
        $this->formCode .= $attributes ? $this->addAttributes($attributes) . '>' : '>';

        return $this;
    }

    public function addError($fieldError, $attributes = []): self
    {
        $this->formCode .= "<div id='$fieldError' ";

        $this->formCode .= $attributes ? $this->addAttributes($attributes) : '';

        $this->formCode .= '></div>';

        return $this;
    }

    public function addTextarea(string $name, string $value = '', array $attributes = []): self
    {
        // On ouvre la balise
        $this->formCode .= "<textarea name='$name'";

        // On ajoute les attributs
        $this->formCode .= $attributes ? $this->addAttributes($attributes) : '';

        // On ajoute le texte
        $this->formCode .= ">$value</textarea>";

        return $this;
    }

    public function addSelect(string $name, array $options, string $defaultText, $selectedValue = '', array $attributes = []): self
    {
        // On crée le select
        $this->formCode .= "<select name='$name'";

        // On ajoute les attributs
        $this->formCode .= $attributes ? $this->addAttributes($attributes) . '>' : '>';

        $this->formCode .= "<option value='' selected disabled>Select a $defaultText</option>";

        // On ajoute les options
        foreach ($options as $value => $text) {
            $selected = $value == $selectedValue ? 'selected' : '';
            $this->formCode .= "<option value=\"$value\" $selected>$text</option>";
        }

        // On ferme le select
        $this->formCode .= "</select>";

        return $this;
    }

    public function addTagInputCreate(string $name, array $tagsOptions = []): self
    {
        $this->formCode .= "<input type='hidden' name='$name' id='hidden-tag' value=''>
        <div class='tag-input-container' id='tag-input-container'>
            <div id='tag-container' class='tag-container'>";

        $this->formCode .= "<input id='add-newtag' type='text' placeholder='Enter a tag' style='border: none; border-radius: 3px; background-color: #F2F2F2;'>";

        // Ajout des options des tags existants
        $this->formCode .= "</div>
                <select id='all-tags' hidden class='form-select tag-select'>
                    <option value='' disabled selected>Select a tag</option>";

        foreach ($tagsOptions as $tag) {
            $this->formCode .= "<option value='{$tag}'>{$tag}</option>";
        }

        $this->formCode .= "</select>
        </div><div id='tagError' class='text-danger'></div>";

        return $this;
    }

    public function addTagInputEdit(string $name, array $tagsForPost = [], array $tagsOptions = [], array $selectedTagsIds = []): self
    {
        $this->formCode .= "<input type='hidden' name='$name' id='hidden-tag' value='" . implode(',', $selectedTagsIds) . "'>
        <div class='tag-input-container' id='tag-input-container'>
            <div id='tag-container' class='tag-container'>";

        // Ajout des tags déjà sélectionnés
        foreach ($tagsForPost as $tag) {
            $this->formCode .= "<span class='tag-label' data-tag='{$tag['name_tag']}'>{$tag['name_tag']} <span class='remove-tag'>x</span></span>";
        }

        $this->formCode .= "<input id='add-newtag' type='text' placeholder='Enter a tag' style='border: none; border-radius: 3px; background-color: #F2F2F2;'>";

        // Ajout des options des tags existants
        $this->formCode .= "</div>
                <select id='all-tags' hidden class='form-select tag-select'>
                    <option value='' disabled selected>Select a tag</option>";

        foreach ($tagsOptions as $tag) {
            $this->formCode .= "<option value='{$tag}'>{$tag}</option>";
        }

        $this->formCode .= "</select>
        </div><div id='tagError' class='text-danger'></div>";

        return $this;
    }


    public function addButton(string $text, string $type, string $name, array $attributes = []): self
    {
        // On ouvre le bouton
        $this->formCode .= "<br><button type='$type' name='$name'";

        // On ajoute les attributs
        $this->formCode .= $attributes ? $this->addAttributes($attributes) : '';

        // On ajoute le texte et on ferme
        $this->formCode .= ">$text</button>";

        return $this;
    }

    public function imagePreview(): self
    {
        $this->formCode .= "<div id='imagePreview'></div>";

        return $this;
    }

    public function editImage(string $name, string $file): self
    {
        $this->formCode .= "<div id='existingFileInfo'>";

        $this->formCode .= "<img id='existingImagePreview' src='/webdevpro/public/upload/$name' alt='edit-image' width='150px' height='100px'/>";

        $this->formCode .= "<p>$file</p>";

        $this->formCode .= "</div>";

        return $this;
    }

    public function startDiv(array $attributes = []): self
    {
        // On ouvre la balise div
        $this->formCode .= "<div";

        // On ajoute les attributs éventuels
        $this->formCode .= $attributes ? $this->addAttributes($attributes) : '';

        // On ferme la balise div
        $this->formCode .= '>';

        return $this;
    }

    public function endDiv(): self
    {
        $this->formCode .= '</div>';
        return $this;
    }
}
