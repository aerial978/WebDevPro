<?php

namespace src\controller;

class HomeController extends BaseController
{
    public function index()
    {
        $this->twig->display('frontend/home.html.twig');
    }
}
