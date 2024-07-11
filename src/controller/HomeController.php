<?php

namespace src\controller;

use src\Models\PostModel;

class HomeController extends BaseController
{
    public function index()
    {
        $postModel = new PostModel();

        $posts = $postModel->getPosts();

        foreach ($posts as &$post) {
            $post['time_elapsed'] = $postModel->timeElapsedString($post['updated_at_post']);
        }

        $this->twig->display('frontend/home.html.twig', [
            'posts' => $posts
        ]);
    }
}
