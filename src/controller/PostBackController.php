<?php

namespace src\controller;

use src\Models\TagModel;
use src\Models\PostModel;
use src\Models\PostTagModel;
use src\Service\PostService;
use src\Models\CategoryModel;
use src\Constants\ErrorMessage;
use src\Service\PostFormService;
use src\service\TagCloudService;
use src\service\SlugService;

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

        //try {
        if (isset($_POST['submit'])) {
            $title = trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
            $postContent = $_POST['postContent']; // contenu brut, sera assaini plus tard avant enregistrement
            $category = isset($_POST['category']) ? $_POST['category'] : '';
            $postStatus = isset($_POST['postStatus']) ? $_POST['postStatus'] : '';
            $postImage = $_FILES['postImage'];

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

                // Générer le slug à partir du titre
                $slugService = new SlugService();
                $slugPost = $slugService->generateSlug($title);

                $posts = new PostModel();

                $posts->setTitle($title)
                    ->setPostContent($postContent)
                    ->setSlugPost($slugPost)
                    ->setCategory_id($category)
                    ->setStatus_id($postStatus)
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

        $postFormService = new PostFormService();
        $createPostForm = $postFormService->createPostService($categoriesOptions, $tagsOptions);

        $this->twig->display('admin/posts/create.html.twig', [
            'errors' => $errors,
            'createPostForm' => $createPostForm->create()
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

        if (isset($_POST['submit'])) {
            $title = trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
            $postContent = $_POST['postContent']; // contenu brut, sera assaini plus tard avant enregistrement
            $category = isset($_POST['category']) ? $_POST['category'] : '';
            $postStatus = isset($_POST['postStatus']) ? $_POST['postStatus'] : '';
            $postImage = $_FILES['postImages'];

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

        $postFormService = new PostFormService();
        $editPostForm = $postFormService->editPostService($categoriesOptions, $post, $tagsOptions, $tagsForPost);

        $this->twig->display('admin/posts/edit.html.twig', [
            'errors' => $errors,
            'editPostForm' => $editPostForm->create(),
            'tagsForPost' => $tagsForPost,
            'selectedTagsIds' => $selectedTagsIds
        ]);
        /*} catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }*/
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

    public function postList()
    {
        $postModel = new PostModel();
        $postService = new PostService();

        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        // Nombre de posts par page
        $limit = 2;
        // Calcul de l'offset pour la pagination, qui détermine combien d'éléments (posts) doivent être ignorés avant de récupérer les résultats pour la page courante
        $offset = ($page - 1) * $limit;

        if ($search) {
            $posts = $postModel->searchPosts($search, $offset, $limit);
            // calcul du nombre total de posts
            $totalPosts = count($posts);
        } else {
            $posts = $postModel->getPosts($offset, $limit);
            // calcul du nombre total de posts
            $totalPosts = $postModel->countPosts();
        }

        if ($search) {
            $posts = $postModel->searchPosts($search, $offset, $limit);
            $totalPosts = count($posts);
            foreach ($posts as &$post) {
                $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
                $post['excerpt_title'] = $postService->getExcerpt($post['title'], $search);
                $post['excerpt_content'] = $postService->getExcerpt($post['post_content'], $search);
            }
        } else {
            $posts = $postModel->getPosts($offset, $limit);
            $totalPosts = $postModel->countPosts();
            foreach ($posts as &$post) {
                $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
            }
        }

        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAllCategory();

        // Récupére les fréquences des tags
        $tagModel = new TagModel();
        $tags = $tagModel->getTagFrequencies();

        // Calcule les tailles des tags
        $tagCloudService = new TagCloudService();
        $tagSizes = $tagCloudService->calculateTagSizes($tags);

        $latestPosts = $postModel->getPosts();

        // Pagination, calcul du nombre total de pages
        $totalPages = ceil($totalPosts / $limit);

        $searchTerms = $search ? explode(' ', $search) : [];

        $this->twig->display('frontend/postList.html.twig', [
            'posts' => $posts,
            'search' => $search,
            'searchTerms' => $searchTerms,
            'categories' => $categories,
            'tagSizes' => $tagSizes,
            'latestPosts' => $latestPosts,
            'showSeeMoreLinks' => false,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
