<?php

namespace src\Service;

use src\Core\Form;

class CategoryFormService
{
    public function createCategoryService()
    {
        $createCategoryForm = new Form();

        $createCategoryForm->startForm('post', '', ['class' => '', 'id' => 'createCategoryForm'])
        ->addLabelFor('nameCategory', 'Name', ['class' => 'mt-3'])
        ->addInput('text', 'nameCategory', ['class' => 'form-control', 'id' => 'name_category'])
        ->addError('nameCategoryError', ['class' => 'text-danger'])

        ->addLabelFor('description', 'Description', ['class' => 'mt-3'])
        ->addTextarea('descriptionCategory', '', ['class' => 'form-control', 'id' => 'editor'])
        ->addError('descriptionCategoryError', ['class' => 'text-danger'])

        ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom p-2', 'id' => 'submit'])

        ->endForm();

        return $createCategoryForm;
    }

    public function editCategoryService($category)
    {
        $editCategoryForm = new Form();

        $editCategoryForm->startForm('post', '', ['class' => '', 'id' => 'editCategoryForm'])
        ->addLabelFor('nameCategory', 'Name', ['class' => 'mt-3'])
        ->addInput('text', 'nameCategory', ['class' => 'form-control', 'id' => 'name_category', 'value' => $category->name_category])
        ->addError('nameCategoryError', ['class' => 'text-danger'])

        ->addLabelFor('description', 'Description', ['class' => 'mt-3'])
        ->addTextarea('descriptionCategory', $category->description_category, ['class' => 'form-control', 'id' => 'editor'])
        ->addError('descriptionCategoryError', ['class' => 'text-danger'])

        ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom p-2', 'id' => 'submit'])

        ->endForm();

        return $editCategoryForm;
    }
}
