<?php

namespace src\Service;

use src\Core\Form;

class FormService
{
    public function RegistrationService()
    {
        $registrationForm = new Form();

        $registrationForm->startForm('post', 'registration', ['id' => 'registrationForm'])

            ->addLabelFor('email', 'Email', ['class' => 'mt-3'])
            ->addInput('email', 'email', ['class' => 'form-control', 'id' => 'email'])
            ->addError('emailError', ['class' => 'text-danger'])

            ->addLabelFor('password', 'Password', ['class' => 'mt-3'])
            ->addInput('password', 'password', ['class' => 'form-control', 'id' => 'password'])
            ->addError('passwordError', ['class' => 'text-danger'])

            ->addLabelFor('password', 'Confirm password', ['class' => 'mt-3'])
            ->addInput('password', 'confirm_password', ['class' => 'form-control', 'id' => 'confirm_password'])
            ->addError('confirmPasswordError', ['class' => 'text-danger'])

            ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom btn-submit w-100 p-3', 'id' => 'submit'])
            ->endForm();

        return $registrationForm;
    }

    public function LoginService()
    {
        $loginForm = new Form();

        $loginForm->startForm('post', 'login', ['id' => 'loginForm'])

            ->addLabelFor('email', 'Email', ['class' => 'mt-3'])
            ->addInput('email', 'email', ['class' => 'form-control', 'id' => 'email'])

            ->addLabelFor('password', 'Password', ['class' => 'mt-3'])
            ->addInput('password', 'password', ['class' => 'form-control', 'id' => 'password'])

            ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom btn-submit w-100 p-3', 'id' => 'submit'])
            ->endForm();

        return $loginForm;
    }

    public function createPostService()
    {
        $createPostForm = new Form();

        $createPostForm->startForm('post', '', ['class' => '', 'enctype' => 'multipart/form-data', 'id' => 'createPostForm'])

            ->addLabelFor('title', 'Title', ['class' => 'mt-3'])
            ->addInput('text', 'title', ['class' => 'form-control', 'id' => 'title'])
            ->addError('titleError', ['class' => 'text-danger'])

            ->addLabelFor('introduction', 'Introduction', ['class' => 'mt-3'])
            ->addInput('text', 'introduction', ['class' => 'form-control', 'id' => 'introduction'])
            ->addError('introductionError', ['class' => 'text-danger'])

            ->addLabelFor('postContent', 'Content', ['class' => 'mt-3'])
            ->addTextarea('postContent', '', ['class' => 'form-control', 'id' => 'postContent'])
            ->addError('postContentError', ['class' => 'text-danger'])

            ->startDiv(['class' => 'row'])
                ->startDiv(['class' => 'col-md-6 mt-4'])
                ->addSelect('category', ['Html' => 'Html', 'CSS' => 'CSS', 'Javascript' => 'Javascript'], 'category', '', ['class' => 'form-select', 'id' => 'category'])
                ->addError('categoryError', ['class' => 'text-danger'])
                ->endDiv()
                ->startDiv(['class' => 'col-md-6 mt-4'])
                    ->addSelect('postStatus', ['Draft' => 'Draft', 'Waiting' => 'Waiting', 'Published' => 'Published', 'Archived' => 'Archived'], 'status', '', ['class' => 'form-select', 'id' => 'postStatus'])
                    ->addError('postStatusError', ['class' => 'text-danger'])
                ->endDiv()
            ->endDiv()
            ->startDiv(['class' => 'row'])
                ->startDiv(['class' => 'col-md-6 mt-4'])
                    ->addInput('file', 'postImage', ['class' => 'form-control', 'id' => 'postImage'])
                    ->addError('postImageError', ['class' => 'text-danger'])
                ->endDiv()
                ->startDiv(['class' => 'col-md-6 mt-4'])
                    ->imagePreview()
                ->endDiv()
            ->endDiv()
            ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom p-2', 'id' => 'submit'])
            ->endForm();

        return $createPostForm;
    }

    public function editPostService($post)
    {
        $editPostForm = new Form();

        $editPostForm->startForm('post', '', ['class' => '', 'enctype' => 'multipart/form-data', 'id' => 'editPostForm'])

        ->addLabelFor('title', 'Title', ['class' => 'mt-3'])
        ->addInput('text', 'title', ['class' => 'form-control', 'id' => 'title', 'value' => $post->title])
        ->addError('titleError', ['class' => 'text-danger'])

        ->addLabelFor('introduction', 'Introduction', ['class' => 'mt-3'])
        ->addInput('text', 'introduction', ['class' => 'form-control', 'id' => 'introduction', 'value' => $post->introduction])
        ->addError('introductionError', ['class' => 'text-danger'])

        ->addLabelFor('postContent', 'Content', ['class' => 'mt-3'])
        ->addTextarea('postContent', $post->post_content, ['class' => 'form-control', 'id' => 'postContent'])
        ->addError('postContentError', ['class' => 'text-danger'])

        ->startDiv(['class' => 'row'])
            ->startDiv(['class' => 'col-md-6 mt-4'])
            ->addSelect('category', ['Html' => 'Html', 'CSS' => 'CSS', 'Javascript' => 'Javascript'], 'category', $post->category, ['class' => 'form-select', 'id' => 'category'])
            ->addError('categoryError', ['class' => 'text-danger'])
            ->endDiv()
            ->startDiv(['class' => 'col-md-6 mt-4'])
                ->addSelect('postStatus', ['Draft' => 'Draft', 'Waiting' => 'Waiting', 'Published' => 'Published', 'Archived' => 'Archived'], 'status', $post->post_status, ['class' => 'form-select', 'id' => 'postStatus'])
                ->addError('postStatusError', ['class' => 'text-danger'])
            ->endDiv()
        ->endDiv()
        ->startDiv(['class' => 'row'])
            ->startDiv(['class' => 'col-md-6 mt-4'])
                ->addInput('file', 'postImages', ['class' => 'form-control', 'id' => 'postImage'])
                ->addError('postImageError', ['class' => 'text-danger'])
            ->endDiv()
            ->startDiv(['class' => 'col-md-6 mt-4'])
                ->editImage($post->post_image, $post->post_image)
            ->endDiv()
        ->endDiv()
        ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom p-2', 'id' => 'submit'])
        ->endForm();

        return $editPostForm;
    }
}
