<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Config\Routes;
use AFTC\Controllers\Api\UtilsAFTC;
use AFTC\Utils\AFTCUtils;
use AFTC\VOs\RouteVo;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use JetBrains\PhpStorm\NoReturn;

class RouterLib
{
    public string $uri = "";
    public bool $isApi = false;
    private int $routeIndex = 0;
    private array $routes = [];
    private ?RouteVo $routeModel = null;

    public function __construct()
    {
        $this->uri = $this->getUri();
        Config::$isApi = str_starts_with($this->uri, '/api/');
        new Routes($this);
        $this->setupRouter();
    }

    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        return rawurldecode($uri);
    }

    public function add(string $requestMethod, string $url, string $namespace, string $class, ?string $method = null): void
    {
        $model = new RouteVo();
        $model->index = $this->routeIndex++;
        $model->requestMethod = $requestMethod;
        $model->url = $url;
        $model->namespace = $namespace;
        $model->class = $class;
        $model->method = $method;

        $this->routes[] = $model;
    }

    public function getRoute(int $index): ?RouteVo
    {
        foreach ($this->routes as $vo) {
            if ($vo->index === $index) {
                return $vo;
            }
        }

        $this->respondWith404();
        return null;
    }

    public function getRouteByUri(string $uri): ?RouteVo
    {
        foreach ($this->routes as $vo) {
            if ($vo->url === $uri) {
                return $vo;
            }
        }

        $this->respondWith404();
        return null;
    }

    private function respondWith404(): void
    {
        $jsonResponse = [
            "status" => 404,
            "message" => "Not found"
        ];
        $this->handleErrorResponse(404, $jsonResponse, Config::$pageNotFoundUrl, $jsonResponse["message"]);
    }

    private function setupRouter(): void
    {
        $FRCacheData = [
            'cacheFile' => Config::$routerCacheFolder . '/route.cache',
            'cacheDisabled' => !Config::$routerCacheEnabled,
        ];

        $dispatcher = \FastRoute\cachedDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $vo) {
                $r->addRoute($vo->requestMethod, $vo->url, $vo->index);
            }
        }, $FRCacheData);

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $routeInfo = $dispatcher->dispatch($httpMethod, $this->uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $this->respondWith404();
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $this->respondWith405();
                break;

            case Dispatcher::FOUND:
                $this->handleFoundRoute($routeInfo);
                break;
        }
    }

    private function respondWith405(): void
    {
        $jsonResponse = [
            "status" => 405,
            "message" => "Method not allowed"
        ];
        $this->handleErrorResponse(405, $jsonResponse, Config::$pageMethodNotAllowed, $jsonResponse["message"]);
    }

    private function handleFoundRoute(array $routeInfo): void
    {
        $routeIndex = $routeInfo[1];
        $this->routeModel = $this->getRoute($routeIndex);

        $class = $this->routeModel->namespace . "\\" . $this->routeModel->class;

        if (!class_exists($class)) {
            $jsonResponse = [
                "status" => 500,
                "message" => "Namespace or Class not found [$class]"
            ];
            $this->handleErrorResponse(500, $jsonResponse, Config::$pageError, $jsonResponse["message"]);
        }

        $controller = new $class();
        $method = $this->routeModel->method;
        $params = $routeInfo[2] ?? [];

        if ($method !== null) {
            if (!method_exists($controller, $method)) {
                $jsonResponse = [
                    "status" => 500,
                    "message" => "Method not found [$method]"
                ];
                $this->handleErrorResponse(500, $jsonResponse, Config::$pageError, $jsonResponse["message"]);
            }
            $controller->$method($params);
        } else {
            new $class($params);
        }
    }

    #[NoReturn]
    private function handleErrorResponse(int $httpCode, array $jsonResponse, string $redirectTo = "", string $errorLogMessage = ""): void
    {
        http_response_code($httpCode);
        if (Config::$isApi) {
            header('Content-type: application/json;charset=utf-8');
            echo json_encode($jsonResponse);
        } else {
            AFTCUtils::redirect($redirectTo);
        }

        if ($errorLogMessage !== "") {
            AFTCUtils::writeToLog($errorLogMessage);
        }
        exit();
    }
}
