<?php

namespace src\controller;

use src\Models\TagModel;
use src\Models\PostModel;
use src\Models\UserModel;
use src\Models\CommentModel;
use src\Service\PostService;
use src\Models\CategoryModel;
use src\Service\AsideService;

class PostFrontController extends BaseController
{
    public function postList()
    {
        $postModel = new PostModel();
        $postService = new PostService();
        $asideService = new AsideService();

        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        // Nombre de posts par page
        $limit = 10;
        // Calcul de l'offset pour la pagination, qui détermine combien d'éléments (posts) doivent être ignorés avant de récupérer les résultats pour la page courante
        $offset = ($page - 1) * $limit;

        if ($search) {
            $posts = $postModel->searchPosts($search, $offset, $limit);
            // calcul du nombre total de posts
            $totalPosts = $postModel->countSearchPosts($search);
        } else {
            $posts = $postModel->getPosts($offset, $limit);
            // calcul du nombre total de posts
            $totalPosts = $postModel->countPosts();
        }

        if ($search) {
            $posts = $postModel->searchPosts($search, $offset, $limit);
            $totalPosts = $postModel->countSearchPosts($search);
            foreach ($posts as &$post) {
                // Calcule et ajoute le temps écoulé depuis la dernière mise à jour du post
                $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
                // Créer et ajouter un extrait du titre du post en mettant en évidence les termes de recherche
                $post['excerpt_title'] = $postService->getExcerpt($post['title'], $search);
                // Créer et ajouter un extrait du contenu du post en mettant en évidence les termes de recherche
                $post['excerpt_content'] = $postService->getExcerpt($post['post_content'], $search);
            }
        } else {
            $posts = $postModel->getPosts($offset, $limit);
            $totalPosts = $postModel->countPosts();
            foreach ($posts as &$post) {
                $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
            }
        }

        $totalPages = ceil($totalPosts / $limit);

        $searchTerms = $search ? explode(' ', $search) : [];

        $asideData = $asideService->getAsideData();

        $this->twig->display('frontend/postList.html.twig', array_merge([
            'posts' => $posts,
            'search' => $search,
            'searchTerms' => $searchTerms,
            'showSeeMoreLinks' => false,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'page' => 'postlist',
        ], $asideData));
    }

    public function postSingle(string $slug)
    {
        $postModel = new PostModel();
        $commentModel = new CommentModel();
        $asideService = new AsideService();

        $postDetail = $postModel->singlePost($slug);

        // Vérification si le post existe
        if (!$postDetail) {
            // Affichage de la page 404 personnalisée
            $errorController = new ErrorController();
            $errorController->notFound();
            exit();
        }

        $postId = $postDetail['postId'];

        // Incrémenter le nombre de vues du post
        $postModel->incrementViewCount($postId);

        $nextPostId = $postModel->getNextPostId($postId);
        $previousPostId = $postModel->getPreviousPostId($postId);

        //$postDetail['tag'] = $postDetail['tag'] ? explode(', ', $postDetail['tag']) : [];

        $postDetail['tag'] = [];
        if (!empty($postDetail['tag_name']) && !empty($postDetail['tag_slug'])) {
            $tagName = explode(', ', $postDetail['tag_name']);
            $tagSlug = explode(', ', $postDetail['tag_slug']);

            foreach ($tagName as $index => $tagName) {
                $postDetail['tag'][] = [
                    'name' => $tagName,
                    'slug' => $tagSlug[$index]
                ];
            }
        }

        // Paramètres de pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8; // Nombre de commentaires par page
        $offset = ($page - 1) * $limit;

        // Récupére les commentaires avec pagination
        $commentByPosts = $commentModel->getCommentByPost($postId, $offset, $limit);
        $totalComments = $commentModel->countCommentsByPost($postId);
        $totalPages = ceil($totalComments / $limit);

        $asideData = $asideService->getAsideData();

        // Vérifie si la requête est une requête AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // Renvoie seulement la partie des commentaires en gardant la même vue
            $this->twig->display('frontend/postSingle.html.twig', array_merge([
                'commentByPosts' => $commentByPosts,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ], $asideData));
        } else {

            // Renvoie la page complète
            $this->twig->display('frontend/postSingle.html.twig', array_merge([
                'postDetail' => $postDetail,
                'commentByPosts' => $commentByPosts,
                'nextPostId' => $nextPostId,
                'previousPostId' => $previousPostId,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ], $asideData));
        }
    }

    public function postsByCategory(string $slug)
    {
        $categoryModel = new CategoryModel();
        $postModel = new PostModel();
        $asideService = new AsideService();

        $category = $categoryModel->getCategoryBySlug($slug);

        if ($category) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 2;
            $offset = ($page - 1) * $limit;

            $posts = $postModel->getPostsByCategory($category['id'], $offset, $limit);
            foreach ($posts as &$post) {
                $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
            }

            $totalPosts = $postModel->countPostsByCategory($category['id']);
            $totalPages = ceil($totalPosts / $limit);

            $asideData = $asideService->getAsideData();

            $this->twig->display('frontend/category.html.twig', array_merge([
                'posts' => $posts,
                'category' => $category,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'page' => 'category',
            ], $asideData));
        } else {
            $errorController = new ErrorController();
            $errorController->notFound();
            exit();
        }
    }

    public function postsByTag(string $slug)
    {
        $tagModel = new TagModel();
        $postModel = new PostModel();
        $asideService = new AsideService();

        $tag = $tagModel->getTagBySlug($slug);

        if ($tag) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 2;
            $offset = ($page - 1) * $limit;

            $posts = $postModel->getPostsByTag($tag['id'], $offset, $limit);
            foreach ($posts as &$post) {
                $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
            }

            $totalPosts = $postModel->countPostsByTag($tag['id']);
            $totalPages = ceil($totalPosts / $limit);

            $asideData = $asideService->getAsideData();

            $this->twig->display('frontend/tag.html.twig', array_merge([
                'posts' => $posts,
                'tag' => $tag,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'page' => 'tag',
            ], $asideData));
        } else {
            $errorController = new ErrorController();
            $errorController->notFound();
            exit();
        }
    }

    public function postsByUser(string $slug)
    {
        $userModel = new UserModel();
        $postModel = new PostModel();
        $asideService = new AsideService();

        $user = $userModel->getUserBySlug($slug);

        if ($user) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 2;
            $offset = ($page - 1) * $limit;

            $posts = $postModel->getPostsByUser($user['id'], $offset, $limit);
            foreach ($posts as &$post) {
                $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
            }

            $totalPosts = $postModel->countPostsByUser($user['id']);
            $totalPages = ceil($totalPosts / $limit);

            $asideData = $asideService->getAsideData();

            $this->twig->display('frontend/user.html.twig', array_merge([
                'posts' => $posts,
                'user' => $user,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'page' => 'user',
            ], $asideData));
        } else {
            $errorController = new ErrorController();
            $errorController->notFound();
            exit();
        }
    }

    public function postsByArchive($year, $month)
    {
        $postModel = new PostModel();
        $asideService = new AsideService();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 2;
        $offset = ($page - 1) * $limit;

        // Début et fin du mois pour la requête
        $startDate = "{$year}-{$month}-01";
        $endDate = date("Y-m-t", strtotime($startDate)); // Dernier jour du mois

        $formattedDate = date('F Y', strtotime($startDate));

        // Récupération des posts pour ce mois
        $posts = $postModel->getPostsByArchive($startDate, $endDate, $offset, $limit);
        foreach ($posts as &$post) {
            $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
        }

        $totalPosts = $postModel->countPostsByArchive($startDate, $endDate);
        $totalPages = ceil($totalPosts / $limit);

        // Vérification si des articles existent pour cette période
        if (empty($posts)) {
            // Redirection vers une page 404 si aucun article n'est trouvé
            $errorController = new ErrorController();
            $errorController->notFound();
            return;
        }

        $asideData = $asideService->getAsideData();

        // Affichage de la vue d'archives
        $this->twig->display('frontend/archive.html.twig', array_merge([
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'year' => $year,
            'month' => $month,
            'formattedDate' => $formattedDate,
            'page' => 'archive',
        ], $asideData));
    }

}
