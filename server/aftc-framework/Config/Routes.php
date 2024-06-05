<?php

namespace AFTC\Config;

use AFTC\Enums\eRouteType;
use AFTC\Libs\RouterLib;

class Routes
{

    public function __construct(RouterLib $router)
    {
        // $router->add method definition - leave out or set method to false will make the route just use the constructor
        //add($requestMethod, $url, $namespace, $class_name, $method = false)

        // TODO: Clean up router
        // TODO: Re-code cache capabilities to router due to updated version
        // Demo Templates

        // API
        $router->add("GET", "/api/status", "AFTC\Controllers\Api", "ApiStatus","get");


        // VIEWS
        $router->add("GET", "/", "AFTC\Controllers\Pages", "TwigView","get");
        // $router->add("GET", "/php", "AFTC\Controllers\Pages", "PhpView","get");
        // $router->add("POST", "/php", "AFTC\Controllers\Pages", "PhpView","post");



        // EG
        // $router->add("GET", "/api/status2", "AFTC\Controllers\Api", "ApiParamTest","Post");
        // $router->add("PUT", "/api/status2", "AFTC\Controllers\Api", "ApiParamTest","Put");
        // $router->add("PATCH", "/api/status2", "AFTC\Controllers\Api", "ApiParamTest","Patch");
        // $router->add("DELETE", "/api/status2", "AFTC\Controllers\Api", "ApiParamTest","Delete");
        // $router->add("GET", "/", "AFTC\Controllers\Pages", "TwigView","get");
        // $router->add("POST", "/", "AFTC\Controllers\Pages", "TwigView","post");
        // $router->add("GET", "/php", "AFTC\Controllers\Pages", "PhpView","get");
        // $router->add("POST", "/php", "AFTC\Controllers\Pages", "PhpView","post");


        // Auth
        // $router->add("POST", "/api/auth/login", "AFTC\Controllers\Api", "AuthAFTC", "login");
        // $router->add("GET", "/api/auth/logged-in", "AFTC\Controllers\Api", "AuthAFTC", "isLoggedIn");
        // $router->add("GET", "/api/auth/refresh-token", "AFTC\Controllers\Api", "AuthAFTC", "getRefreshToken");
        // $router->add("GET", "/api/auth/logout", "AFTC\Controllers\Api", "AuthAFTC", "logout");
        // $router->add("POST", "/api/auth/validate-token", "AFTC\Controllers\Api", "AuthAFTC", "isTokenValid");


    }
}
