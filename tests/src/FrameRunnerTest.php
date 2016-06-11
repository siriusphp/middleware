<?php

namespace Sirius\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class FrameRunnerTest extends \PHPUnit_Framework_TestCase
{

    /* @var FrameRunner */
    protected $runner;

    /* @var ServerRequestInterface */
    protected $request;

    protected $middleware_a;
    protected $middleware_b;

    public function setUp()
    {
        $this->runner = new FrameRunner(function(ServerRequestInterface $request) {
            $response = new Response();
            $response->getBody()->write('start');
            return $response;
        });

        $this->request = new ServerRequest();

        $this->middleware_a = function(ServerRequestInterface $request, callable $next) {
            return $next($request->withAttribute('dodge', 'wow! such win!'));
        };

        $this->middleware_b = function(ServerRequestInterface $request, callable $next) {
            /* @var $response Response */
            $response = $next($request);
            $response->getBody()->rewind();
            $response->getBody()->write($request->getAttribute('dodge'));
            return $response;
        };
    }

    public function test_empty_runner() {

        $runner = $this->runner;
        $response = $runner($this->request);

        $this->assertEquals('start', (string)$response->getBody());

    }

    public function test_middleware() {
        $runner = $this->runner
            ->add($this->middleware_b)
            ->add($this->middleware_a);

        /* @var $response \Zend\Diactoros\Response*/
        $response = $runner($this->request);

        $this->assertEquals('wow! such win!', (string)$response->getBody());
    }

    public function test_exception_thrown_when_middleware_does_not_return_a_response()
    {

        $this->setExpectedException('Exception');

        $runner = $this->runner->add(function(ServerRequestInterface $request, callable $next) {
            return '5';
        });

        $runner($this->request);
    }

    public function test_exception_thrown_when_first_middleware_calls_next()
    {

        $this->setExpectedException('Exception');

        $runner = new FrameRunner(function(ServerRequestInterface $request, callable $next) {
            return $next($request, function(){});
        });

        $runner($this->request);
    }

    public function test_the_factory_method()
    {
        $runner = FrameRunner::factory(array(
            function(ServerRequestInterface $request) {
               return new Response();
            },
            $this->middleware_b,
            $this->middleware_a
        ));

        /* @var $response \Zend\Diactoros\Response*/
        $response = $runner($this->request);

        $this->assertEquals('wow! such win!', (string)$response->getBody());
    }

}