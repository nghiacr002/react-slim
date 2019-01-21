<?php
$database = array( 
    'default' => array(
        'read' => [
            'host' => ['127.0.0.1'],
        ],
        'write' => [
            'host' => ['127.0.0.1']
        ],
        'sticky' => true,
        'driver' => 'mysql',
        //'host' => 'localhost',
        'database' => 'mydb',
        'username' => 'root',
        'password' => '123456',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'prefix'    => 'tbl_',
        'options'   => array(
           // PDO::ATTR_PERSISTENT => true,
        ),
    ),
);
return $database;
?>