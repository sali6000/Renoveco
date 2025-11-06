<?php
return [
    'user' => [
        'ProfileController@view',
        'OrderController@list',
    ],
    'admin' => [
        '*', // accès total
    ],
    'superadmin' => [
        '*', // accès total
    ],
    'guest' => [
        'CguController@index',
        'AuthController@login',
        'AdminController@index',
        'AuthController@connection',
        'HomeController@index',
        'AboutController@index',
        'ProductController@list',
        'ProductController@detail',
        'Manage\ControllersController@index'
    ],
];
