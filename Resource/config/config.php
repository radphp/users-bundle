<?php
return [
    'users' => [
        'loginTwigTemplate' => '@Users/login.twig',
        'authentication' => [
            'loginRedirect' => '/',
            'logoutRedirect' => '/users/login'
        ]
    ]
];
