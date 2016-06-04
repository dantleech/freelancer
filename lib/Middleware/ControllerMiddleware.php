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
use DTL\Freelancer\Action\ClientAction;
use DTL\Freelancer\Action\ProjectAction;

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
        $map->get('client', '/{client}', ClientAction::class)
            ->tokens([
                'client' => '[A-Z]{6}'
            ]);
        $map->get('project', '/{client}/p/{project}', ProjectAction::class)
            ->tokens([
                'client' => '[A-Z]{6}',
                'project' => '[0-9]{4}[A-Z]{6}'
            ]);

        $matcher = $this->container->get(Matcher::class);
        $route = $matcher->match($request);

        if (!$route) {
            throw new \InvalidArgumentException(sprintf(
                'Could not match route for request: [%s] %s',
                $request->getMethod(),
                (string) $request->getUri()
            ));
        }
        $action = $this->container->get($route->handler);
        $request = $request->withAttribute('route', $route);

        return $action($request, $response, $next);
	}
}
