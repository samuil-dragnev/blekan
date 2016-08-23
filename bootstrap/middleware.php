<?php
// Application middleware

$app->add(new \Slim\HttpCache\Cache('public', 86400));
$app->add(new \Blekan\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \Blekan\Middleware\OldInputMiddleware($container));
$app->add(new \Blekan\Middleware\CsrfViewMiddleware($container));
$app->add(function ($request, $response, $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));

        return $response->withRedirect((string) $uri, 301);
    }

    return $next($request, $response);
});
$app->add($container->csrf);
