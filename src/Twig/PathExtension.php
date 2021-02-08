<?php

namespace App\Twig;

use Slim\Interfaces\RouteParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PathExtension extends AbstractExtension
{
    private RouteParserInterface $routeParser;

    public function __construct(RouteParserInterface $route)
    {
        $this->routeParser = $route;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'path']),
        ];
    }

    public function path(string $routeName, array $data = [], array $queryParams = [])
    {
        return $this->routeParser->urlFor($routeName, $data, $queryParams);
    }
}
