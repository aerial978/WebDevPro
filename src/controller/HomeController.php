<?php

namespace src\controller;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Homepage',
            'content' => 'Bienvenue sur la page d\'accueil de votre application.',
        ];

        echo $this->twig->render('home.html.twig', $data);
    }
}
