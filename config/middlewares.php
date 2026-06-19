<?php

namespace Config;

use Core\Middleware\AccessControlMiddleware;
use Core\Middleware\AdminMiddleware;
use Core\Middleware\AuthMiddleware;
use Core\Middleware\LoggerMiddleware;
use Core\Middleware\MaintenanceMiddleware;
use Core\Middleware\SecurityHeaderMiddleware;

use Src\Modules\Admin\Product\Interface\Http\Controllers\ProductController as AdminProductController;
// use Src\Modules\Product\Interface\Http\Controllers\ProductController as ShopProductController;


return [
    '*@*' => [
        LoggerMiddleware::class,
        MaintenanceMiddleware::class,
        AccessControlMiddleware::class,
        SecurityHeaderMiddleware::class
    ],
    AdminProductController::class . '@*' => [AuthMiddleware::class, AdminMiddleware::class],
    // ShopProductController::class  . '@*' => [],
    'OrderController@*' => [AuthMiddleware::class],
    'AdminController@*' => [AuthMiddleware::class, AdminMiddleware::class],
    'AdminController@dashboard' => [AuthMiddleware::class, AdminMiddleware::class],
];
