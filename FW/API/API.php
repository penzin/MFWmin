<?php

namespace FW\API;

use FW\API\ApiResponse;

/**
 * Класс для работы с REST API
 * 
 * @author fedyak <fedyak.82@gmail.com>
 * @author под редакцией Roman V. Penzin <penzin.r.v@gmail.com>
 */
final class API
{
    
    /**
     * @var boolean Режим, не выкидывающий исключений
     */
    private $_silent_mode;
    
    
    /**
     * @var Resource Дескриптор cURL соединения
     */
    private $_ch;
    
    
    /**
     * @var string Адрес API сервера
     */
    private $_api_url;   
    
    
    /**
     * @var int Код ответа HTTP
     */
    private $_http_code;
    

    /**
     * Подключение к API
     * 
     * @param string $api_url
     */
    public function __construct($api_url) 
    {
        $this->_api_url = $api_url;
    }

    
    /**
     * Включение режима, при котором не выбрасываются исключения
     * 
     * @return \FW\API\API
     */
    public function silent()
    {
        $this->_silent_mode = true;
        return $this;
    }
    
    
    /**
     * Инициализация CURL
     * 
     * @return void
     */
    private function _curlInit()
    {
        $this->_ch = curl_init();
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_HEADER, true);
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, 0);
    }
    
    
    /**
     * Уничтожаем CURL дескриптор
     * 
     * @return void
     */
    private function _curlDestroy()
    {
        curl_close($this->_ch);
    }
    
    
    /**
     * Парсинг ответа сервера API
     * 
     * @param string $response
     * @return array             
     */
    private function _parseResponse($response)
    {
        $header_size = curl_getinfo($this->_ch, CURLINFO_HEADER_SIZE);
        $http_code   = curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);
        
        $headers = substr($response, 0, $header_size);
        $data    = substr($response, $header_size);         
        
        $this->_http_code = $http_code;
        $this->_curlDestroy();
        
        return [
            'data'      => $data,
            'http_code' => $http_code,
            'headers'   => $headers
        ];
    }
    
    
    /**
     * Получение кода ответа http
     * 
     * @return string
     */
    public function getHTTPcode()
    {
        return $this->_http_code;
    }
    
    
    /**
     * Выполнение GET запроса к API
     * 
     * @param string $url
     * 
     * @return \FW\API\ApiResponse
     */
    public function get($url)
    {
        $this->_curlInit();
        
        curl_setopt($this->_ch, CURLOPT_URL, $this->_api_url . $url);
        
        $res = new ApiResponse($this->_parseResponse(curl_exec($this->_ch)), $this->_api_url . $url, $this->_silent_mode);
        $this->_silent_mode = false;
        return $res;
    }
    
    
    /**
     * Выполнение POST запроса к API
     * 
     * @param string $url
     * @param array $params
     * 
     * @return \FW\API\ApiResponse
     */    
    public function post($url, $params = [])
    {
        $this->_curlInit();
        
        curl_setopt($this->_ch, CURLOPT_URL, $this->_api_url . $url);
        curl_setopt($this->_ch, CURLOPT_POST, true);
        curl_setopt($this->_ch, CURLOPT_POSTFIELDS, http_build_query($params));
        
        $res = new ApiResponse($this->_parseResponse(curl_exec($this->_ch)), $this->_api_url . $url);
        $this->_silent_mode = false;
        return $res;
    }    
    
    
    /**
     * Выполнение PUT запроса к API
     * 
     * @param string $url
     * @param array $params
     * 
     * @return \FW\API\ApiResponse
     */    
    public function put($url, $params = [])
    {
        $this->_curlInit();
        
        curl_setopt($this->_ch, CURLOPT_URL, $this->_api_url . $url);
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->_ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $res = new ApiResponse($this->_parseResponse(curl_exec($this->_ch)), $this->_api_url . $url);
        $this->_silent_mode = false;
        return $res;
    }     

    
    /**
     * Выполнение DELETE запроса к API
     * 
     * @param string $url
     * @param array $params
     * 
     * @return \FW\API\ApiResponse
     */    
    public function delete($url, $params = [])
    {
        $this->_curlInit();
        
        curl_setopt($this->_ch, CURLOPT_URL, $this->_api_url . $url);
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($this->_ch, CURLOPT_POSTFIELDS, http_build_query($params));
        
        $res = new ApiResponse($this->_parseResponse(curl_exec($this->_ch)), $this->_api_url . $url);
        $this->_silent_mode = false;
        return $res;
    }     
    

    
    /**
     * Возвращает адрес API сервера
     * 
     * @return string
     */
    public function getApiUrl()
    {
        return $this->_api_url;
    }
    
    
}