<?php

namespace DTL\Freelancer\Twig;

use Twig_Extension;
use Aura\Router\Generator;
use Twig_SimpleFunction;

class AuraRouterExtension extends Twig_Extension
{
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('path', function ($name, $parameters = []) {
                return $this->generator->generate($name, $parameters);
            })
        ];
    }

    public function getName()
    {
        return 'aura_router';
    }
}
