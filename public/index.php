<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;
use Valitron\Validator;
use Carbon\Carbon;
use Slim\Middleware\MethodOverrideMiddleware;
use PageAnalyzer\Connection;
use PageAnalyzer\Urls;
use PageAnalyzer\UrlChecks;
use DiDom\Document;
use GuzzleHttp\Exception\TransferException;
use Slim\Flash\Messages;

session_start();

$conn = new Connection();
$pdo = $conn->connect();
$conn->initTables($pdo);

$container = new Container();
$container->set('renderer', function () {
    return new PhpRenderer(__DIR__ . '/../templates');
});

$container->set('flash', function () {
    return new Messages();
});

$container->set('urls', function () use ($pdo) {
    return new Urls($pdo);
});

$container->set('urls_checks', function () use ($pdo) {
    return new UrlChecks($pdo);
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

    if ($this->get('urls')->getId($urlData['name'])) {
        $id = $this->get('urls')->getId($urlData['name']);
        $this->get('flash')->addMessage('success', 'Страница уже существует');
        return $response->withRedirect($router->urlFor('show', ['id' => $id]));
    } elseif ($v->validate()) {
        $create = Carbon::now();
        $id = $this->get('urls')->create($urlData['name'], $create);
        $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
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
    $urls = $this->get('urls')->getAll();
    $params = [
        'urls' => $urls
    ];
    return $this->get('renderer')->render($response, 'urls.phtml', $params);
})->setName('urls');

$app->get('/urls/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $url = $this->get('urls')->get($id);
    $check = $this->get('urls_checks')->getAll($id);
    $messages = $this->get('flash')->getMessages();
    $params = [
        'url' => $url,
        'check' => $check,
        'flash' => $messages
    ];
    return $this->get('renderer')->render($response, 'show.phtml', $params);
})->setName('show');

$app->post('/urls/{id}/checks', function ($request, $response, $args) use ($router) {
    $id = $args['id'];
    $create = Carbon::now();
    $url = $this->get('urls')->get($id);
    $urlName = $url['name'];
    $client = new GuzzleHttp\Client();
    try {
        $res = $client->request('GET', "$urlName");
    } catch (TransferException) {
        $this->get('flash')->addMessage('error', 'Произошла ошибка при проверке, не удалось подключиться');
        return $response->withRedirect($router->urlFor('show', ['id' => $id]));
    }
    $status = $res->getStatusCode();

    $document = new Document($urlName, true);
    $findDesc = $document->find('meta[name=description]');
    if ($findDesc[0]->hasAttribute('content')) {
        $description = $findDesc[0]->getAttribute('content');
    }


    $findH = $document->find('h1');
    $h = optional($findH[0])->text();

    $findTitle = $document->find('title');
    $title = optional($findTitle[0])->text();

    $this->get('urls_checks')->create($id, $status, $h, $title, $description, $create);
    $this->get('flash')->addMessage('success', 'Страница успешно проверена');
    return $response->withRedirect($router->urlFor('show', ['id' => $id]));
});

$app->run();
