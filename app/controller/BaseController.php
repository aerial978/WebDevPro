<?php

namespace app\controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class BaseController
{
    private $loader;
    protected $twig;

    public function __construct()
    {
        $this->loader = new FilesystemLoader(__DIR__ . '/../../app/view/template');
        
        $this->twig = new Environment($this->loader);
    }
}