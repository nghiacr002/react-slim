<?php 
use LegoAsync\Modules\HelloWorld\Controller\TestController;

$router = array(
    'helloworld_index' => array(
        'uri' => '/home',
        'controller' => TestController::class,
        'action' => 'Home',
        'method' => ['GET','POST'],
        //'auth' => true
    ),
    'helloworld_TestAsync' => array(
        'uri' => '/async',
        'controller' => TestController::class,
        'action' => 'TestAsync',
        'method' => ['GET'],
        //'auth' => true
    ),
    'helloworld_TestQueue' => array(
        'uri' => '/queue',
        'controller' => TestController::class,
        'action' => 'TestQueue',
        'method' => ['GET'],
        //'auth' => true
    ),
    'helloworld_TestDBConnection' => array(
        'uri' => '/dbconnection',
        'controller' => TestController::class,
        'action' => 'TestDBConnection',
        'method' => ['GET'],
        //'auth' => true
    ),
    'helloworld_TestPHPINfo' => array(
        'uri' => '/phpinfo',
        'controller' => TestController::class,
        'action' => 'TestPHPInfo',
        'method' => ['GET'],
        //'auth' => true
    ),
);
return $router;