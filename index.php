<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use src\controller\ErrorController;

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';

    $classFile = str_replace('\\', '/', $class) . '.php';
    $classFile = $baseDir . $classFile;

    require_once $classFile;
});

$routes = include 'config/route.php';

$requestUri = isset($_SERVER['REQUEST_URI']) ? stripslashes($_SERVER['REQUEST_URI']) : null;

if ($requestUri) {
    $parsedUrl = parse_url($requestUri);//////
    $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';//////
    $cleanedUri = filter_var($requestUri, FILTER_SANITIZE_URL);
    $uriSegments = explode('/webdevpro', $path);
    $cleanedUri = end($uriSegments);

    $routeFound = false;

    // Itérer sur toutes les routes pour trouver la correspondance
    foreach ($routes as $routeUri => $route) {
        // Générer une expression régulière pour la route
        $pattern = str_replace('/', '\/', $routeUri);///////
        $pattern = '/^' . $pattern . '\/?(\d+)?\/?(\d+)?$/';

        // Comparer l'URI avec le motif de la route
        if (preg_match($pattern, $cleanedUri, $matches)) {
            $routeFound = true;

            $controllerName = $route['controller'];
            $controller = new $controllerName();

            $action = $route['action'];

            // Passer l'ID à l'action du contrôleur si défini dans les matches
            if (isset($matches[1]) && isset($matches[2])) {
                $controller->$action($matches[1], $matches[2]); // Passer deux paramètres (year, month)
            } elseif (isset($matches[1])) {
                $controller->$action($matches[1]); // Passer un seul paramètre (slug ou id)
            } else {
                $controller->$action(); // Pas de paramètre
            }

            break; // Sortir de la boucle une fois la correspondance trouvée
        }
    }

    // Si aucune correspondance n'est trouvée, renvoyer une erreur 404
    if (!$routeFound) {
        $errorController = new ErrorController();
        $errorController->notFound(); 
    }
} else {
    $errorController = new ErrorController();
    $errorController->serverError(); 
}
?>
