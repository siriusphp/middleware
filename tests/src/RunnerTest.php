<?php

namespace Sirius\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class RunnerTest extends \PHPUnit_Framework_TestCase
{

    /* @var Runner */
    protected $runner;

    /* @var RequestInterface */
    protected $request;

    /* @var ResponseInterface */
    protected $response;

    protected $middleware_a;
    protected $middleware_b;

    public function setUp()
    {
        $this->runner = new Runner;

        $this->request = new ServerRequest();

        $this->response = new Response();

        $this->middleware_a = function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
            return $next($request->withAttribute('dodge', 'wow! such win!'), $response);
        };

        $this->middleware_b = function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
            $response = $next($request, $response);
            $response->getBody()->write($request->getAttribute('dodge'));
            return $response;
        };
    }

    public function test_empty_runner() {

        $runner = $this->runner;
        $response = $runner($this->request, $this->response);

        $this->assertEquals($response, $this->response);

    }

    public function test_middleware() {
        $runner = $this->runner
            ->add($this->middleware_b)
            ->add($this->middleware_a);

        /* @var $response \Zend\Diactoros\Response*/
        $response = $runner($this->request, $this->response);

        $this->assertEquals('wow! such win!', (string)$response->getBody());
    }

    public function test_middleware_that_does_not_return_a_response()
    {
        $runner = $this->runner->add(function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
            return '5';
        });

        $response = $runner($this->request, $this->response);

        $this->assertEquals($response, $this->response);
    }

    public function test_the_factory_method()
    {
        $runner = Runner::factory(array(
            $this->middleware_b,
            $this->middleware_a
        ));

        /* @var $response \Zend\Diactoros\Response*/
        $response = $runner($this->request, $this->response);

        $this->assertEquals('wow! such win!', (string)$response->getBody());
    }

}