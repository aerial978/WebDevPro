<?php

use src\controller\HomeController;
use src\controller\PostBackController;
use src\controller\PostFrontController;
use src\controller\TestController;
use src\controller\CategoryController;
use src\controller\SecurityController;
use src\controller\DashboardController;
use src\controller\TagController;
use src\controller\CommentController;
use src\controller\PrivacyPolicyController;

return [
    '/home' => ['controller' => HomeController::class, 'action' => 'index'],
    '/privacy-policy' => ['controller' => PrivacyPolicyController::class, 'action' => 'index'],
    '/login' => ['controller' => SecurityController::class, 'action' => 'handleLogin'],
    '/registration' => ['controller' => SecurityController::class, 'action' => 'handleRegistration'],
    '/logout' => ['controller' => SecurityController::class, 'action' => 'logout'],
    '/admin/dashboard' => ['controller' => DashboardController::class, 'action' => 'index'],
    '/admin/posts/index' => ['controller' => PostBackController::class, 'action' => 'index'],
    '/admin/posts/create' => ['controller' => PostBackController::class, 'action' => 'create'],
    '/admin/posts/edit' => ['controller' => PostBackController::class, 'action' => 'edit'],
    '/admin/posts/delete/' => ['controller' => PostBackController::class, 'action' => 'delete'],
    '/admin/categories/index' => ['controller' => CategoryController::class, 'action' => 'index'],
    '/admin/categories/create' => ['controller' => CategoryController::class, 'action' => 'create'],
    '/admin/categories/edit/' => ['controller' => CategoryController::class, 'action' => 'edit'],
    '/admin/categories/delete/' => ['controller' => CategoryController::class, 'action' => 'delete'],
    '/admin/tags/index' => ['controller' => TagController::class, 'action' => 'index'],
    '/admin/tags/create' => ['controller' => TagController::class, 'action' => 'create'],
    '/admin/tags/edit/' => ['controller' => TagController::class, 'action' => 'edit'],
    '/admin/tags/delete/' => ['controller' => TagController::class, 'action' => 'delete'],
    '/posts' => ['controller' => PostFrontController::class, 'action' => 'postList'],
    '/postsingle/([a-zA-Z0-9_-]+)' => ['controller' => PostFrontController::class, 'action' => 'postSingle'],
    '/addcomment' => ['controller' => CommentController::class, 'action' => 'addComment'],
    '/test/addTagsToPost' => ['controller' => TestController::class, 'action' => 'testAddTagsToPost']
];
