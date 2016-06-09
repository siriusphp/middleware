<?php

namespace Sirius\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

/**
 * FrameRunner Middleware inspired by this article
 * http://blog.ircmaxell.com/2016/05/all-about-middleware.html
 */

class FrameRunner {

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
        $base = null;
        foreach ($middlewares as $middleware) {
            $base = $base ? $base->add($middleware) : new static($middleware);
        }
        return $base;
    }

    public function __construct(callable $middleware)
    {
        $this->middleware = $middleware;
    }

    public function setNext(FrameRunner $runner)
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
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(RequestInterface $request)
    {

        $response = call_user_func($this->middleware, $request, $this->next);

        if (!$response instanceof ResponseInterface) {
            throw new \Exception('Middleware is not returning a Response object');
        }

        return $response;
    }

}