<?php
return [
    'user' => [
        'ProfileController@view',
        'OrderController@list',
    ],
    'admin' => [
        '*', // accès total
    ],
    'guest' => [
        'HomeController@index',
        'AuthController@login',
        'Manage\ControllersController@index'
    ],
];
