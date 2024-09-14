<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;
use Valitron\Validator;
use Carbon\Carbon;
use Slim\Middleware\MethodOverrideMiddleware;
use PageAnalyzer\Connection;
use PageAnalyzer\Table;

session_start();

$conn = new Connection();
$pdo = $conn->connect();
$conn->initTables($pdo);

$container = new Container();
$container->set('renderer', function () {
    return new PhpRenderer(__DIR__ . '/../templates');
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

    $v = new Validator($urlData);
    $v->rule('required', 'name')->message('URL не должен быть пустым');
    $v->rule('url', 'name')->message('Некорректный URL');

    if ($this->get('table')->getId($urlData['name'])) {
        $id = $this->get('table')->getId($urlData['name']);
        $this->get('flash')->addMessage('success', 'Страница уже существует');
        return $response->withRedirect($router->urlFor('show', ['id' => $id]));
    } elseif ($v->validate()) {
        $create = Carbon::now();
        $id = $this->get('table')->insert($urlData['name'], $create);
        $this->get('flash')->addMessage('success', 'Страница была успешно добавлена');
        return $response->withRedirect($router->urlFor('show', ['id' => $id]));
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
    $messages = $this->get('flash')->getMessages();
    $params = [
        'url' => $url,
        'flash' => $messages
    ];
    return $this->get('renderer')->render($response, 'show.phtml', $params);
})->setName('show');

$app->run();
