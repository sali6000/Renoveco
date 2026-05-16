<?php

return [
    'roles' => [
        'guest'      => [],
        'user'       => ['guest'],      // hérite de guest
        'admin'      => ['user'],       // hérite de user
        'superadmin' => ['admin'],      // hérite de admin
    ],
    'permissions' => [
        'guest' => [
            'About\AboutIndexController@index',
            'Auth\AuthLoginController@connection',
            'Auth\AuthLoginController@login',
            'Cgu\CguIndexController@index',
            'Cgu\CguPolicyController@policy',
            'Contact\ContactIndexController@index',
            'Contact\ContactIndexController@mailSend',
            'Gallery\GalleryIndexController@index',
            'Home\HomeIndexController@index',
            'Product\ProductDetailController@detail',
            'Product\ProductListController@list',
            'Services\DetailController@detail',
            'User\UserCreateController@create',
            'User\UserCreateController@store',
            'Utilities\SitemapController@index',
            // -- new-line-generate-by-make-module --
        ],
        'user' => [
            'User\ProfileController@view',
            'Order\OrderController@list',
            'Auth\AuthLoginController@logout',
        ],
        'admin'      => [
            'Admin\AdminDashboardController@index',
        ],
        'superadmin' => ['*'],
    ],
];
