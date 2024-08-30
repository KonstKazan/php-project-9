<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);



$app->get('/', function ($request, $response, $args) {
    $params = ['id' => 'Nickkkki', 'nickname' => 'user-' . $args['id']];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->run();
