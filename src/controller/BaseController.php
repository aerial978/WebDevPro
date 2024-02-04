<?php

namespace src\controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use src\Session\SessionManager;

abstract class BaseController
{
    private $loader;
    protected $twig;

    public function __construct()
    {
        $this->loader = new FilesystemLoader(__DIR__ . '/../../src/view');

        $this->twig = new Environment($this->loader);

        $sessionManager = new SessionManager();
        $this->twig->addGlobal('session', $sessionManager->getAll());
    }
}
