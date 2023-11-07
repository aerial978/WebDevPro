<?php

use src\controller\HomeController;
use src\controller\PrivacyPolicyController;

return [
    '/' => ['controller' => HomeController::class, 'action' => 'index'],
    '/privacy-policy' => ['controller' => PrivacyPolicyController::class, 'action' => 'index'],
];
