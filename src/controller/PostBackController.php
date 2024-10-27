<?php

namespace src\controller;

use src\Models\TagModel;
use src\Models\PostModel;
use src\Models\CommentModel;
use src\Models\PostTagModel;
use src\service\SlugService;
use src\Models\CategoryModel;
use src\Service\StatusService;
use src\Constants\ErrorMessage;
use src\Models\PostHistoryModel;
use src\Models\CommentHistoryModel;
use src\Models\ModerationReasonModel;

class PostBackController extends BaseController
{
    /**
     * Displays the list of articles in the admin interface.
     *
     * Retrieves all articles from the database and displays them in the corresponding view.
     */
    public function index()
    {
        $postsModel = new PostModel();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        $sortColumn = $_GET['sortColumn'] ?? null; // Par défaut, tri par ID
        $sortOrder = $_GET['sortOrder'] ?? 'desc'; // Par défaut, ordre décroissant

        if ($sortColumn) {
            $indexPosts = $postsModel->findAllPost($offset, $limit, $sortColumn, $sortOrder);
        } else {
            $indexPosts = $postsModel->findAllPost($offset, $limit);
        }

        $totalComments = $postsModel->countPosts();
        $totalPages = ceil($totalComments / $limit);

        $this->twig->display('admin/posts/index.html.twig', [
            'indexPosts' => $indexPosts,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
        ]);
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

        $statusService = new StatusService();
        $statusOptions = $statusService->getStatusOptions();

        //try {
        if (isset($_POST['submit'])) {
            $title = trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
            $postContent = $_POST['content-post']; // contenu brut, sera assaini plus tard avant enregistrement
            $category = isset($_POST['category']) ? $_POST['category'] : '';
            $postImage = $_FILES['image-post'];

            if (empty($title)) {
                $errors['title'] = ErrorMessage::TITLE_INVALID;
            }

            if (empty($postContent)) {
                $errors['content'] = ErrorMessage::CONTENT_INVALID;
            }

            if (empty($category)) {
                $errors['category'] = ErrorMessage::CATEGORY_INVALID;
            }

            if (empty($postImage['name'])) {
                $errors['postImage'] = ErrorMessage::IMAGEPOST_INVALID;
            } elseif (isset($_FILES['image-post']) && $_FILES['image-post']['size'] > 0) {
                $error = $_FILES['image-post']['error'];
                if ($error > 0) {
                    $errors['transfert'] = ErrorMessage::TRANSFERT_INVALID;
                }

                $image_tmp_name = $_FILES['image-post']['tmp_name'];
                $image_size = $_FILES['image-post']['size'];
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

                // Générer le slug à partir du titre
                $slugService = new SlugService();
                $slugPost = $slugService->generateSlug($title);

                $posts = new PostModel();

                $posts->setTitle($title)
                    ->setPostContent($postContent)
                    ->setSlugPost($slugPost)
                    ->setCategory_id($category)
                    ->setPostImage($postImage['name'])
                    ->setUser_id($_SESSION['user']['id'])
                    ->setCreated_at_post();

                $posts->createPost();

                // Récupérer l'ID du post nouvellement créé
                $postId = $posts->getLastInsertedPostId();

                $selectedTags = isset($_POST['tags']) ? $_POST['tags'] : [];
                $selectedTagsArray = explode(',', $selectedTags);

                $tagsModel = new TagModel();
                $postTagModel = new PostTagModel();

                if (!empty($selectedTags)) {
                    $existingTagNames = $tagsModel->getAllTagNames();
                    $tagsToAdd = array_values(array_diff($selectedTagsArray, $existingTagNames));

                    foreach ($tagsToAdd as $tagAdd) {
                        $tagData = ['name_tag' => $tagAdd];
                        $tagsModel->setNameTag($tagData['name_tag']);
                        $tagsModel->create($tagData);
                    }

                    foreach ($selectedTagsArray as $tag) {
                        $tagId = $tagsModel->getTagIdByTagName($tag);

                        $postTag = $postTagModel->getByPostAndTag($postId, $tagId->id);
                        if (!$postTag) {
                            $postTagModel->addTagsToPost($postId, [$tagId->id]);
                        }
                    }
                }
                header('Location: index');
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
            $tagsOptions[$tag->id] = $tag->name_tag;
        }

        $this->twig->display('admin/posts/create.html.twig', [
            'errors' => $errors,
            'statusOptions' => $statusOptions,
            'categoriesOptions' => $categoriesOptions,
            'tagsOptions' => $tagsOptions,
        ]);
        /*} catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }*/
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

        //try {
        $postsModel = new PostModel();
        $post = $postsModel->find($id);

        $statusService = new StatusService();
        $statusOptions = $statusService->getStatusOptions();

        $moderationReasonModel = new ModerationReasonModel();
        $reasonsData = $moderationReasonModel->getAllReasons();

        $reasons = [];
        foreach ($reasonsData as $reason) {
            $reasons[$reason['id']] = $reason['refusal_reason'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
            $postContent = $_POST['post-content']; // contenu brut, sera assaini plus tard avant enregistrement
            $category = $_POST['category'] ?? null;
            $postStatus = $_POST['status-post'] ?? null;
            $moderationReasonId = $_POST['moderation-reason'] ?? null;
            $revisionDetails = $_POST['detail-revision'] ?? null;
            $postImage = $_FILES['image-post'];

            if (empty($title)) {
                $errors['title'] = ErrorMessage::TITLE_INVALID;
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

            if ($postStatus == '5' && empty($moderationReasonId)) {
                $errors['moderation-reason-id'] = ErrorMessage::REFUSAL_INVALID;
            }

            if ($postStatus == '5' && $moderationReasonId == '6') {
                if (empty($revisionDetails)) {
                    $errors['revision-reasons'] = ErrorMessage::SPECIFYREASON_INVALID;
                }
            }

            if (!empty($postImage['name'])) {
                if ($_FILES['image-post']['error'] > 0) {
                    $errors['transfert'] = ErrorMessage::TRANSFERT_INVALID;
                }

                $image_tmp_name = $_FILES['image-post']['tmp_name'];
                $image_size = $_FILES['image-post']['size'];
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

                if ($title !== $post['title']) {
                    $slugService = new SlugService();
                    $slugPost = $slugService->generateSlug($title);
                    $postsEdit->setSlugPost($slugPost);
                }

                $postsEdit->setTitle($title)
                    ->setPostContent($postContent)
                    ->setCategory_id($category)
                    ->setStatus_id($postStatus)
                    ->setUser_id($_SESSION['user']['id'])

                    ->setUpdated_at_post();

                if (!empty($postImage['name'])) {
                    $postsEdit->setPostImage($postImage['name']);
                }

                $postsEdit->update($id);

                $postHistoryModel = new PostHistoryModel();

                $postHistoryModel->setPostId($id)
                    ->setStatusId($postStatus)
                    ->setUserId($post['user_id'])
                    ->setModeratedByPost($_SESSION['user']['id'])
                    ->setModerationReasonId($moderationReasonId)
                    ->setRevisionDetails(!empty($revisionDetails) ? $revisionDetails : null)
                    ->setCreatedAtModerationPost(date('Y-m-d H:i:s'));

                $postHistoryModel->create();

                $selectedTags = isset($_POST['tags']) ? $_POST['tags'] : [];

                $selectedTagsArray = explode(',', $selectedTags);

                $tagsModel = new TagModel();
                $postTagModel = new PostTagModel();

                if (!empty($selectedTags)) {
                    $existingTagNames = $tagsModel->getAllTagNames();

                    $tagsToAdd = array_values(array_diff($selectedTagsArray, $existingTagNames));

                    $postTagModel->removeTagsFromPost($id);

                    foreach ($tagsToAdd as $tagAdd) {
                        $tagData = ['name_tag' => $tagAdd];

                        $tagsModel->setNameTag($tagData['name_tag']);

                        $tagsModel->create($tagData);
                    }

                    foreach($selectedTagsArray as $tag) {

                        // récupére l'ID d'un tag en fonction de son nom
                        $tagId = $tagsModel->getTagIdByTagName($tag);

                        $postTag = $postTagModel->getByPostAndTag($id, $tagId->id);
                        if(!$postTag) {
                            // récupère l'id de postTag si un tag est déjà associé à un post
                            $postTagModel->addTagsToPost($id, [$tagId->id]);
                        }
                    }
                }

                header('Location: ../index');
            }
        }

        $postHistoryModel = new PostHistoryModel();
        $moderationActions = $postHistoryModel->findModerationHistoryByPostId($id);

        $selectedValue = isset($moderationActions[0]['historyReasonId']) ? $moderationActions[0]['historyReasonId'] : null;

        $revisionDetails = isset($moderationActions[0]['historyRevisionDetails']) ? $moderationActions[0]['historyRevisionDetails'] : null;

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
            $tagsOptions[$tag->id] = $tag->name_tag;
        }

        $tagsModel = new TagModel();
        $tagsForPost = $tagsModel->getTagsForPost($id);

        $selectedTagsIds = array_column($tagsForPost, 'name_tag');

        $this->twig->display('admin/posts/edit.html.twig', [
            'errors' => $errors,
            'post' => $post,
            'statusOptions' => $statusOptions,
            'categoriesOptions' => $categoriesOptions,
            'tagsForPost' => $tagsForPost,
            'selectedTagsIds' => $selectedTagsIds,
            'tagsOptions' => $tagsOptions,
            'reasons' => $reasons,
            'selectedValue' => $selectedValue,
            'revisionDetails' => $revisionDetails,
        ]);
        /*} catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }*/
    }

    public function historyPost($postId)
    {
        $postsModel = new PostModel();
        $postHistory = $postsModel->findHistoryPostById($postId);

        $postsHistoryModel = new PostHistoryModel();
        $moderationActions = $postsHistoryModel->findModerationHistoryByPostId($postId);

        $this->twig->display('admin/posts/history.html.twig', [
            'postHistory' => $postHistory,
            'moderationActions' => $moderationActions,
        ]);
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
