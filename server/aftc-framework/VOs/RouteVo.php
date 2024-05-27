<?php

namespace AFTC\VOs;

class RouteVo
{
    public int $index;

    public string $requestMethod;
    public string $url;
    public string $namespace;
    public $class;
    public string $method;

}