<?php

namespace FW\API;

use FW\API\ApiResponseFormat;


/**
 * Класс, представляющий работу с ответом REST API
 * 
 * @author fedyak <fedyak.82@gmail.com>
 * @author под редакцией Roman V. Penzin <penzin.r.v@gmail.com>
 */
class ApiResponse 
{
    
    /**
     * @var mixed Тело ответа API сервера
     */
    private $_data;
    
    
    /**
     * @var string Заголовки ответа API сервера
     */
    private $_headers;
    
    
    /**
     * @var int  Код ответа API сервера
     */
    private $_http_code;
        

    /**
     * Констурктор класса
     * 
     * @param array     $response_array     Ассоциативный массив ['data' => , 'headers' => , 'http_code' => ]
     * @param string    $request            Текст запроса к API
     * @param boolean   $silent_mode        Если TRUE - исключения не будет выброшено
     */
    public function __construct($response_array, $request, $silent_mode = false) 
    {
        $this->_data      = $response_array['data'];
        $this->_headers   = $response_array['headers'];
        $this->_http_code = $response_array['http_code'];
        
        if ($silent_mode) {
            return;
        }
        
        // Ошибка
        if (in_array($this->_http_code, [401, 403, 500, 501])) {
            throw new \Exception($request . print_r($response_array, true));
        }        
    }
    
    
    /**
     * Преобразование тела ответа в нужный формат
     * 
     * @param string $type
     * 
     * @return mixed|array|null     По умолчанию возвращается массив
     */
    public function getData($type = null)
    {
        $response = new ApiResponseFormat($this->_data);
        
        switch ($type)
        {
            case ApiResponseFormat::RESPONSE_JSON:
                return $response->toJSON();

            case ApiResponseFormat::RESPONSE_ARRAY:
                return $response->toArray();

            case ApiResponseFormat::RESPONSE_OBJECT:
                return $response->toObject();

            default:
                return $response->toArray();
        }
    }
    

    /**
     * Возвращает код ответа API сервера
     * 
     * @return int
     */
    public function getHttpCode()
    {
        return $this->_http_code;
    }

    
    /**
     * Возвращает заголовки ответа API сервера
     * 
     * @return string
     */
    public function getHeaders()
    {
        return $this->_headers;
    }    
    
    
}