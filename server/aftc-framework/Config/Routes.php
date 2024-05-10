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

        // Done
        $router->add(eRouteType::API , "GET",
            "/api/status", "AFTC\Controllers\Api", "ApiStatus","get");

        // TODO
        $router->add(eRouteType::API , "GET",
            "/api/status2", "AFTC\Controllers\Api", "ApiParamTest","Post");
        $router->add(eRouteType::API , "PUT",
            "/api/status2", "AFTC\Controllers\Api", "ApiParamTest","Put");
        $router->add(eRouteType::API , "PATCH",
            "/api/status2", "AFTC\Controllers\Api", "ApiParamTest","Patch");
        $router->add(eRouteType::API , "DELETE",
            "/api/status2", "AFTC\Controllers\Api", "ApiParamTest","Delete");


        // 75% Done
        $router->add(eRouteType::VIEW , "GET",
            "/", "AFTC\Controllers\Pages", "TwigView","get");

        // TODO
        $router->add(eRouteType::VIEW , "POST",
            "/", "AFTC\Controllers\Pages", "TwigView","post");

        // Done
        $router->add(eRouteType::VIEW , "GET",
            "/php", "AFTC\Controllers\Pages", "PhpView","get");

        // Done
        $router->add(eRouteType::VIEW , "POST",
            "/php", "AFTC\Controllers\Pages", "PhpView","post");


        if (Config::$dev === true) {
            // $router->add("GET", "/api/status", "AFTC\Controllers\Api", "StatusAFTC","test1");
            // $router->add("GET", "/api/dev/jwt-get-token", "AFTC\Controllers\Api\Dev", "DevJwt","encodeToken");
            // $router->add("GET", "/api/dev/jwt-validate-token", "AFTC\Controllers\Api\Dev", "DevJwt","validateJwtToken");
            // $router->add("GET", "/api/dev/jwt-show-payload", "AFTC\Controllers\Api\Dev", "DevJwt","showPayload");

            // $router->add("GET", "/api/dev/seed-users", "AFTC\Controllers\Api\Dev", "SeedAFTC", "seedUsers");
            // $router->add("GET", "/api/dev/seed-schools", "AFTC\Controllers\Api\Dev", "SeedAFTC", "seedSchools");
            // $router->add("GET", "/api/dev/seed-gps", "AFTC\Controllers\Api\Dev", "SeedAFTC", "seedGps");
            // $router->add("GET", "/api/dev/seed-guardians", "AFTC\Controllers\Api\Dev", "SeedAFTC", "seedGuardians");
            // $router->add("GET", "/api/dev/seed-la", "AFTC\Controllers\Api\Dev", "SeedAFTC", "seedLocalAuthorities");

            // $router->add("GET", "/api/dev/seed-cases", "AFTC\Controllers\Api\Dev", "SeedAFTC", "seedCases");
            // $router->add("GET", "/api/dev/generate-case-folders", "AFTC\Controllers\Api\Dev", "SeedAFTC", "generateCaseFolders");
            // $router->add("GET", "/api/dev/get-case-files/{case_id:\d+}", "AFTC\Controllers\Api\Dev", "DevAFTC", "getCaseFiles");
            // $router->add("GET", "/api/cases/file/download/{id:\d+}/{file}/{token}", "AFTC\Controllers\Api", "FilesController", "downloadCaseFile");
        }

        // Auth
        // $router->add("POST", "/api/auth/login", "AFTC\Controllers\Api", "AuthAFTC", "login");
        // $router->add("GET", "/api/auth/logged-in", "AFTC\Controllers\Api", "AuthAFTC", "isLoggedIn");
        // $router->add("GET", "/api/auth/refresh-token", "AFTC\Controllers\Api", "AuthAFTC", "getRefreshToken");
        // $router->add("GET", "/api/auth/logout", "AFTC\Controllers\Api", "AuthAFTC", "logout");
        // $router->add("POST", "/api/auth/validate-token", "AFTC\Controllers\Api", "AuthAFTC", "isTokenValid");


    }
}
