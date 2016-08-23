<?php

namespace Blekan\Middleware;

class GuestMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if ($this->container->auth->isSignedIn()) {
            return $response->withRedirect($this->container->router->pathFor('admin.home'));
        }

        return $next($request, $response);
    }
}
