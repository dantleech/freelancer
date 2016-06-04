<?php

namespace DTL\Freelancer;

use DTL\Freelancer\Extension\CoreExtension;
use PhpBench\DependencyInjection\Container;

class Freelancer
{
    const VERSION = '0.0.1';

    public static function getContainer()
    {
        $container = new Container([
            CoreExtension::class
        ]);
        $container->init();

        return $container;
    }
}
