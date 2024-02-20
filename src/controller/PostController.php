<?php

namespace src\controller;

use src\Models\PostModel;
use src\Service\FormService;

class PostController extends BaseController
{
    /**
     * Gère la création d'un nouveau post.
     * Effectue la validation des données et enregistre le post s'il n'y a pas d'erreurs.
     * En cas d'erreur, affiche les messages ou redirige vers une page d'erreur.
     *
     * @throws \Exception En cas d'erreur, redirige vers la page d'erreur 500.
     *
     * @return void
     */
    public function create()
    {
        $errors = [];

        try {
            if (isset($_POST['submit'])) {
                $title = trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
                $introduction = trim(htmlspecialchars($_POST['introduction'], ENT_QUOTES, 'UTF-8'));
                $postContent = trim(htmlspecialchars($_POST['postContent'], ENT_QUOTES, 'UTF-8'));
                $category = isset($_POST['category']) ? $_POST['category'] : '';
                $postStatus = isset($_POST['postStatus']) ? $_POST['postStatus'] : '';
                $postImage = $_FILES['postImage'];

                if (empty($title)) {
                    $errors['title'] = "Title vide";
                }

                if (empty($introduction)) {
                    $errors['introduction'] = "introduction vide";
                }

                if (empty($postContent)) {
                    $errors['content'] = "content vide";
                }

                if (empty($category)) {
                    $errors['category'] = "category vide";
                }

                if (empty($postStatus)) {
                    $errors['postStatus'] = "postStatus vide";
                }

                if (empty($postImage['name'])) {
                    $errors['postImage'] = 'Select an image';
                } elseif (isset($_FILES['postImage']) && $_FILES['postImage']['size'] > 0) {
                    $error = $_FILES['postImage']['error'];
                    if ($error > 0) {
                        $errors['transfert'] = 'There was a problem with the transfer!';
                    }

                    $image_tmp_name = $_FILES['postImage']['tmp_name'];
                    $image_size = $_FILES['postImage']['size'];
                    $upload_path = "public/upload/";

                    $maxsize = 2 * 1024 * 1024;

                    if ($image_size >= $maxsize) {
                        $errors['size'] = 'File too large (2 Mo max) !';
                    }

                    $image_ext = pathinfo($postImage['name'], PATHINFO_EXTENSION);
                    $image_ext_min = strtolower($image_ext);
                    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');

                    if (!in_array($image_ext_min, $allowed_ext)) {
                        $errors['extension'] = 'file extension is not allowed (jpg, jpeg, png only) !';
                    }
                }

                if (empty($errors)) {
                    move_uploaded_file($image_tmp_name, $upload_path . $postImage['name']);
                    $posts = new PostModel();

                    $posts->setTitle($title)
                        ->setIntroduction($introduction)
                        ->setPostContent($postContent)
                        ->setCategory($category)
                        ->setPostStatus($postStatus)
                        ->setPostImage($postImage['name'])
                        ->setUser_id($_SESSION['user']['id']);

                    $posts->create();
                }
            }

            $formService = new FormService();
            $createPostForm = $formService->createPostService();

            $this->twig->display('admin/posts/create.html.twig', [
                'errors' => $errors,
                'createPostForm' => $createPostForm->create()
            ]);
        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }
}
