<?php

namespace FW;

use FW\Singleton as S;
use FW\ResponseType\ResponseTypeInterface;
use FW\Helpers\JSONConfig;

/**
 * Основной класс приложения
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class App 
{    
    /**
     * Инициализация приложения
     * 
     * @param array $config
     */
    public function __construct(array $config)
    {       
        //инициализация констант приложения из JSON конфига
        $this->_loadJSONConfig();
        
        //обязательные константы (если вдруг JSON оказался пустым)
        if (!defined('SITE_ID')) {
            define('SITE_ID', $_SERVER['SERVER_NAME']);
        }       
        
        //обязательные константы (если вдруг JSON оказался пустым)
        if (!defined('NOTICE_EMAIL')) {
            define('NOTICE_EMAIL', '');
        }             
        
        //инициализация конфига приложения
        $this->_loadConfig($config);
        
        //инициализация роутинга, роутинг
        $request    = S::get('Request');
        $router     = S::get('Router', $request->getRequestURI());
        
        
        //выполнение действия
        $action_result = $router->route();
        
        //Устанавливаем secure header
        $r = S::get('Response');
        
        if (!$r->isHeadersSent()) {
            $r->setXframeOptionsSame();
            $r->setXSSBrowserBlock();
            $r->setXcontentTypeNoSniff();
            $r->applyHeaders();
        }
        
        //если вернулся экземпляр представления, устанавливаем его в Page
        if ($action_result instanceof View) {
            S::get('Page')->setView($action_result)->render();           
        }
        
        //если вернулся экземпляр объекта, реализующего интерфейс 
        //ResponseTypeInterface, выводим его на экран
        if ($action_result instanceof ResponseTypeInterface) {
            echo $action_result;           
        }
        
        //если вернулся экземпляр шаблона (страницы), выводим его на экран
        if ($action_result instanceof Page) {
            $action_result->render();
        }
    }
    
    
    /**
     * Загрузка конфига в приложение
     * 
     * @param array $config
     * 
     * @return Config
     */
    private function _loadConfig($config)
    {
        return S::get('Config', $config);
    }
    
    
    /**
     * Загрузка массива конcтант приложения из JSON конфига
     * 
     * @param string $file_name Имя файла конфига
     */
    private function _loadJSONConfig($file_name = APP_JSON_CONFIG_FILE_NAME)
    {
        $JSON_config = new JSONConfig($file_name);

        //формируем массив параметров
        $config = $JSON_config->getParams();

        if (!is_array($config) || count($config) == 0) {
            return false;
        }
        
        //инициализируем константы по конфигу
        foreach ($config as $const_name => $value) 
        {
            if (!defined($const_name)) {
                define($const_name, $value);
            } 
            else {
                throw new \Exception('Ошибка при загрузке конфигурации: константа ' . $const_name . ' уже объявлена!');
            }
        }
    }
}
