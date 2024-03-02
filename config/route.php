<?php

use src\controller\HomeController;
use src\controller\PrivacyPolicyController;
use src\controller\SecurityController;
use src\controller\DashboardController;
use src\controller\PostController;

return [
    '/home' => ['controller' => HomeController::class, 'action' => 'index'],
    '/privacy-policy' => ['controller' => PrivacyPolicyController::class, 'action' => 'index'],
    '/login' => ['controller' => SecurityController::class, 'action' => 'handleLogin'],
    '/registration' => ['controller' => SecurityController::class, 'action' => 'handleRegistration'],
    '/logout' => ['controller' => SecurityController::class, 'action' => 'logout'],
    '/admin/dashboard' => ['controller' => DashboardController::class, 'action' => 'index'],
    '/admin/posts/index' => ['controller' => PostController::class, 'action' => 'index'],
    '/admin/posts/create' => ['controller' => PostController::class, 'action' => 'create'],
    '/admin/posts/edit/' => ['controller' => PostController::class, 'action' => 'edit'],
    '/admin/posts/delete/' => ['controller' => PostController::class, 'action' => 'delete'],
];
