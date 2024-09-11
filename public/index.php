<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use PageAnalyzer\Connection;
use PageAnalyzer\InitDatabase;
use PageAnalyzer\Table;

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});


$container->set('connection', function () {
    return new Connection();
});

$pdo = $container->get('connection')->connect();
$init = new InitDatabase($pdo);
$init->initTables();

$container->set('table', function () use ($pdo) {
    return new Table($pdo);
});



$app = AppFactory::createFromContainer($container);
$router = $app->getRouteCollector()->getRouteParser();

$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);



$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'index.phtml');
})->setName('index');

$app->post('/urls', function ($request, $response) use ($router, $pdo) {
    $urlData = $request->getParsedBodyParam('url');
    $create = 'now';
    $this->get('table')->insert($urlData['name'], $create);
    return $this->get('renderer')->render($response, 'index.phtml');
})->setName('index.urls');

$app->run();
