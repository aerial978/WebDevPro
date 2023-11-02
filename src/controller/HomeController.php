<?php

namespace src\controller;

class HomeController extends BaseController
{
    public function index()
    {
        echo $this->twig->render('front/home.html.twig', [
            'pageTitle' => 'Homepage',
            'content'   => 'Bienvenue sur la page d\'accueil de votre application.',
        ]);
    }
}
