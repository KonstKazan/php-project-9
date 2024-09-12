<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;
use Valitron\Validator;
use Carbon\Carbon;
use Slim\Middleware\MethodOverrideMiddleware;
use PageAnalyzer\Connection;
use PageAnalyzer\InitDatabase;
use PageAnalyzer\Table;

$conn = new Connection();
$pdo = $conn->connect();
$conn->initTables($pdo);

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

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

$app->post('/urls', function ($request, $response) use ($router) {
    $urlData = $request->getParsedBodyParam('url');
    $v = new Valitron\Validator($urlData);
    $v->rule('required', 'name')->message('URL не должен быть пустым');
    $v->rule('url', 'name')->message('Некорректный URL');
    if ($v->validate()) {
        $create = Carbon::now();
        $id = $this->get('table')->insert($urlData['name'], $create);
        return $response->withRedirect("/urls/{$id}");
    } else {
        $params = [
            'urlData' => $urlData,
            'errors' => $v->errors()
        ];
        return $this->get('renderer')->render($response->withStatus(422), 'index.phtml', $params);
    }
});

$app->get('/urls', function ($request, $response) {
    $urls = $this->get('table')->selectAll();
    $params = [
        'urls' => $urls
    ];
    return $this->get('renderer')->render($response, 'urls.phtml', $params);
})->setName('urls');

$app->get('/urls/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $url = $this->get('table')->select($id);
    $params = [
        'url' => $url
    ];
    return $this->get('renderer')->render($response, 'show.phtml', $params);
})->setName('show');

$app->run();
