<?php

namespace FW;

/**
 * Класс для работы с конфигурацией приложения
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Config
{
    
    /**
     * @var array Общий конфиг приложения
     */
    private $_config;
    
    
    /**
     * @var array Модули
     */
    private $_modules = [];
    
    
    /**
     * @var array Маршруты
     */
    private $_routes = [];
    
    
    /**
     * @var array Шаблоны
     */
    private $_layouts = [];
    
    
    /**
     * @var array Представления
     */
    private $_views = [];
    
    
    /**
     * @var array Виждеты
     */
    private $_widgets = [];    
    
    
    /**
     * Конструктор класса - инициализация конфига приложения
     * 
     * @param array $config
     */
    public function __construct($config) 
    {
        if (!is_array($config)) {
            throw new \Exception("Передан неверный конфиг!");
        }
        
        //заполнение внутренних переменных данными из конфига, не дублируя модули,
        //маршруты, шаблоны и представления
        $this->_config = array_diff_key($config, array_flip(['modules', 'routes', 'layouts', 'views']));
        
        $this->_modules = (array_key_exists('modules', $config)) ? $config['modules'] : [];
        $this->_routes = (array_key_exists('routes', $config)) ? $config['routes'] : [];
        $this->_layouts = (array_key_exists('layouts', $config)) ? $config['layouts'] : [];
        $this->_views = (array_key_exists('views', $config)) ? $config['views'] : [];
        
        if (!empty($this->_modules)) {
            $this->_getModulesConfigs();
        }  

        //выполняем сортировку маршрутов
        $this->_sortRoutes(); 
    }
    
    
    /**
     * Создает наборы данных по маршрутам, шаблонам, представлениям по всем модулям
     * 
     * @return void    
     */
    private function _getModulesConfigs()
    {
        //обходим все модули
        foreach ($this->_modules as $module)
        {          
            $config_fname = MODULE_DIR . DS . $module . DS . "config.php";
            
            //проверяем наличие файла конфига
            if (!file_exists($config_fname)) {
                continue;
            }
            
            $config = include $config_fname;
            
            //собираем роуты
            if (isset($config['routes']) && is_array($config['routes'])) {
                $this->_routes = array_merge($this->_routes, $config['routes']);
            }

            //собираем шаблоны
            if (isset($config['layouts']) && is_array($config['layouts'])) {
                $this->_layouts = array_merge($this->_layouts, $config['layouts']);
            }
            
            //собираем вьюхи
            if (isset($config['views']) && is_array($config['views'])) {
                $this->_views = array_merge($this->_views, $config['views']);
            }    
            
            //собираем виджеты
            if (isset($config['widgets']) && is_array($config['widgets'])) {
                $this->_widgets = array_merge($this->_widgets, $config['widgets']);
            }              
            
        }        
    }  
    
    
    /**
     * Сортировка машрутов из конфига
     * Правила сортировки:
     * 1) Маршруты без плейсхолдеров имеют больший приоритет
     * 2) Маршрут с меньшим количеством плейсхолдеров имеет больший приоритет
     * 3) Маршрут с меньшим количеством блоков имеет больший приоритет
     * 
     * @return void
     */
    private function _sortRoutes()
    {
        uksort($this->_routes, function($a, $b) {
            //количество плейсхолдеров
            $val_a = substr_count($a, ":");
            $val_b = substr_count($b, ":");

            //если одинаковое количество, сравниваем по длине маршрута
            if ($val_a == $val_b) {
                
                //длина маршрутов (в блоках)
                $len_a = count(explode("/", $a));
                $len_b = count(explode("/", $b));
                
                if ($len_a == $len_b){
                    return 0;
                }
                elseif ($len_a > $len_b) {
                    return 1;
                }
                else {
                    return -1;
                }
                
            }
            elseif ($val_a > $val_b) {
                return 1;
            }
            else {
                return -1;
            }
        });
    }
    
    
    /**
     * Получение списка подключенных модулей
     * 
     * @return array
     */
    public function getModules()
    {
        return $this->_modules;
    }
    
    
    /**
     * Получение списка подключенных виждетов
     * 
     * @return array
     */
    public function getWidgets()
    {
        return $this->_widgets;
    }    
    
    
    /**
     * Получение маршрутов
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }
    
    
    /**
     * Получение шаблонов
     * 
     * @return array
     */    
    public function getLayouts()
    {
        return $this->_layouts;
    }
    
    
    /**
     * Получение представлений
     * 
     * @return array
     */    
    public function getViews()
    {
        return $this->_views;
    }
    
    
    /**
     * Получение конфигурационного параметра (произвольного)
     * 
     * @param string    $name   Имя конфигурационного параметра
     * 
     * @return mixed
     */
    public function getParam($name)
    {
        if (isset($this->_config[$name])) {
            return $this->_config[$name];
        }
        
        return null;
    }
    
    
    /**
     * Модуль подключен?
     * 
     * @param string $name
     * @return boolean
     */
    public function moduleIsset($name)
    {
        return in_array($name, $this->_modules);
    }
    
}

