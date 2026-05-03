<?php
return [
    'user' => [
        'User\ProfileController@view',
        'Order\OrderController@list',
        'Home\HomeIndexController@index',
        'Cgu\CguController@index',
        'Home\HomeIndexController@index',
        'Product\ProductController@detail',
        'Product\ProductListController@list',
        'Auth\AuthController@logout',
    ],
    'admin' => [
        '*', // accès total
    ],
    'superadmin' => [
        '*', // accès total
    ],
    'guest' => [
        'About\AboutIndexController@index',
        'Admin\AdminDashboardController@index',
        'Contact\ContactIndexController@index',
        'User\UserIndexController@create',
        'Auth\AuthIndexController@login',
        'Auth\AuthIndexController@registerJson',
        'Cgu\CguIndexController@index',
        'Home\HomeIndexController@index',
        'Product\ProductDetailController@detail',
        'Product\ProductListController@list',
        'Auth\AuthLoginController@connection',
        'Auth\AuthLoginController@login',
        'User\UserCreateController@registerJson',
        'Contact\ContactIndexController@mailSend',
        'User\UserCreateController@create',
        'Utilities\SitemapController@index',
        'Gallery\GalleryIndexController@index',
        'Cgu\CguPolicyController@policy',
        'Services\DetailController@detail',
        // -- new-line-generate-by-make-module --
    ],
];
