<?php

namespace Sirius\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface {

    /**
     * @param ServerRequestInterface $requestInterface
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface|null
     */
    public function __invoke(ServerRequestInterface $requestInterface, ResponseInterface $response, callable $next);
}