<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Config\Routes;
use AFTC\Controllers\Api\UtilsAFTC;
use AFTC\Utils\AFTCUtils;
use AFTC\VOs\RouteVo;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class RouterLib
{
    public string $uri = "";
    public bool $isApi = false;
    private int $routeIndex = 0;
    private array $routes = [];
    private ?RouteVo $routeModel = null;

    public function __construct()
    {
        // Get URI
        $this->uri = $_SERVER['REQUEST_URI'];
        if (($pos = strpos($this->uri, '?')) !== false) {
            $this->uri = substr($this->uri, 0, $pos);
        }
        $this->uri = rawurldecode($this->uri);

        // isAPI
        Config::$isApi = str_starts_with($this->uri, '/api/');

        // Setup Router
        new Routes($this);
        $this->setupRouter();
    }

    public function add(string $requestMethod, string $url, string $namespace, string $class, ?string $method = null): void
    {
        $model = new RouteVo();
        $model->index = $this->routeIndex;
        $model->requestMethod = $requestMethod;
        $model->url = $url;
        $model->namespace = $namespace;
        $model->class = $class;
        $model->method = $method;

        $this->routeIndex++;
        array_push($this->routes, $model);
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
        http_response_code(404);
        AFTCUtils::redirect(Config::$pageNotFoundUrl);
        // exit();
    }

    private function setupRouter(): void
    {
        $FRCacheData = [
            'cacheFile' => Config::$routerCacheFolder . '/route.cache',
            'cacheDisabled' => !Config::$routerCacheEnabled,
        ];

        // AFTCUtils::dumpJson($FRCacheData);
        // AFTCUtils::writeToLog($FRCacheData);

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
                http_response_code(405);
                header('Content-type:application/json;charset=utf-8');
                echo json_encode([
                    "status" => 405,
                    "message" => "Method Not Allowed"
                ]);
                break;

            case Dispatcher::FOUND:
                $routeIndex = $routeInfo[1];
                $this->routeModel = $this->getRoute($routeIndex);
                $class = $this->routeModel->namespace . "\\" . $this->routeModel->class;

                if (!class_exists($class)) {
                    $this->handleRoutingError("Namespace or Class not found [$class]");
                }

                $controller = new $class();

                if ($this->routeModel->method !== null && !method_exists($controller, $this->routeModel->method)) {
                    $this->handleRoutingError("Method not found [{$this->routeModel->method}]");
                }

                $method = $this->routeModel->method;
                $params = $routeInfo[2] ?? [];

                if ($method !== null) {
                    $controller->$method($params);
                } else {
                    $controller = new $class($params);
                }
                break;
        }
    }

    private function handleRoutingError(string $message): void
    {
        http_response_code(500);
        $msg = [
            "status" => 500,
            "message" => "Routing Error: $message"
        ];
        AFTCUtils::writeToLog($msg["message"]);

        if (Config::$isApi) {
            header('Content-type:application/json;charset=utf-8');
            echo json_encode($msg);
        } else {
            AFTCUtils::redirect(Config::$errorUrl);
        }
        exit();
    }
}
