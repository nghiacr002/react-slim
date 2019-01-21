<?php

$configurations = array(
    'settings' => array(
        'appVersion' => '1.0.0',
        'releaseDate' => '01/02/2019',
        'addContentLengthHeader' => true,
        'displayErrorDetails' => true, // output error detail when running
        'determineRouteBeforeAppMiddleware' => true,
        'routerCacheFile' => false,//APP_CACHE_PATH .'routers.php',
        'module' => array(
            'basePath' => APP_SOURCE_PATH . "Modules" . DS,
            'allowToOverWriteRouter' => true,
        ),
        'router' => array(
            'cachePath' => APP_PUBLIC_PATH. "Cache" . DS,
            'enableCachingRouter' => true,
        ),
        'forceResponseEndcoding' => 'JSON',
        'mode' => 'development', // maintenance|development|production
        'security' => array(
            'random' => '0987654321qetganaksdy9qy8343lkfkalfyq893723or3!#&(#!^Lskadlsa###'
        ),
        'logger' => array(
            'path' => APP_LOG_PATH.'App'.DS.'main.log',
            'level' => Monolog\Logger::DEBUG
        ),
        'writeConsoleLogToFile' => true,
        'guard' => array(
            'headers' => [
                'X-Powered-By' => 'LegoAsync',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Accept, Origin, Authorization,Token',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
                //'Server' => 'LegoAsync-1.0' => not working ?
            ]
        ),
        'defaultContentType' => 'application/json', 
        'httpVersion' => '1.1',
        'template' => [
            'path' => APP_TEMPLATE_PATH,
            'cache' => false
        ],
        'mailer' => [
            'type' => 'smtp',
            'config' => array(
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'user' => 'hello@sample.com',
                'password' => '123456',
                'security' => 'tls'
            ),
            'from' => ['hello@sample.com' => 'From Mail Title']
        ]
    )
);
$cache = require 'cache.php';
$dbconfig = require 'database.php';
$configurations['settings']['database'] = $dbconfig;
$configurations['settings']['cache'] = $cache;
$configurations['settings'] = forceSettingsFromENV($configurations['settings']);
return $configurations;
?>
