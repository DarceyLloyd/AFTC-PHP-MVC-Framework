<?php

namespace AFTC\VOs;

use AFTC\Enums\eRouteType;

class RouteVo
{
    public int $index;
    public eRouteType $routeType;

    public string $requestMethod;
    public string $url;
    public string $namespace;
    public $class;
    public string $method;

}