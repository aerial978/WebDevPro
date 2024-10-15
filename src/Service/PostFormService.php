<?php

namespace src\Service;

use src\Core\Form;
use src\Service\StatusService;

class PostFormService
{
    private $statusService;

    public function __construct()
    {
        $this->statusService = new StatusService();
    }

    public function createPostService($categoriesOptions, $tagsOptions)
    {
        $statusOptions = $this->statusService->getStatusOptions();

        $createPostForm = new Form();

        $createPostForm->startForm('post', '', ['class' => '', 'enctype' => 'multipart/form-data', 'id' => 'createPostForm'])

            ->addLabelFor('title', 'Title', ['class' => 'mt-3'])
            ->addInput('text', 'title', ['class' => 'form-control', 'id' => 'title'])
            ->addError('titleError', ['class' => 'text-danger'])

            ->addLabelFor('postContent', 'Content', ['class' => 'mt-3'])
            ->addTextarea('postContent', '', ['class' => 'form-control', 'id' => 'editor'])
            ->addError('postContentError', ['class' => 'text-danger'])

            ->startDiv(['class' => 'row'])
                ->startDiv(['class' => 'col-md-6 mt-4'])
                    ->addSelect('category', $categoriesOptions, 'category', '', ['class' => 'form-select', 'id' => 'category'])
                    ->addError('categoryError', ['class' => 'text-danger'])
                ->endDiv()
                ->startDiv(['class' => 'col-md-6 mt-4'])
                    ->addSelect('postStatus', $statusOptions, 'status', '', ['class' => 'form-select', 'id' => 'postStatus'])
                    ->addError('postStatusError', ['class' => 'text-danger'])
                ->endDiv()
            ->endDiv()

            ->startDiv(['class' => 'row mt-3'])
            ->startDiv(['class' => 'col-md-12'])
            ->addLabelFor('tags', 'Tags')
                    ->addTagInputCreate('tags', $tagsOptions)
                ->endDiv()
            ->endDiv()

            ->startDiv(['class' => 'row'])
                ->startDiv(['class' => 'col-md-6 mt-4'])
                    ->addLabelFor('postImage', 'Image')
                    ->addInput('file', 'postImage', ['class' => 'form-control', 'id' => 'postImage'])
                    ->addError('postImageError', ['class' => 'text-danger'])
                ->endDiv()
                ->startDiv(['class' => 'col-md-6 mt-4'])
                    ->imagePreview()
                ->endDiv()
            ->endDiv()

            ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom p-2', 'id' => 'submitForm'])

            ->endForm();

        return $createPostForm;
    }

    public function editPostService($categoriesOptions, $post, $tagsOptions, $tagsForPost)
    {
        $statusOptions = $this->statusService->getStatusOptions();
        $selectedTagsIds = array_column($tagsForPost, 'name_tag');

        $editPostForm = new Form();

        $editPostForm->startForm('post', '', ['class' => '', 'enctype' => 'multipart/form-data', 'id' => 'editPostForm'])

        ->addLabelFor('title', 'Title', ['class' => 'mt-3'])
        ->addInput('text', 'title', ['class' => 'form-control', 'id' => 'title', 'value' => $post['title']])
        ->addError('titleError', ['class' => 'text-danger'])

        ->addLabelFor('postContent', 'Content', ['class' => 'mt-3'])
        ->addTextarea('postContent', $post['post_content'], ['class' => 'form-control', 'id' => 'editor'])
        ->addError('postContentError', ['class' => 'text-danger'])

        ->startDiv(['class' => 'row'])
            ->startDiv(['class' => 'col-md-6 mt-4'])
                ->addLabelFor('category', 'Category')
                ->addSelect('category', $categoriesOptions, 'category', $post['category_id'], ['class' => 'form-select', 'id' => 'category'])
                ->addError('categoryError', ['class' => 'text-danger'])
            ->endDiv()
            ->startDiv(['class' => 'col-md-6 mt-4'])
                ->addLabelFor('status', 'Status')
                ->addSelect('postStatus', $statusOptions, 'status', $post['status_id'], ['class' => 'form-select', 'id' => 'postStatus'])
                ->addError('postStatusError', ['class' => 'text-danger'])
            ->endDiv()
        ->endDiv()

        ->startDiv(['class' => 'row mt-3'])
            ->startDiv(['class' => 'col-md-12'])
                ->addLabelFor('tags', 'Tags')
                ->addTagInputEdit('tags', $tagsForPost, $tagsOptions, $selectedTagsIds)
            ->endDiv()
        ->endDiv()

        ->startDiv(['class' => 'row'])
            ->startDiv(['class' => 'col-md-6 mt-4'])
                ->addInput('file', 'postImages', ['class' => 'form-control', 'id' => 'postImage'])
                ->addError('postImageError', ['class' => 'text-danger'])
            ->endDiv()
            ->startDiv(['class' => 'col-md-6 mt-4'])
                ->editImage($post['post_image'], $post['post_image'])
            ->endDiv()
        ->endDiv()

        ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom p-2', 'id' => 'submit'])

        ->endForm();

        return $editPostForm;
    }
}
