<?php
return [
    'user' => [
        'ProfileController@view',
        'OrderController@list',
        'HomeIndexController@index',
        'CguController@index',
        'HomeIndexController@index',
        'ProductController@detail',
        'ProductListController@list',
        'AuthController@logout',
    ],
    'admin' => [
        '*', // accès total
    ],
    'superadmin' => [
        '*', // accès total
    ],
    'guest' => [
        'AboutIndexController@index',
        'AdminDashboardController@index',
        'ContactIndexController@index',
        'UserIndexController@create',
        'AuthIndexController@login',
        'AuthIndexController@registerJson',
        'CguIndexController@index',
        'HomeIndexController@index',
        'ProductDetailController@detail',
        'ProductListController@list',
        'AuthLoginController@connection',
        'AuthLoginController@login',
        'UserCreateController@registerJson',
        'ContactIndexController@mailSend',
        'UserCreateController@create',
        'SitemapController@index',
        'GalleryIndexController@index',
        'CguPolicyController@policy',
        'BonjourUnefoisController@unefois',
        'DerniereFoisController@fois',
        'VoitureDetailController@detail',
        'StevenFernandoController@fernando',
        // -- new-line-generate-by-make-module --
    ],
];
