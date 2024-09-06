<?php

namespace src\controller;

use src\Models\TagModel;
use src\Models\PostModel;
use src\Models\CommentModel;
use src\Service\PostService;
use src\Models\CategoryModel;
use src\service\TagCloudService;

class PostFrontController extends BaseController
{
    public function postList()
    {
        $postModel = new PostModel();
        $postService = new PostService();
        $commentModel = new CommentModel();

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

        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAllCategory();

        // Récupére les fréquences des tags
        $tagModel = new TagModel();
        $tags = $tagModel->getTagFrequencies();

        // Calcule les tailles des tags
        $tagCloudService = new TagCloudService();
        $tagSizes = $tagCloudService->calculateTagSizes($tags);

        // Récupération des posts les plus vus
        $mostViewedPosts = $postModel->getMostViewedPosts();

        // Récupération des derniers posts
        $latestPosts = $postModel->getPosts();

        $latestComments = $commentModel->getLatestComments();

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
            'mostViewedPosts' => $mostViewedPosts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'latestComments' => $latestComments,
            'page' => 'postlist',
        ]);
    }

    public function postSingle(string $slug)
    {
        $postModel = new PostModel();
        $commentModel = new CommentModel();

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

        $postDetail['tag'] = $postDetail['tag'] ? explode(', ', $postDetail['tag']) : [];

        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAllCategory();

        // Récupére les fréquences des tags
        $tagModel = new TagModel();
        $tags = $tagModel->getTagFrequencies();

        // Calcule les tailles des tags
        $tagCloudService = new TagCloudService();
        $tagSizes = $tagCloudService->calculateTagSizes($tags);

        // Récupération des posts les plus vus
        $mostViewedPosts = $postModel->getMostViewedPosts();

        // Récupération des derniers posts
        $latestPosts = $postModel->getPosts();

        $latestComments = $commentModel->getLatestComments();

        // Paramètres de pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8; // Nombre de commentaires par page
        $offset = ($page - 1) * $limit;

        // Récupére les commentaires avec pagination
        $commentByPosts = $commentModel->getCommentByPost($postId, $offset, $limit);
        $totalComments = $commentModel->countCommentsByPost($postId);
        $totalPages = ceil($totalComments / $limit);

        // Vérifie si la requête est une requête AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // Renvoie seulement la partie des commentaires en gardant la même vue
            $this->twig->display('frontend/postSingle.html.twig', [
                'commentByPosts' => $commentByPosts,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ]);
        } else {

            // Renvoie la page complète
            $this->twig->display('frontend/postSingle.html.twig', [
                'postDetail' => $postDetail,
                'commentByPosts' => $commentByPosts,
                'nextPostId' => $nextPostId,
                'previousPostId' => $previousPostId,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'mostViewedPosts' => $mostViewedPosts,
                'latestPosts' => $latestPosts,
                'categories' => $categories,
                'tagSizes' => $tagSizes,
                'latestComments' => $latestComments
            ]);
        }
    }
}
