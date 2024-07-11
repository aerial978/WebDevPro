<?php

namespace src\controller;

use src\Models\TagModel;
use src\Constants\ErrorMessage;
use src\Service\TagFormService;

class TagController extends BaseController
{
    /**
     * Displays the list of tags with the count of associated posts in the admin interface.
     *
     * Retrieves all tags along with the count of associated posts from the database
     * and displays them in the corresponding view.
     */
    public function index()
    {
        $tagsModel = new TagModel();

        $indexTags = $tagsModel->findTagList();

        $this->twig->display('admin/tags/index.html.twig', compact('indexTags'));
    }

    /**
     * Manages the creation of a new tag in the admin interface.
     *
     * Processes the form submission, validates input, checks for uniqueness,
     * and creates a new tag in the database if all conditions are met.
     * In case of error, displays error messages or redirects to an error page.
     *
     * @throws \Exception On error, redirects to error page 500.
     *
     * @return void
     */
    public function create()
    {
        $errors = [];

        try {
            if (isset($_POST['submit'])) {
                $nameTag = trim(htmlspecialchars($_POST['nameTag'], ENT_QUOTES, 'UTF-8'));

                if (empty($nameTag)) {
                    $errors['nameTag'] = ErrorMessage::NAMETAG_INVALID;
                } else {
                    $tagsModel = new TagModel();
                    $existingTags = $tagsModel->findAll();

                    foreach ($existingTags as $existingTag) {
                        if (strtolower($existingTag->tag_name) === strtolower($nameTag)) {
                            $errors['uniqueNameTag'] = ErrorMessage::UNIQUENAMETAG_INVALID;
                            break;
                        }
                    }
                }

                if (empty($errors)) {

                    $tags = new TagModel();

                    $tags->setTagName($nameTag);

                    $tags->create();
                    header('Location: index');
                }
            }

            $tagFormService = new TagFormService();
            $createTagForm = $tagFormService->createTagService();

            $this->twig->display('admin/tags/create.html.twig', [
                'errors' => $errors,
                'createTagForm' => $createTagForm->create()
            ]);
        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }

    /**
     * Manages the editing of an existing tag in the admin interface.
     *
     * Processes the form submission, validates input, and updates the tag
     * in the database if all conditions are met. In case of error, displays
     * error messages or redirects to an error page.
     *
     * @param int $id The ID of the tag to edit.
     *
     * @throws \Exception On error, redirects to error page 500.
     *
     * @return void
     */
    public function edit(int $id)
    {
        $errors = [];

        try {

            $tagsModel = new TagModel();

            $tag = $tagsModel->find($id);

            if (isset($_POST['submit'])) {
                $nameTag = trim(htmlspecialchars($_POST['nameTag'], ENT_QUOTES, 'UTF-8'));

                if (empty($nameTag)) {
                    $errors['nameTag'] = ErrorMessage::NAMETAG_INVALID;
                } else {
                    $tagsModel = new TagModel();
                    $existingTags = $tagsModel->findAll();

                    foreach ($existingTags as $existingTag) {
                        if (strtolower($existingTag->tag_name) === strtolower($nameTag)) {
                            $errors['uniqueNameTag'] = ErrorMessage::UNIQUENAMETAG_INVALID;
                            break;
                        }
                    }
                }

                if (empty($errors)) {

                    $tags = new TagModel();

                    $tags->setTagName($nameTag);

                    $tags->update($id);
                    header('Location: ../index');
                }

            }

            $tagFormService = new TagFormService();
            $editTagForm = $tagFormService->editTagService($tag);

            $this->twig->display('admin/tags/edit.html.twig', [
                'errors' => $errors,
                'editTagForm' => $editTagForm->create()
            ]);

        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }

    /**
     * Deletes a tag in the admin interface.
     *
     * Finds the tag by ID, checks if it exists, and deletes it from the database.
     * If the tag doesn't exist, redirects to a 404 error page. In case of an error,
     * redirects to an error page 500.
     *
     * @param int $id The ID of the tag to delete.
     *
     * @throws \Exception On error, redirects to error page 500.
     *
     * @return void
     */
    public function delete(int $id)
    {
        try {
            $tagsModel = new TagModel();

            $tag = $tagsModel->find($id);

            if (!$tag) {
                // Le tag n'existe pas, vous pouvez gérer cette situation
                // Redirection vers une page d'erreur ou autre
                header('Location: /error-page-404');
                exit;
            }

            $tagsModel->delete($id);

            header('Location: ../index');

        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }
}
