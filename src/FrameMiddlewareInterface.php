<?php

namespace Sirius\Middleware;

use Psr\Http\Message\ServerRequestInterface;

interface FrameMiddlewareInterface {

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, callable $next);
}