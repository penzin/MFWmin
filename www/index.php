<?php

//режим работы из консоли
if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == "cli") {
    define("CLI", true);
} 

chdir(dirname(__DIR__));

require_once 'constants.php';
require_once 'settings.php';

session_start();

if (!file_exists(ROOT_DIR . "/vendor/autoload.php")) {
    die("Application fatal error: Composer required!");
}

if (!file_exists(ROOT_DIR . 'config/global.php') && !file_exists(ROOT_DIR . 'config/local.php')) {
    die("Application fatal error: Application config not found!");    
}

//Composer autoloader
require_once ROOT_DIR . "/vendor/autoload.php";

try {
    $config_file = file_exists(ROOT_DIR . 'config/local.php') 
            ? (ROOT_DIR . 'config/local.php') 
            : (ROOT_DIR . 'config/global.php');

    $config = require_once $config_file;
    
    if (defined("DEV_MODE") && DEV_MODE === true) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    }
    else {
        error_reporting(0);
        ini_set('display_errors', '0');
    }    
    
    new FW\App($config);
}
    //исключение, выброшенное в роутере
    catch (FW\RouterException $e) 
    {
        if (defined('DEV_MODE') && DEV_MODE === true) {
            echo FW\DisplayException::show($e);
        }
        else {
            http_response_code(404);

            $page = \FW\Singleton::get('Page')->setLayout(PAGE_DEFAULT_404);
            $page->render();

            exit();
        }
    }
    //прочие исключения
    catch (Exception $e) {
        if (defined('DEV_MODE') && DEV_MODE === true) {
            echo FW\DisplayException::show($e);
        }
        else {
            echo "Страница не найдена";
        }
    }
