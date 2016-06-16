<?php

namespace Sirius\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Runner
{

    /* @var runner */
    protected $next;

    protected $middleware;

    /**
     * Creates a runner based on an array of middleware
     *
     * @param array $middlewares
     * @return Runner
     */
    public static function factory(array $middlewares = array())
    {
        $runner = new static;
        foreach ($middlewares as $middleware) {
            $runner = $runner->add($middleware);
        }
        return $runner;
    }

    public function __construct(callable $middleware = null)
    {
        $this->middleware = $middleware;
    }

    /**
     * Returns a new instance of a runner (ie: immutable middleware runner)
     *
     * @param callable $middleware
     * @return Runner
     */
    public function add(callable $middleware)
    {
        $runner = new static($middleware);
        $runner->next = $this;

        return $runner;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (!$this->middleware) {
            return $response;
        }

        $result = call_user_func($this->middleware, $request, $response, $this->next);
        if (!$result instanceof ResponseInterface) {
            $result = $response;
        }

        return $result;
    }
}
