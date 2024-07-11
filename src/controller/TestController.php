<?php

namespace src\controller;

use src\Models\PostTagModel;

class TestController extends BaseController
{
    public function testAddTagsToPost()
    {
        // Remplacez ces valeurs par celles correspondant à votre test
        $postId = 298; // L'ID du post auquel vous souhaitez ajouter des tags
        $tagIds = [77, 78, 80]; // Les ID des tags à ajouter

        $postTagModel = new PostTagModel();
        $results = $postTagModel->addTagsToPost($postId, $tagIds);

        // Afficher les résultats sur la page de test
        var_dump($results);
    }
}
