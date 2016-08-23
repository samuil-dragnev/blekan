<?php

return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'view' => [
            'template_path' => __DIR__.'/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'blekan-app',
            'path' => __DIR__.'/../logs/app.log',
        ],

        // Database settings
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'blekan',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ],
        // Email settings
        'email' => [
            'host' => 'localhost',
            'is_smtp' => true,
            'security_type' => 'tsl',
            'port' => 25,
            'username' => '',
            'password' => ''
    ],
];
