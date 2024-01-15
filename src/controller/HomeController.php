<?php

namespace src\controller;

class HomeController extends BaseController
{
    public function index()
    {
        $this->twig->display('front/home.html.twig');
    }
}
