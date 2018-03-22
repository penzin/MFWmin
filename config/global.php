<?php

define('DEV_MODE', false);

return [
    'name'  => 'Приложение MFW',

    'db' => [
        'db_host'      => '127.0.0.1',
        'db_port'      => 3306,
        'db_login'     => 'root',
        'db_password'  => '',
        'db_name'      => '',
    ],
    
    'modules' => [
        'Application',       
    ],
    
    'layouts' => [

    ],
    
];
