<?php

namespace src\controller;

use src\Models\TagModel;
use src\Models\PostModel;
use src\Models\PostTagModel;
use src\Models\CategoryModel;
use src\Constants\ErrorMessage;
use src\Service\PostFormService;

class PostController extends BaseController
{
    /**
     * Displays the list of articles in the admin interface.
     *
     * Retrieves all articles from the database and displays them in the corresponding view.
     */
    public function index()
    {
        $postsModel = new PostModel();

        $indexPosts = $postsModel->findAllPost();

        $this->twig->display('admin/posts/index.html.twig', compact('indexPosts'));
    }

    /**
     * Manages the creation of a new post.
     * Performs data validation and saves the post if there are no errors.
     * In case of error, displays messages or redirects to an error page.
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
                        ->setCategory_id($category)
                        ->setPostStatus($postStatus)
                        ->setPostImage($postImage['name'])
                        ->setUser_id($_SESSION['user']['id'])
                        ->setCreated_at_post();

                    $posts->createPost();
                    header('Location: index');
                }
            }

            $tagModel = new TagModel();
            $tags = isset($_POST['tags']) ? $_POST['tags'] : [];

            $postModel = new PostModel();
            $getLastInsertedPostId = $postModel->getLastInsertedPostId();

            foreach ($tags as $tag) {
                if (is_numeric($tag)) {
                    $criteria = ['id' => $tag];

                    $postTagModel = new PostTagModel();
                    $postTagModel->addTagsToPost($getLastInsertedPostId, $criteria);
                } else {
                    $criteria = ['tag_name' => $tag];
                }

                $existingTags = $tagModel->findBy($criteria);

                if (empty($existingTags)) {
                    $tagData = ['tag_name' => $tag];
                    $tagModel->setTagName($tagData['tag_name']);
                    $tagModel->create($tagData);

                    $getLastInsertedTagId = [$tagModel->getLastInsertedTagId()];

                    $postTagModel = new PostTagModel();
                    $postTagModel->addTagsToPost($getLastInsertedPostId, $getLastInsertedTagId);
                }
            }

            $categoryModel = new CategoryModel();
            $categories = $categoryModel->findAll();

            $categoriesOptions = [];
            foreach ($categories as $category) {
                $categoriesOptions[$category->id] = $category->name_category;
            }

            $tagModel = new TagModel();
            $tags = $tagModel->findAll();

            $tagsOptions = [];
            foreach ($tags as $tag) {
                $tagsOptions[$tag->id] = $tag->tag_name;
            }

            $postFormService = new PostFormService();
            $createPostForm = $postFormService->createPostService($categoriesOptions, $tagsOptions);

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
     * Manages the modification of a post.
     * Performs data validation and saves the post if there are no errors.
     * In case of error, displays messages or redirects to an error page.
     *
     * @param int $id The ID of the post to edit.
     *
     * @throws \Exception On error, redirects to error page 500.
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
                    if ($_FILES['postImages']['error'] > 0) {
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
                        ->setCategory_id($category)
                        ->setPostStatus($postStatus)
                        ->setUser_id($_SESSION['user']['id'])
                        ->setUpdated_at_post();

                    if (!empty($postImage['name'])) {
                        $postsEdit->setPostImage($postImage['name']);
                    }

                    $postsEdit->update($id);

                    $selectedTags = isset($_POST['tags']) ? $_POST['tags'] : [];

                    $tagsModel = new TagModel();
                    $currentTags = $tagsModel->getTagsForPost($id);

                    $currentTagIds = array_column($currentTags, 'tag_id');

                    if (!empty($selectedTags)) {
                        $tagsToAdd = array_values(array_diff($selectedTags, $currentTagIds));
                        $tagsToRemove = array_values(array_diff($currentTagIds, $selectedTags));

                        foreach ($tagsToAdd as $tagId) {
                            if (!is_numeric($tagId)) {
                                $tagName = ucfirst($tagId);
                                $tagData = ['tag_name' => $tagName];
                                $tagsModel->setTagName($tagData['tag_name']);
                                $tagsModel->create($tagData);

                                $tagId = $tagsModel->getLastInsertedTagId();
                            }

                            $postTagModel = new PostTagModel();
                            $postTagModel->addTagsToPost($id, [$tagId]);
                        }

                        foreach ($tagsToRemove as $tagId) {
                            $postTagModel = new PostTagModel();
                            $postTagModel->removeTagsFromPost($id, [$tagId]);
                        }
                    }
                    header('Location: ../index');
                }
            }

            $categoryModel = new CategoryModel();
            $categories = $categoryModel->findAll();

            $categoriesOptions = [];
            foreach ($categories as $category) {
                $categoriesOptions[$category->id] = $category->name_category;
            }

            $tagModel = new TagModel();
            $tags = $tagModel->findAll();

            $tagsOptions = [];
            foreach ($tags as $tag) {
                $tagsOptions[$tag->id] = $tag->tag_name;
            }

            $tagsModel = new TagModel();
            $tagsForPost = $tagsModel->getTagsForPost($id);

            $postFormService = new PostFormService();
            $editPostForm = $postFormService->editPostService($categoriesOptions, $post, $tagsOptions, $tagsForPost);

            $this->twig->display('admin/posts/edit.html.twig', [
                'errors' => $errors,
                'editPostForm' => $editPostForm->create(),
                'tagsForPost' => $tagsForPost
            ]);
        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }

    /**
     * Manages the deletion of a post.
     * Finds the post to delete, performs the deletion in the template, then redirects to the list of posts.
     *
     * @param int $id The ID of the post to delete.
     *
     * @throws \Exception On error, redirects to error page 500.
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
