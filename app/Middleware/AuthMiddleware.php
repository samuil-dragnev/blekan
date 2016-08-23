<?php

namespace Blekan\Middleware;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (!$this->container->auth->isSignedIn()) {
            $this->container->flash->addMessage('error', 'You are not signed in!');

            return $response->withRedirect($this->container->router->pathFor('signin'));
        }

        return $next($request, $response);
    }
}
