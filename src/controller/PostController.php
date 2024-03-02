<?php

namespace src\controller;

use src\Core\Form;
use src\Models\PostModel;
use src\Service\FormService;
use src\Constants\ErrorMessage;

class PostController extends BaseController
{
    public function index()
    {
        $postsModel = new PostModel();

        $indexPosts = $postsModel->findAll();

        $this->twig->display('admin/posts/index.html.twig', compact('indexPosts'));
    }

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
                    $errors['title'] = ErrorMessage::TITLE_INVALID;
                }

                if (empty($introduction)) {
                    $errors['introduction'] = ErrorMessage::INTRODUCTION_INVALID;
                }

                if (empty($postContent)) {
                    $errors['content'] = ErrorMessage::CONTENT_INVALID;
                }

                if (empty($category)) {
                    $errors['category'] = ErrorMessage::CATEGORY_INVALID;
                }

                if (empty($postStatus)) {
                    $errors['postStatus'] = ErrorMessage::STATUS_INVALID;
                }

                if (empty($postImage['name'])) {
                    $errors['postImage'] = ErrorMessage::IMAGEPOST_INVALID;
                } elseif (isset($_FILES['postImage']) && $_FILES['postImage']['size'] > 0) {
                    $error = $_FILES['postImage']['error'];
                    if ($error > 0) {
                        $errors['transfert'] = ErrorMessage::TRANSFERT_INVALID;
                    }

                    $image_tmp_name = $_FILES['postImage']['tmp_name'];
                    $image_size = $_FILES['postImage']['size'];
                    $upload_path = "public/upload/";

                    $maxsize = 2 * 1024 * 1024;

                    if ($image_size >= $maxsize) {
                        $errors['size'] = ErrorMessage::SIZE_INVALID;
                    }

                    $image_ext = pathinfo($postImage['name'], PATHINFO_EXTENSION);
                    $image_ext_min = strtolower($image_ext);
                    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');

                    if (!in_array($image_ext_min, $allowed_ext)) {
                        $errors['extension'] = ErrorMessage::EXTENSION_INVALID;
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
                        ->setUser_id($_SESSION['user']['id'])
                        ->setCreated_at_post();

                    $posts->create();
                    header('Location: index');
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

    /**
     * Gère la modification d'un post.
     * Effectue la validation des données et enregistre le post s'il n'y a pas d'erreurs.
     * En cas d'erreur, affiche les messages ou redirige vers une page d'erreur.
     *
     * @throws \Exception En cas d'erreur, redirige vers la page d'erreur 500.
     *
     * @return void
     */
    public function edit(int $id)
    {
        $errors = [];

        try {

            $postsModel = new PostModel();

            $post = $postsModel->find($id);

            if (isset($_POST['submit'])) {
                $title = trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
                $introduction = trim(htmlspecialchars($_POST['introduction'], ENT_QUOTES, 'UTF-8'));
                $postContent = trim(htmlspecialchars($_POST['postContent'], ENT_QUOTES, 'UTF-8'));
                $category = isset($_POST['category']) ? $_POST['category'] : '';
                $postStatus = isset($_POST['postStatus']) ? $_POST['postStatus'] : '';
                $postImage = $_FILES['postImages'];

                if (empty($title)) {
                    $errors['title'] = ErrorMessage::TITLE_INVALID;
                }

                if (empty($introduction)) {
                    $errors['introduction'] = ErrorMessage::INTRODUCTION_INVALID;
                }

                if (empty($postContent)) {
                    $errors['content'] = ErrorMessage::CONTENT_INVALID;
                }

                if (empty($category)) {
                    $errors['category'] = ErrorMessage::CATEGORY_INVALID;
                }

                if (empty($postStatus)) {
                    $errors['postStatus'] = ErrorMessage::STATUS_INVALID;
                }

                if (!empty($postImage['name'])) {
                    if($_FILES['postImages']['error'] > 0) {
                        $errors['transfert'] = ErrorMessage::TRANSFERT_INVALID;
                    }

                    $image_tmp_name = $_FILES['postImages']['tmp_name'];
                    $image_size = $_FILES['postImages']['size'];
                    $upload_path = "public/upload/";

                    $maxsize = 2 * 1024 * 1024;

                    if ($image_size >= $maxsize) {
                        $errors['size'] = ErrorMessage::SIZE_INVALID;
                    }

                    $image_ext = pathinfo($postImage['name'], PATHINFO_EXTENSION);
                    $image_ext_min = strtolower($image_ext);
                    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');

                    if (!in_array($image_ext_min, $allowed_ext)) {
                        $errors['extension'] = ErrorMessage::EXTENSION_INVALID;
                    }

                    if (empty($errors)) {
                        move_uploaded_file($image_tmp_name, $upload_path . $postImage['name']);
                    }
                }

                if (empty($errors)) {
                    $postsEdit = new PostModel();

                    $postsEdit->setTitle($title)
                        ->setIntroduction($introduction)
                        ->setPostContent($postContent)
                        ->setCategory($category)
                        ->setPostStatus($postStatus)
                        ->setUser_id($_SESSION['user']['id'])
                        ->setUpdated_at_post();

                    if (!empty($postImage['name'])) {
                        $postsEdit->setPostImage($postImage['name']);
                    }

                    $postsEdit->update($id);
                    header('Location: ../index');
                }
            }

            $formService = new FormService();
            $editPostForm = $formService->editPostService($post);

            $this->twig->display('admin/posts/edit.html.twig', [
                'errors' => $errors,
                'editPostForm' => $editPostForm->create()
            ]);

        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }

    /**
     * Gère la suppression d'un post.
     * Recherche le post à supprimer, effectue la suppression dans le modèle, puis redirige vers la liste des posts.
     *
     * @param int $id L'identifiant du post à supprimer.
     *
     * @throws \Exception En cas d'erreur, redirige vers la page d'erreur 500.
     *
     * @return void
     */
    public function delete(int $id)
    {
        try {
            $postsModel = new PostModel();

            $post = $postsModel->find($id);

            if (!$post) {
                // Le post n'existe pas, vous pouvez gérer cette situation
                // Redirection vers une page d'erreur ou autre
                header('Location: /error-page-404');
                exit;
            }

            $postsModel->delete($id);

            header('Location: ../index');

        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }
}
