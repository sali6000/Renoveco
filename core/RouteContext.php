<?php

namespace Core;

class RouteContext
{
    public static string $controller;
    public static string $action;

    public static function set(string $controller, string $action): void
    {
        self::$controller = $controller;
        self::$action = $action;
    }

    public static function get(): string
    {
        return self::$controller . '@' . self::$action;
    }
}
