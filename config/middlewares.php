<?php

namespace Config;

use Core\Middleware\AccessControlMiddleware;
use Core\Middleware\AdminMiddleware;
use Core\Middleware\AuthMiddleware;
use Core\Middleware\LoggerMiddleware;
use Core\Middleware\MaintenanceMiddleware;
use Core\Middleware\SecurityHeaderMiddleware;

return [
    '*@*' => [
        LoggerMiddleware::class,
        MaintenanceMiddleware::class,
        AccessControlMiddleware::class,
        SecurityHeaderMiddleware::class
    ],
    'ProductController@create' => [AuthMiddleware::class],
    'OrderController@*' => [AuthMiddleware::class],
    'AdminController@*' => [AuthMiddleware::class, AdminMiddleware::class],
    'AdminController@dashboard' => [AuthMiddleware::class, AdminMiddleware::class],
];
