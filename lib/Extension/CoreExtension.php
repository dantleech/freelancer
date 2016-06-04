<?php

namespace DTL\Freelancer\Extension;

use PhpBench\DependencyInjection\ExtensionInterface;
use PhpBench\DependencyInjection\Container;
use Twig_Environment;
use Twig_Loader_Filesystem;
use DTL\Freelancer\Middleware\ControllerMiddleware;
use DTL\Freelancer\Action\DashboardAction;
use Aura\Router\RouterContainer;
use Aura\Router\Matcher;
use Aura\Router\Map;
use Aura\Router\Generator;
use DTL\Freelancer\Twig\AuraRouterExtension;
use Doctrine\Orm\EntityManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use DTL\Freelancer\Freelancer;
use DTL\Freelancer\Console\Application;
use DTL\Freelancer\Console\Command\DbalMigrateCommand;

class CoreExtension implements ExtensionInterface
{
    public function load(Container $container)
    {
        $this->loadMiddlewares($container);
        $this->loadTwig($container);
        $this->loadRouter($container);
        $this->loadActions($container);
        $this->loadDbal($container);
        $this->loadConsole($container);
    }

    public function build(Container $container)
    {
        $twig = $container->get(Twig_Environment::class);
        foreach (array_keys($container->getServiceIdsForTag('twig.extension')) as $serviceId) {
            $twig->addExtension($container->get($serviceId));
        }

        // TODO: This is criminally inefficient.
        $application = $container->get(Application::class);
        foreach (array_keys($container->getServiceIdsForTag('console.command')) as $serviceId) {
            $application->add($container->get($serviceId));
        }
    }

    public function getDefaultConfig()
    {
        return [
            'debug' => true,
            'twig.views.path' => __DIR__ . '/../../views',
            'cache_path' => __DIR__ . '/../../var/cache',
            'dbal.path' => __DIR__ . '/../../var/freelancer.db'
        ];
    }

    private function loadRouter(Container $container)
    {
        $container->register(RouterContainer::class, function ($container) {
            return new RouterContainer();
        });

        $container->register(Map::class, function ($container) {
            return $container->get(RouterContainer::class)->getMap();
        });

        $container->register(Matcher::class, function ($container) {
            return $container->get(RouterContainer::class)->getMatcher();
        });

        $container->register(Generator::class, function ($container) {
            return $container->get(RouterContainer::class)->getGenerator();
        });
    }

    private function loadTwig(Container $container)
    {
        $container->register(Twig_Environment::class, function ($container) {
            $loader = new Twig_Loader_Filesystem($container->getParameter('twig.views.path'));
            $twig = new Twig_Environment($loader, array(
                'cache' => $container->getParameter('cache_path') . '/twig',
                'debug' => $container->getParameter('debug')
            ));

            return $twig;
        });

        $container->register(AuraRouterExtension::class, function ($container) {
            return new AuraRouterExtension($container->get(Generator::class));
        }, [ 'twig.extension' => []]);
    }

    private function loadMiddlewares(Container $container)
    {
        $container->register(ControllerMiddleware::class, function ($container) {
            return new ControllerMiddleware($container);
        });
    }

    private function loadActions(Container $container)
    {
        $container->register(DashboardAction::class, function ($container) {
            return new DashboardAction($container->get(Twig_Environment::class));
        });
    }

    private function loadDbal($container)
    {
        $container->register(Connection::class, function ($container) {
            $params = array(
                'driver' => 'pdo_sqlite',
                'path' => $container->getParameter('dbal.path'),
            );

            return DriverManager::getConnection($params);
        });
    }

    private function loadConsole($container)
    {
        $container->register(Application::class, function ($container) {
            return new Application('freelance', Freelancer::VERSION);
        });

        // commands
        $container->register(DbalMigrateCommand::class, function ($container) {
            return new DbalMigrateCommand($container->get(Connection::class));
        }, [ 'console.command' => []]);
    }
}
