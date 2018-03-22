<?php

namespace FW\API;


/**
 * Ответ в виде объекта от REST API
 * 
 * @author fedyak <fedyak.82@gmail.com>
 * @author под редакцией Roman V. Penzin <penzin.r.v@gmail.com>
 * 
 * @deprecated
 */
class ApiResponseObject
{
    /**
     * @var mixed Данные
     */
    private $_data;
    
    
    /**
     * Констурктор класса
     * 
     * @param array $data
     */
    public function __construct($data) 
    {
        $array = json_decode($data, true);
        $this->_data = $array;
    }
    
    
    /**
     * 
     * @param type $name
     * 
     * @return boolean
     */
    public function __get($name) 
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        
        return false;
    }
    
    
    /**
     * 
     * @return type
     */
    public function getDataArray()
    {
        return $this->_data;
    }
    
    

}