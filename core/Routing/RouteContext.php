<?php

namespace Core\Routing;

class RouteContext
{
    // Privé = impossible d'accéder directement
    private string $class = '';
    private string $method = 'GET';
    private string $controller = '';
    private string $action = '';
    private string $module = '';
    private array $params = [];

    private static ?self $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function setClass(string $class)
    {
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setModule(string $module): void
    {
        $this->module = $module;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setDefaultRouteContext($method, $controller, $action)
    {
        $this->setMethod($method ?? 'GET');
        $this->setController($controller);
        $this->setAction($action);
    }

    public function setRouteContext($controller, $action, $class, $params, $method)
    {
        $this->setMethod($method ?? 'GET');
        $this->setController($controller);
        $this->setAction($action);
        $this->setClass($class);
        $this->setParams($params);
    }

    public function getRouteContext(): array
    {
        return [
            'method' => $this->getMethod(),
            'controller' => $this->getController(),
            'action' => $this->getAction(),
            'class' => $this->getClass(),
            'params' => $this->getParams()
        ];
    }
}
