<?php

use src\controller\HomeController;
use src\controller\PrivacyPolicyController;
use src\controller\SecurityController;
use src\controller\DashboardController;

return [
    '/home' => ['controller' => HomeController::class, 'action' => 'index'],
    '/privacy-policy' => ['controller' => PrivacyPolicyController::class, 'action' => 'index'],
    '/login' => ['controller' => SecurityController::class, 'action' => 'handleLogin'],
    '/registration' => ['controller' => SecurityController::class, 'action' => 'handleRegistration'],
    '/logout' => ['controller' => SecurityController::class, 'action' => 'logout'],
    '/dashboard' => ['controller' => DashboardController::class, 'action' => 'index'],

];
