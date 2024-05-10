<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Config\Routes;
use AFTC\Utils\AFTCUtils;
use AFTC\VOs\RouteVo;
use AFTC\Enums\eRouteType;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class RouterLib
{
    public string $uri = "";
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

        new Routes($this);
        $this->setupRouter();
    }

    public function add(eRouteType $routeType, string $requestMethod, string $url, string $namespace, string $class, ?string $method = null): void
    {
        $model = new RouteVo();
        $model->index = $this->routeIndex;
        $model->routeType = $routeType;
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
        exit();
    }

    private function setupRouter(): void
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $vo) {
                $r->addRoute($vo->requestMethod, $vo->url, $vo->index);
            }
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $routeInfo = $dispatcher->dispatch($httpMethod, $this->uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                http_response_code(404);
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

                $isApi = $this->routeModel->routeType === eRouteType::API;

                if (!class_exists($class)) {
                    http_response_code(500);
                    $msg = [
                        "status" => 500,
                        "message" => "Routing Error: Namespace or Class not found [$class]"
                    ];
                    AFTCUtils::writeToLog($msg["message"]);

                    if ($isApi) {
                        header('Content-type:application/json;charset=utf-8');
                        echo json_encode($msg);
                    } else {
                        AFTCUtils::redirect(Config::$errorUrl);
                    }
                    exit();
                }

                $controller = new $class();

                if (!method_exists($controller, $this->routeModel->method)) {
                    http_response_code(500);
                    $msg = [
                        "status" => 500,
                        "message" => "Routing Error: Method not found [{$this->routeModel->method}]"
                    ];
                    AFTCUtils::writeToLog($msg["message"]);

                    if ($isApi) {
                        header('Content-type:application/json;charset=utf-8');
                        echo json_encode($msg);
                    } else {
                        AFTCUtils::redirect(Config::$errorUrl);
                    }
                    exit();
                }

                $method = $this->routeModel->method;

                if ($method !== null) {
                    $controller->$method($routeInfo[2] ?? null);
                } else {
                    $controller = new $class($routeInfo[2] ?? null);
                }
                break;
        }
    }
}