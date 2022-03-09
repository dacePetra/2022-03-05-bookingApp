<?php
session_start();

use App\Controllers\ApartmentReservationsController;
use App\Controllers\ApartmentReviewsController;
use App\Controllers\WelcomeController;
use App\Controllers\UsersController;
use App\Controllers\ApartmentsController;
use App\Redirect;
use App\Views\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


require_once 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/', [WelcomeController::class, 'opening']);
    $r->addRoute('GET', '/welcome', [WelcomeController::class, 'welcome']);

    $r->addRoute('GET', '/users/signup', [UsersController::class, 'signup']);
    $r->addRoute('POST', '/users', [UsersController::class, 'register']);

    $r->addRoute('GET', '/users/login', [UsersController::class, 'login']);
    $r->addRoute('POST', '/users/login', [UsersController::class, 'enter']);

    $r->addRoute('GET', '/users/logout', [UsersController::class, 'logout']);

    $r->addRoute('GET', '/users', [UsersController::class, 'index']);
    $r->addRoute('GET', '/users/{id:\d+}', [UsersController::class, 'show']);

    $r->addRoute('GET', '/apartments', [ApartmentsController::class, 'index']);
    $r->addRoute('GET', '/apartments/{id:\d+}', [ApartmentsController::class, 'show']);

    $r->addRoute('GET', '/apartments/create', [ApartmentsController::class, 'create']);
    $r->addRoute('POST', '/apartments', [ApartmentsController::class, 'store']);

    $r->addRoute('POST', '/apartments/{id:\d+}/delete', [ApartmentsController::class, 'delete']);

    $r->addRoute('GET', '/apartments/{id:\d+}/edit', [ApartmentsController::class, 'edit']);
    $r->addRoute('POST', '/apartments/{id:\d+}', [ApartmentsController::class, 'update']);

    $r->addRoute('POST', '/apartments/{id:\d+}/review', [ApartmentReviewsController::class, 'review']);
    $r->addRoute('POST', '/apartments/{nr:\d+}/erase/{id:\d+}', [ApartmentReviewsController::class, 'erase']);

    $r->addRoute('GET', '/apartments/{id:\d+}/reserve', [ApartmentReservationsController::class, 'reserve']);
    $r->addRoute('POST', '/apartments/{id:\d+}/confirm', [ApartmentReservationsController::class, 'confirm']);

    $r->addRoute('GET', '/apartments/{id:\d+}/show', [ApartmentReservationsController::class, 'show']);

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
        $handler = $routeInfo[1];
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
