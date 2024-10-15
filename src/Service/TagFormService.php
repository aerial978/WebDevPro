<?php

namespace src\Service;

use src\Core\Form;

class TagFormService
{
    public function createTagService()
    {
        $createTagForm = new Form();

        $createTagForm->startForm('post', '', ['class' => '', 'id' => 'createTagForm'])
        ->addLabelFor('nameTag', 'Name', ['class' => 'mt-3'])
        ->addInput('text', 'nameTag', ['class' => 'form-control', 'id' => 'name_tag'])
        ->addError('nameTagError', ['class' => 'text-danger'])

        ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom p-2', 'id' => 'submit'])

        ->endForm();

        return $createTagForm;
    }

    public function editTagService($tag)
    {
        $editTagForm = new Form();

        $editTagForm->startForm('post', '', ['class' => '', 'id' => 'editTagForm'])
        ->addLabelFor('nameTag', 'Name', ['class' => 'mt-3'])
        ->addInput('text', 'nameTag', ['class' => 'form-control', 'id' => 'name_tag', 'value' => $tag->name_tag])
        ->addError('nameTagError', ['class' => 'text-danger'])

        ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom p-2', 'id' => 'submit'])

        ->endForm();

        return $editTagForm;
    }
}
