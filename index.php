<?php
require 'vendor/autoload.php';

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
        $pattern = '/^' . $pattern . '\/?(\d+)?$/';

        // Comparer l'URI avec le motif de la route
        if (preg_match($pattern, $cleanedUri, $matches)) {
            $routeFound = true;

            $controllerName = $route['controller'];
            $controller = new $controllerName();

            $action = $route['action'];

            // Passer l'ID à l'action du contrôleur si défini dans les matches
            if (isset($matches[1])) {
                $id = $matches[1];
                $controller->$action($id);
            } else {
                $controller->$action();
            }

            break; // Sortir de la boucle une fois la correspondance trouvée
        }
    }

    // Si aucune correspondance n'est trouvée, renvoyer une erreur 404
    if (!$routeFound) {
        http_response_code(404);
        trigger_error("Sorry, the page you are looking for cannot be found", E_USER_WARNING);
    }
} else {
    http_response_code(500);
    trigger_error("Internal Server Error", E_USER_ERROR);
}
?>
