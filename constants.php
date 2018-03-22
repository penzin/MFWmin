<?php

/**
 * Константы приложения
 */


define("SERVER_NAME", filter_input(INPUT_SERVER, "SERVER_NAME"));
define("SERVER_PROTOCOL", filter_input(INPUT_SERVER, "SERVER_PROTOCOL"));

//пути, директории и файлы
define("DS", DIRECTORY_SEPARATOR);

if (!defined('CLI')) {
    $doc_root = filter_input(INPUT_SERVER, "DOCUMENT_ROOT");

    if (substr($doc_root, -1) == '/' || substr($doc_root, -1) == '\\') {
        define("WWW_DIR", $doc_root);
    } else {
        define("WWW_DIR", $doc_root . DS);
    }
} 
else {
    define("WWW_DIR", dirname(__FILE__) . DS . "www" . DS);
}



if (defined('CLI')) {
    define("REQUEST_URI", $_SERVER['argv'][2]);
    define("ROOT_DIR", dirname(__FILE__) . DS);
} else {
    define("ROOT_DIR", WWW_DIR . ".." . DS);
    define("REQUEST_URI", filter_input(INPUT_SERVER, 'REQUEST_URI'));
}

define("MODULE_DIR", ROOT_DIR . "Modules" . DS);
define("APP_JSON_CONFIG_FILE_NAME", ROOT_DIR . DS . "app_config.json");


//сессии
define("SESSION_REFERER_NAME", "");

//представления
define("VIEW_DEFAULT_TITLE", "Минимальное приложение MFW");
//define("VIEW_DEFAULT_KEYWORDS", "MFW");
define("VIEW_DEFAULT_AUTHOR", "");
define("VIEW_DEFAULT_COPYRIGHT", "");
define("VIEW_DEFAULT_DESCRIPTION", "Тестовое описание");
define("VIEW_DEFAULT_ROBOTS", "index,follow");
define("VIEW_DEFAULT_RESOURCE_TYPE", "document");
define("VIEW_DEFAULT_CONTENT_TYPE", "text/html");
define("VIEW_DEFAULT_CONTENT_CHARSET", "utf-8");
define("VIEW_DEFAULT_GENERATOR", "");
define("VIEW_DEFAULT_FAVICON", "");
define("VIEW_DEFAULT_ITEMS_PER_PAGE", 10);

//Дефолтный шаблон страницы 404
define("PAGE_DEFAULT_404", "error_404");