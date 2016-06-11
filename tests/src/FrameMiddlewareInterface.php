<?php

namespace Sirius\Middleware;

use Psr\Http\Message\ServerRequestInterface;

interface FrameMiddlewareInterface {

    /**
     * @param ServerRequestInterface $requestInterface
     * @param callable $next
     * @return ResponseInterface|null
     */
    public function __invoke(ServerRequestInterface $requestInterface, callable $next);
}