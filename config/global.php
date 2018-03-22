<?php

define('DEV_MODE', false);

return [
    'name'  => 'Минимальное приложение MFW',

    //коннект к БД
    'db' => [
        'db_host'      => '127.0.0.1',
        'db_port'      => 3306,
        'db_login'     => '',
        'db_password'  => '',
        'db_name'      => '',
    ],
    
    //подключенные модули
    'modules' => [
        'Application',       
    ],    
];
