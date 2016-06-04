<?php

namespace DTL\Freelancer\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use mindplay\middleman\Dispatcher;
use DTL\Freelancer\Action\DashboardAction;
use Aura\Router\RouterContainer;
use Aura\Router\Map;
use Aura\Router\Matcher;

class ControllerMiddleware
{
	private $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $map = $this->container->get(Map::class);
        $map->get('dashboard', '/', DashboardAction::class);

        $matcher = $this->container->get(Matcher::class);
        $route = $matcher->match($request);
        $action = $this->container->get($route->handler);

        return $action($request, $response, $next);
	}
}
