# Sirius Middleware

[![Source Code](http://img.shields.io/badge/source-siriusphp/middleware-blue.svg?style=flat-square)](https://github.com/siriusphp/middleware)
[![Latest Version](https://img.shields.io/packagist/v/siriusphp/middleware.svg?style=flat-square)](https://github.com/siriusphp/middleware/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/siriusphp/middleware/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/siriusphp/middleware/master.svg?style=flat-square)](https://travis-ci.org/siriusphp/middleware)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/siriusphp/middleware.svg?style=flat-square)](https://scrutinizer-ci.com/g/siriusphp/middleware/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/siriusphp/middleware.svg?style=flat-square)](https://scrutinizer-ci.com/g/siriusphp/middleware)
[![Total Downloads](https://img.shields.io/packagist/dt/siriusphp/middleware.svg?style=flat-square)](https://packagist.org/packages/siriusphp/middleware)


## Regular middleware

```php

$middlewares = array();

$middlewares[] = function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
    // do your thing
    return $response;
};

$middlewares[] = function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
    // do your thing
    return $response;
};

$runner = Sirius\Middleware\Runner::factory($middlewares);

$response = $runner(Zend\Diactoros\ServerRequestFactory::fromGlobals(), new Zend\Diactoros\Response);

```

The middleware runners are immutable so adding a new middleware returns a new runner. If you want to create the runner by hand 

```php

// this is WRONG!!!!!!!!!
$runner = new Sirius\Middleware\Runner;
$runner->add(function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
    // do your thing
    return $response;
});
$runner->add(function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
    // do your thing
    return $response;
});


// this is CORRECT
$runner =  (new Sirius\Middleware\Runner)
            ->add(function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
              // do your thing
              return $response;
            })
            ->add(function(RequestInterface $request, ResponseInterface $response, callable $next = null) {
              // do your thing
              return $response;
            });

```

## Frame middleware

Inspired by this article http://blog.ircmaxell.com/2016/05/all-about-middleware.html

```php

$middlewares = array();

// first middleware in the stack must return an response
$middlewares[] = function(RequestInterface $request, callable $next = null) {
    return new Zend\Diactoros\Response;
};

$middlewares[] = function(RequestInterface $request, callable $next = null) {
    // do your thing
    return $response;
};

$middlewares[] = function(RequestInterface $request, callable $next = null) {
    // do your thing
    return $response;
};

$runner = Sirius\Middleware\FrameRunner::factory($middlewares);

$response = $runner(Zend\Diactoros\ServerRequestFactory::fromGlobals());

```
