<?php

use mindplay\middleman\Dispatcher;
use DTL\Freelancer\Extension\CoreExtension;
use mindplay\middleman\InteropResolver;
use DTL\Freelancer\Middleware\ControllerMiddleware;
use PhpBench\DependencyInjection\Container;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\ErrorHandler;

require_once(__DIR__ . '/../vendor/autoload.php');

ExceptionHandler::register();
ErrorHandler::register();

$container = Freelancer::getContainer();
$dispatcher = new Dispatcher(
    [
        ControllerMiddleware::class
    ],
    new InteropResolver($container)
);

$request = ServerRequestFactory::fromGlobals();
$response = $dispatcher->dispatch($request, new Response());

$emitter = new SapiEmitter();
$emitter->emit($response);