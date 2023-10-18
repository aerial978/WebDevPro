<?php

require_once __DIR__ . '/vendor/autoload.php';

// fonction de rappel anonyme afin de rechercher automatiquement le 
// fichier de classe, le charger, et de l'utiliser
spl_autoload_register(function($class) {
  $baseDir = __DIR__ . '/'; // Chemin de base de votre application

    // Convertir le namespace en chemin de fichier
    $classFile = str_replace('\\', '/', $class) . '.php';
    $classFile = $baseDir . $classFile;

    if(file_exists($classFile)) {
        require_once($classFile);
    }
});

// Inclure le fichier des routes
$routes = include(__DIR__ . '/config/route.php');

$requestUri = $_SERVER['REQUEST_URI'];

// Extraction de l'URI
$requestUri = parse_url($requestUri, PHP_URL_PATH);

$uriSegments = explode('/webdevpro', $requestUri);

// Récupérez le dernier segment (c'est celui qui vous intéresse)
$cleanedUri = end($uriSegments);

if(array_key_exists($cleanedUri, $routes)) {
    $route = $routes[$cleanedUri];

    $controllerName = $route['controller'];
    $controller = new $controllerName();

    $action = $route['action'];

    if(method_exists($controller, $action)) {
        $controller->$action();
    } else {
        http_response_code(404);
        echo "404 - Resource not found";
    }
} else {
    http_response_code(404);
        echo "Sorry, the page you are looking for cannot be found";
}