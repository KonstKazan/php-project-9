<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;
use Valitron\Validator as V;
use Carbon\Carbon;
use Slim\Middleware\MethodOverrideMiddleware;
use PageAnalyzer\Connection;
use PageAnalyzer\InitDatabase;
use PageAnalyzer\Table;

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

$container->set('flash', function () {
    return new \Slim\Flash\Messages();
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
    $v = new Valitron\Validator($urlData);
    $v->rule('email', 'name');
    if ($v->validate()) {
        $create = Carbon::now();
        $this->get('table')->insert($urlData['name'], $create);
        return $this->get('renderer')->render($response, 'index.phtml');
    } else {
        $params = [
            'urlData' => $urlData,
            'errors' => 'Некорректный url'
        ];
        return $this->get('renderer')->render($response->withStatus(422), 'index.phtml', $params);
    }
})->setName('index.urls');

$app->run();
