<?php

namespace FW;

use FW\Singleton as S;
use FW\RouterException;

/**
 * Роутер приложения
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Router
{      
    /**    
     * 
     * @var string  Запрошенный URI 
     */
    private $_request_uri = '';

    
    /**
     * @var string  Имя модуля
     */
    private $_module = '';
    
    
    /**
     * @var string  Имя контроллера
     */    
    private $_controller = '';
    
    
    /**
     * @var string  Имя действия
     */    
    private $_action = '';
    
    
    private $_state;
    
    
    /**
     * Конструктор класса
     * 
     * @param string $request_uri Строка запроса (чистая)
     */
    public function __construct($request_uri)
    {       
        $this->_request_uri = $request_uri;              
    }
    
    
    /**
     * Разбор блоков запроса
     * 
     * @param array $request_blocks Блоки исходного запроса
     * 
     * @return void
     */
    private function _parseRequestBlocks(array $request_blocks)
    {
        //модуль всегда присутствует
        $this->_module = $request_blocks[0];
        
        //контроллер - либо index (если не указан), либо задан явно (1 параметр)
        if (array_key_exists(1, $request_blocks)) {
            $this->_controller = $request_blocks[1];
        }
        else {
            $this->_controller = 'index';
        }
        
        //action - либо index (если не указано), либо задано явно (2 параметр)
        if (array_key_exists(2, $request_blocks)) {
            $this->_action = $request_blocks[2];
        }    
        else {
            $this->_action = "index";
        }
        
        //если указан 4 параметр - это router параметр для передачи в action
        if (array_key_exists(3, $request_blocks)) {
            S::get('Request')->addRouterParam('default_router_param', $request_blocks[3]);
        }
    }
    
    
    public function setModule($name)
    {
        $this->_module = $name;
        return $this;
    }


    public function setController($name)
    {
        $this->_controller = $name;
        return $this;
    }

    
    public function setAction($name)
    {
        $this->_action = $name;
        return $this;
    }
    
    
    
    public function saveState()
    {
        $this->_state = [
            'module'     =>      $this->getModule(),
            'controller' =>      $this->getController(),
            'action'     =>      $this->getAction(),
        ];
        return $this;
    }
    
    
    public function restoreState()
    {
        $this->_module      = $this->_state['module'];
        $this->_controller  = $this->_state['controller'];
        $this->_action      = $this->_state['action'];
    }    
    
    
    /**
     * Получение имени текущего модуля
     * 
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    
    /**
     * Получение имени текущего контроллера
     * 
     * @return string
     */    
    public function getController()
    {
        return $this->_controller;
    }
    
    
    /**
     * Получение имени текущего действия
     * 
     * @return string
     */    
    public function getAction()
    {
        return $this->_action;
    }   
    
    
    /**
     * Осуществление роутинга
     * 
     * @return mixed
     */
    public function route()
    {
        //получили перечень маршрутов из конфигов модулей в виде массива
        $routes = S::get('Config')->getRoutes();
        
        //если текущий маршрут походит под правило, то сделать мэппинг
        $this->_request_uri = $this->_mapRoutes($routes);
               
        //собираем блоки: фильтруем пустые элементы массива и очищаем ключи
        $request_blocks = array_values(array_filter(explode("/", $this->_request_uri)));
        
        //распарсить блоки
        $this->_parseRequestBlocks($request_blocks);  
        
        //создать контроллер и выполнить действие
        return $this->_doAction();
    }
    
    
    /**
     * Мэппинг маршрутов на основании записей в конфиге
     * 
     * @param array $routes Массив маршуртов (общий)
     * 
     * @return string   Скорректированная строка запроса
     */
    private function _mapRoutes($routes)
    {       
        //берем исходный URI
        $origin_uri = $this->_request_uri;    
        
        //выделяем из него исходные блоки
        $origin_blocks = array_values(array_filter(explode("/", $origin_uri)));

        //перебираем все маршруты (из всех конфигов и из таблицы со страницами).
        //Ключ - правило (то что сопоставляется с реальным маршрутом), 
        //Значение - мэппинг (реальный машрут для FW, то что потом будет 
        //разбиваться на блоки module, controller, action и, если указан, 
        //default_route_param
        foreach ($routes as $route_rule => $route_map)
        {
            //На каждом шаге: выделяем блоки правила
            $route_rule_blocks = array_values(array_filter(explode("/", $route_rule)));
            
            //если количество блоков ПРАВИЛА не совпало с количеством блоков 
            //в ИСХОДНОМ запросе, значит совпадения пока нет
            if (count($origin_blocks) != count($route_rule_blocks)) {
                //поэтому переходим к следующему маршруту
                continue;
            }
            
            //для случая, когда количество блоков совпало:
            //поблоковое сравнение маршрута и формирование массива 
            //значений плейсхолдеров.
            //Если не произошло continue на блоке, который не является 
            //плейсхолдером - маршрут совпал и массив сформирован 
            //и лежит в request->getRouterParams()
            
            //итак, перебираем ИСХОДНЫЕ блоки
            foreach ($origin_blocks as $i => $origin_block)
            {
                //если очередной блок - это плейсхолдер (содержит двоеточие)
                if (strpos($route_rule_blocks[$i], ":") !== false) {
                    
                    //получили имя плейсхолдера (то что записано в правиле, 
                    //без двоеточия)
                    $name = trim($route_rule_blocks[$i],':');
                    
                    //запомнили плейсхолдер для последующего его использования
                    //при формировании РЕАЛЬНОГО маршрута
                    $ph[$route_rule_blocks[$i]] = $origin_block;
                    
                    //Сохранили его значение в массив плейсхолдеров реквеста
                    S::get('Request')->addRouterParam($name, $origin_block);
                    
                    //не выполняем сравнение этого блока и переходим к следующему
                    continue;
                }
                
                //если блок не плейсхолдер и отличается от подобного блока в 
                //реальном запросе, переходим к следующему маршруту 
                //(выход сразу из двух циклов)
                if ($origin_block != $route_rule_blocks[$i]) {
                    continue 2; //для текущего $route_rule
                }
            }

            //Для подошедшего маршрута:
            //заменяем плейсхолдеры их реальным значением 
            //(для случая если плейсхолдер присутствует как справа так и слева)
            if (!empty($ph)) {
                $route_map = str_replace(array_keys($ph), array_values($ph), $route_map);
            }
            //вернули первый рассчитанный маршрут
            return $route_map;
            
        }
        
        //если ни один из машрутов не подошел, вернули исходный
        return $origin_uri;
    }
    
    
    
    /**
     * Выполнить текущее действие
     * 
     * @throws \FW\RouterException
     * 
     * @return mixed
     */
    private function _doAction()
    {
        //имя контроллера
        $controller_name = 'Modules\\' . $this->_module . '\Controller\\' 
                                    . $this->_controller . 'Controller';        

        //имя класса инициализации модуля
        $module_init = 'Modules' . DS . $this->_module . DS . 'init';        
        
        //имя действия
        $action_name = $this->_action . 'Action';
        
        //если метод отсутствует => исключение
        if (!method_exists($controller_name, $action_name)) {   
            throw new RouterException('Не найдено действие ' . $action_name . ' в контроллере ' . $controller_name);
        }   
        
        //создаем инстанс класса init модуля если он существует        
        if (file_exists(ROOT_DIR . $module_init . ".php")) {
            $module_init = str_replace(DS, "\\", $module_init);
            new $module_init();
        }
        
        //инстанцируем класс контроллера
        $controller = new $controller_name();

        $rp = S::get('Request')->getRouterParams();
        
        //старт действия 
        return $controller->$action_name($rp);
    }
}

