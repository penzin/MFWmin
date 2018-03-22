<?php

namespace FW\API;


/**
 * Форматированный ответ от REST API
 * 
 * @author fedyak <fedyak.82@gmail.com>
 * @author под редакцией Roman V. Penzin <penzin.r.v@gmail.com>
 */
class ApiResponseFormat
{
 
    /**
     * Ответ в виде JSON 
     */
    const RESPONSE_JSON = 'json';
    
    
    /**
     * Ответ в виде XML
     */
    const RESPONSE_XML = 'xml';
    
    
    /**
     * Ответ в виде ассоциативного массива
     */
    const RESPONSE_ARRAY = 'array';
    
    
    /**
     * Ответ в виде объекта
     */
    const RESPONSE_OBJECT = 'object';
    
    
    /**
     * @var mixed Данные
     */
    private $_data;
    
        
    /**
     * 
     * @param string $data    JSON данные
     */
    public function __construct($data) 
    {
        $this->_data = $data;
    }
    
    
    /**
     * Возвращает JSON
     * 
     * @return string
     */
    public function toJSON()
    {
        return $this->_data;
    }
    
    
    /**
     * Возвращает XML
     * 
     * @return string
     */
    public function toXML()
    {
        //
    }    
    
    
    /**
     * Возвращает ассоциативный массив
     * 
     * @return array|null
     */    
    public function toArray()
    {
        $res = json_decode($this->_data, true);
        
        //если нормально декодировали json то вернем массив, 
        //иначе кидаем исключение (что-то пошло не так на API сервере)
        if (!is_null($res)) {
            return $res;
        } 
        else {
            throw new \Exception('API PARSE ERROR. Reponse dump:' . print_r($this->_data, true));
        }
    }    
    
    
    /**
     * Возвращает объект
     * 
     * @return array|null
     */        
    public function toObject()
    {
        return json_decode($this->_data);
    }
    
    
    
}
