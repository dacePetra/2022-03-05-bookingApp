<?php
session_start();

use App\Controllers\ApartmentsController;
use App\Redirect;
use App\Views\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Controllers\UsersController;

require_once 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/apartments', [ApartmentsController::class, 'index']);
    $r->addRoute('GET', '/apartments/{id:\d+}', [ApartmentsController::class, 'show']);

    $r->addRoute('GET', '/apartments/create', [ApartmentsController::class, 'create']);
    $r->addRoute('POST', '/apartments', [ApartmentsController::class, 'store']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        var_dump("404 Not Found");
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        var_dump("405 Method Not Allowed");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];   // routeInfo array is in brackets addRoute(0=>'GET', 1=>'/articles/{id:\d+}/edit', 2=>[ArticlesController::class, 'index'])
        $controller = $handler[0];
        $method = $handler[1];
        $vars = $routeInfo[2];

        /** @var View $response */ // because of this getPath and getVariables can be called
        $response = (new $controller)->$method($vars);

        $loader = new FilesystemLoader('app/Views'); //filename path
        $twig = new Environment($loader);

        if ($response instanceof View) {
            echo $twig->render($response->getPath() . '.html', $response->getVariables());
        }
        if ($response instanceof Redirect) {
            header('Location: ' . $response->getLocation());
            exit;
        }
        break;
}
