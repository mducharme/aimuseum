<?php

use AiMuseum\Services\ArtworkLoader;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

/* If using PHP's built-in server, return false to skip existing files on the filesystem. */
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$config = require __DIR__ . '/../config/config.php';

$app->get('/artworks', function (Request $request, Response $response, $args) use ($config) {

    $artworkLoader = new ArtworkLoader($config['FOLDER_DATA']);
    $artworks = $artworkLoader->getListAll();

    // Convert the artworks array to JSON and return it in the response body
    $response->getBody()->write(json_encode($artworks));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'public, max-age=' . $config['CACHE_BROWSER_ARTWORK_LIST_TTL'])
        ->withHeader('Expires', gmdate('D, d M Y H:i:s', time() + $config['CACHE_BROWSER_ARTWORK_LIST_TTL']) . ' GMT');
});

$app->get('/artwork/{id}', function (Request $request, Response $response, $args) use ($config) {
    $id = filter_var($args['id'], FILTER_SANITIZE_STRING);
    if (empty($id) || preg_match('/[^\w\-.]/', $id)) {
        return $response->withStatus(404);
    }

    $artworkLoader = new ArtworkLoader($config['FOLDER_DATA']);
    $artwork = $artworkLoader->getPublicData($id);
    $response->getBody()->write(json_encode($artwork));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'public, max-age=' . $config['CACHE_BROWSER_ARTWORK_INFO_TTL'])
        ->withHeader('Expires', gmdate('D, d M Y H:i:s', time() + $config['CACHE_BROWSER_ARTWORK_INFO_TTL']) . ' GMT');
});
$app->run();
