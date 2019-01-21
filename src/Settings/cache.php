<?php 
$cache = array(
    /*'httpCache' => array(
        'type' => 'public',
        'age' => 84600,
        'mustRevalidate' => false
    ),*/
    'filesystem' => array(
        'path' => APP_CACHE_PATH
    ),
    'redis' => array(
        'default' => array(
            //'host' => '192.168.9.68',
            'host' => '127.0.0.1',
            //'host' => 'caleb.kfrzue.0001.apse1.cache.amazonaws.com',
            'port' => 6379,
            'protocol' => 'tcp'
        )
    )
);
return $cache;