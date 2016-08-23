<?php

namespace Blekan\Middleware;

class OldInputMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['old_input'])) {
            $this->container->view->getEnvironment()->addGlobal('old_input', $_SESSION['old_input']);
        }

        $_SESSION['old_input'] = $request->getParams();

        return $next($request, $response);
    }
}
