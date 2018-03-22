<?php

namespace FW;

/**
 * Работа с HTTP запросом
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Request
{
    /**
     * @var string  Запрошенный URI (вместе с ? & #)
     */
    private $_uri = '';
    
    
    /**
     * @var string  Метод запроса: GET, POST, PUT, ...
     */
    private $_method = '';
    
    
    /**
     * @var string  Имя пользователя при Basic аутентификации
     */
    private $_user = '';    
    
    
    /**
     * @var string  Пароль при Basic аутентификации
     */
    private $_password = '';    


    /**
     * @var string  Фргагмент (Якорь, то что после #)
     */
    private $_fragment = '';     
    
    
    /**
     * @var string   Строка запроса (чистая)
     */
    private $_request_uri = '';


    /**
     * @var string Кусок строки запроса с параметрами
     */
    private $_query_string = '';
    
    
    /**
     * @var array Параметры, переданные в строке запроса
     */
    private $_query_params = [];
    
   
    /**
     * @var array Массив параметров, полученных при роутинге
     */
    private $_router_params = [];
    
     
    /**
     * Констурктор класса - заполнение строки запроса и метода и старт парсинга
     */
    public function __construct() 
    {
        $this->_uri     = REQUEST_URI;
        $this->_method  = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $this->parseURI();
    }   
    
    
    /**
     * Парсинг строки запроса
     */
    public function parseURI()
    {
        $this->_request_uri = parse_url($this->_uri, PHP_URL_PATH);
        
        $this->_query_string = parse_url($this->_uri, PHP_URL_QUERY); 
        
        //сохраняет переменные в виде массива во второй параметр
        parse_str($this->_query_string, $this->_query_params);

        $this->_user = parse_url($this->_uri, PHP_URL_USER);
        $this->_password = parse_url($this->_uri, PHP_URL_PASS);
        $this->_fragment = parse_url($this->_uri, PHP_URL_FRAGMENT);        
    }
    
    
    /**
     * Получение строки запроса
     * 
     * @return array
     */
    public function getRequestURI()
    {
        return $this->_request_uri;
    }
    
    
    /**
     * Получение метода запроса
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }
    
    
    /**
     * Получение имени пользователя при Basic - аутентификации
     * 
     * @return string
     */
    public function getBasicUser()
    {
        return $this->_user;
    }    
    
    
    /**
     * Получение пароля пользователя при Basic - аутентификации
     * 
     * @return string
     */
    public function getBasicPassord()
    {
        return $this->_password;
    }      
    
    
    /**
     * Получение фрагмента (якоря)
     * 
     * @return string
     */
    public function getFragment()
    {
        return $this->_fragment;
    }         
    
    
    /**
     * Получение параметров, переданных в URI
     * 
     * @return array
     */
    public function getQueryParams()
    {
        return $this->_query_params;
    }
    
    
    public function getQueryString()
    {
        return $this->_query_string;
    }
    
    
    /**
     * Добавление параметра из роутинга
     * 
     * @param string $name
     * @param string $value
     * 
     * @return void
     */
    public function addRouterParam($name, $value)
    {
        $this->_router_params[$name] = $value;
    }
    
    
    /**
     * Получение параметров роутинга
     * 
     * @return array
     */
    public function getRouterParams()
    {
        return $this->_router_params;
    }
    
    
    /**
     * Получение POST данных в формате JOSON
     * 
     * @return array|boolean
     */
    public static function getJSONPOST()
    {
        $json = file_get_contents('php://input');
        if (empty($json)) {
            return [];
        }
        
        return json_decode($json, true);
    }   
    
        
    /**
     * Метод запроса - POST?
     * 
     * @return boolean
     */
    public static function isPost()
    {
        return ($_SERVER["REQUEST_METHOD"] == "POST");
    }


    /**
     * Метод запроса - GET?
     * 
     * @return boolean
     */    
    public static function isGet()
    {
        return ($_SERVER["REQUEST_METHOD"] == "GET");
    }


    /**
     * AJAX?
     * 
     * @return boolean
     */    
    public static function isAjax()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return false;
        }

        $xhr = strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest';
        
        if (!$xhr) {
            return false;
        }

        return true;
    }      
    
    
    /**
     * Возвращает GET параметр запроса
     * 
     * @param string|array  $param_name
     * @param mixed         $default_value
     * @param boolean       $filter         Применять ли фильтр к переменным
     * 
     * @return boolean|mixed
     */
    public static function get($param_name = null, $default_value = null, $filter = false)
    {
        if (is_array($param_name)) {
            $res = [];
            foreach ($param_name as $p) {
                $res[] = self::get($p);
            }
            return $res;
        }        
        
        $filtered = $_GET;
        
        //фильтр входных данных
        if ($filter) {
            array_walk_recursive($filtered, function(&$val, $key){
                $val = htmlspecialchars($val, ENT_QUOTES);
            });    
        }
        
        if (is_null($param_name)) {
            return $filtered;
        }        
        
        if (array_key_exists($param_name, $filtered)) {
            return $filtered[$param_name];
        }

        return $default_value;
    }


    /**
     * Возвращает POST параметр запроса
     * 
     * @param string|array  $param_name
     * @param mixed         $default_value
     * @param boolean       $filter         Применять ли фильтр к переменным
     * 
     * @return boolean|mixed
     */
    public static function post($param_name = null, $default_value = null, $filter = false)
    {
        if (is_array($param_name)) {
            $res = [];
            foreach ($param_name as $p) {
                $res[] = self::post($p);
            }
            return $res;
        }        
        
        //$filtered = filter_input_array(INPUT_POST, $_POST);
        $filtered = $_POST;
        
        //фильтр входных данных
        if ($filter) {
            array_walk_recursive($filtered, function(&$val, $key){
                $val = htmlspecialchars($val, ENT_QUOTES);
            });
        }
        
        if (is_null($param_name)) {
            return $filtered;
        }
        
        if (array_key_exists($param_name, $filtered)) {            
            return $filtered[$param_name];
        }
        
        return $default_value;
    }        
}