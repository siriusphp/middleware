<?php

namespace Sirius\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class Runner {

    /* @var runner */
    protected $next;

    protected $middleware;

    /**
     * Creates a runner based on an array of middleware
     *
     * @param array $middlewares
     * @return Runner
     */
    static public function factory(array $middlewares = array())
    {
        $base = new static;
        foreach ($middlewares as $middleware) {
            $base = $base->add($middleware);
        }
        return $base;
    }

    public function __construct(callable $middleware = null)
    {
        $this->middleware = $middleware;
    }

    public function setNext(Runner $runner)
    {
        $this->next = $runner;
    }

    /**
     * @param callable $middleware
     * @return callable
     */
    public function add(callable $middleware)
    {
        $runner = new static($middleware);
        $runner->setNext($this);

        return $runner;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response)
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