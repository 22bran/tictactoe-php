<?php
declare(strict_types=1);

use League\Plates\Engine;
use TicTacToe\Controllers\GameController;
use TicTacToe\Attributes\Route;

require_once 'vendor/autoload.php';

session_start();

if (isset($_SESSION['gameController'])) {
    /** @var GameController */
    $gameController = unserialize($_SESSION['gameController']);
} else {
    $gameController = new GameController(new Engine('/app/src/Templates'));
}

$reflectionClass = new ReflectionClass(GameController::class);
foreach ($reflectionClass->getMethods() as $reflectionMethod) {
    $attributes = $reflectionMethod->getAttributes(Route::class);
    foreach ($attributes as $attribute) {
        /** @var Route */
        $route = $attribute->newInstance();
        if (
            $route->method === ($_SERVER['REQUEST_METHOD'] ?? 'GET') && 
            $route->path === ($_SERVER['PATH_INFO'] ?? '/')) 
        {
            return $gameController->{$reflectionMethod->getName()}();
        }
    }
}

http_response_code(404);

