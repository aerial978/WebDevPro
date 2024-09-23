<?php

namespace src\Service;

use src\Models\CategoryModel;
use src\Models\TagModel;
use src\Models\PostModel;
use src\Models\CommentModel;

class AsideService
{
    public function getAsideData()
    {
        // Récupération des catégories
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAllCategory();

        // Récupération des fréquences des tags
        $tagModel = new TagModel();
        $tags = $tagModel->getTagFrequencies();

        // Calcul des tailles des tags
        $tagCloudService = new TagCloudService();
        $tagSizes = $tagCloudService->calculateTagSizes($tags);

        // Récupération des posts les plus vus
        $postModel = new PostModel();
        $mostViewedPosts = $postModel->getMostViewedPosts();

        // Récupération des derniers posts
        $latestPosts = $postModel->getPosts();

        // Récupération des derniers commentaires
        $commentModel = new CommentModel();
        $latestComments = $commentModel->getLatestComments();

        $archiveData = $postModel->getArchives();

        return [
            'tags' => $tags,
            'categories' => $categories,
            'tagSizes' => $tagSizes,
            'latestPosts' => $latestPosts,
            'mostViewedPosts' => $mostViewedPosts,
            'latestComments' => $latestComments,
            'archives' => $archiveData,
        ];
    }
}
