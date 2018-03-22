<?php

namespace FW\API;


/**
 * Ответ в виде массива от REST API
 * 
 * @author fedyak <fedyak.82@gmail.com>
 * @author под редакцией Roman V. Penzin <penzin.r.v@gmail.com>
 */
class ApiResponseArray
{
    /**
     * @var type Данные из ответа
     */
    private $_data;
    
    
    /**
     * Конструктор класса
     * 
     * @param array $data
     */
    public function __construct($data) 
    {
        $res = json_decode($data, true);
        $this->_data = $res;
    }
    
    
    /**
     * Выполнение класса как метод
     * 
     * @param type $name
     * 
     * @return boolean
     */
    public function __invoke($name) 
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        
        return false;
    }
    
    /**
     * Возвращает данные
     * 
     * @return mixed
     */
    public function getDataArray()
    {
        return $this->_data;
    }    

}