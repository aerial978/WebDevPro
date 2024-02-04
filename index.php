<?php

session_set_cookie_params([
    'secure' => true, // Utiliser seulement pour les connexions HTTPS
    'httponly' => true,
    'samesite' => 'Lax', // Ajuster selon vos besoins
]);

session_start();

require  'vendor/autoload.php';

//fonction de rappel anonyme afin de rechercher automatiquement le fichier de classe, le charger, et de l'utiliser.
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/'; // Chemin de base de votre application

    // Convertir le namespace en chemin de fichier.
    $classFile = str_replace('\\', '/', $class) . '.php';
    $classFile = $baseDir . $classFile;

    require_once $classFile;

});

// Inclure le fichier des routes.
$routes = include 'config/route.php';

$requestUri = isset($_SERVER['REQUEST_URI']) ? stripslashes($_SERVER['REQUEST_URI']) : null;

if ($requestUri) {
    // Extraction de l'URI.
    //$requestUri = parse_url($requestUri, PHP_URL_PATH);

    $cleanedUri = filter_var($requestUri, FILTER_SANITIZE_URL);

    $uriSegments = explode('/webdevpro', $requestUri);

    // Récupérez le dernier segment (c'est celui qui vous intéresse).
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
            trigger_error("404 - Resource not found", E_USER_WARNING);
        }
    } else {
        http_response_code(404);
        trigger_error("Sorry, the page you are looking for cannot be found", E_USER_WARNING);
    }
} else {
    // Gérer le cas où $_SERVER['REQUEST_URI'] n'est pas défini.
    http_response_code(500);
    trigger_error("Internal Server Error", E_USER_ERROR);
}
