<?php

define('DB_PASSWORD', 'root');
define('DB_USER', 'root');

return [
    
    // Set up details on how to connect to the database
    'dsn'             => "mysql:host=localhost;dbname=phpmvc;",
    'username'        => DB_USER,
    'password'        => DB_PASSWORD,
    'driver_options'  => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
    //'table_prefix'    => "",
    
    /*
    // BTH
    'dsn'             => "mysql:host=blu-ray.student.bth.se;dbname=jofc13;",
    'username'        => "jofc13",
    'password'        => "y{O(4A%d",
    'driver_options'  => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
    'table_prefix'    => "phpmvc_project_",
    
    */
    // Display details on what happens
    // 'verbose' => true,

    // Throw a more verbose exception when failing to connect
    //'debug_connect' => 'true',
];
