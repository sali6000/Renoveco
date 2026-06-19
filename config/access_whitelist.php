<?php

use Src\Modules\About\Interface\Http\Controllers\AboutIndexController;
use Src\Modules\Auth\Interface\Http\Controllers\AuthLoginController;
use Src\Modules\Cgu\Interface\Http\Controllers\CguIndexController;
use Src\Modules\Cgu\Interface\Http\Controllers\CguPolicyController;
use Src\Modules\Contact\Interface\Http\Controllers\ContactIndexController;
use Src\Modules\Home\Interface\Http\Controllers\HomeIndexController;
use Src\Modules\Admin\Product\Interface\Http\Controllers\ProductController as AdminProductController;
use Src\Modules\Product\Interface\Http\Controllers\ProductDetailController;
use Src\Modules\Product\Interface\Http\Controllers\ProductListController;
use Src\Modules\Services\Interface\Http\Controllers\DetailController;
use Src\Modules\User\Interface\Http\Controllers\UserCreateController;
use Src\Modules\Utilities\Interface\Http\Controllers\SitemapController;

return [

    'roles' => [
        'guest'      => [],
        'user'       => ['guest'],
        'admin'      => ['user'],
        'superadmin' => ['admin'],
    ],

    'permissions' => [

        'guest' => [
            AboutIndexController::class    . '@index',
            AuthLoginController::class     . '@connection',
            AuthLoginController::class     . '@login',
            AuthLoginController::class     . '@logout',
            CguIndexController::class      . '@index',
            CguPolicyController::class     . '@policy',
            ContactIndexController::class  . '@index',
            ContactIndexController::class  . '@mailSend',
            HomeIndexController::class     . '@index',
            ProductDetailController::class . '@detail',
            ProductListController::class   . '@list',
            DetailController::class        . '@detail',
            UserCreateController::class    . '@create',
            UserCreateController::class    . '@store',
            SitemapController::class       . '@index',
            // -- new-line-generate-by-make-module --
        ],

        'user' => [
            // ...
        ],

        'admin' => [
            AdminProductController::class  . '@list',
            AdminProductController::class  . '@create',
        ],

        'superadmin' => ['*'],
    ],
];
