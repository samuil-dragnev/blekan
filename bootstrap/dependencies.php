<?php
// DIC configuration
use Respect\Validation\Validator as v;
use Blekan\Models\General;

$container = $app->getContainer();

// Auth
$container['auth'] = function ($c) {
    return new \Blekan\Auth\Auth();
};

// view renderer
$container['view'] = function ($c) {
    $settings = $c->get('settings')['view'];
    $view = new Slim\Views\Twig($settings['template_path'], [
      'cache' => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
      $c->router,
      $c->request->getUri()
    ));

    $view->addExtension(new \nochso\HtmlCompressTwig\Extension(true));

    $view->getEnvironment()->addGlobal('auth', [
      'isSignedIn' => $c->auth->isSignedIn(),
      'currentUser' => $c->auth->getCurrentUser(),
    ]);

    $view->getEnvironment()->addGlobal('flash', $c->flash);
    $view->getEnvironment()->addGlobal('general', $general = General::find(1));

    return $view;
};

$container['HomeController'] = function ($c) {
    return new \Blekan\Controllers\HomeController($c);
};

$container['AdminController'] = function ($c) {
    return new \Blekan\Controllers\AdminController($c);
};

$container['AuthController'] = function ($c) {
    return new \Blekan\Controllers\Auth\AuthController($c);
};

// 404 handler
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response, '404.html')->withStatus(404);
    };
};

// 405 handler
$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $c['view']->render($response, '405.html')->withStatus(405);
    };
};

// Database
$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container->get('settings')['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($c) use ($capsule) {
    return $capsule;
};

// Email
$container['mailer'] = function ($c) {
    $mailer = new PHPMailer();

    $settings = $c->get('settings')['email'];
    $mailer->Host = $settings['host'];
    $mailer->SMTPAuth = $settings['is_smtp'];
    $mailer->SMTPSecure = $settings['security_type'];
    $mailer->Port = $settings['port'];
    $mailer->Username = $settings['username'];
    $mailer->Password = $settings['password'];
    $mailer->isHTML(true);
    $mailer->CharSet = 'UTF-8';

    return new \Blekan\Mail\Mailer($c->view, $mailer);
};

// Validation
$container['validator'] = function ($c) {
    return new Blekan\Validation\Validator();
};

v::with('Blekan\\Validation\\Rules\\');

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));

    return $logger;
};

// CSRF
$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard();
};

// HTTP Cache
$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

// Flash
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};
