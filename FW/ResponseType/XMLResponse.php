<?php

namespace FW\ResponseType;

use FW\Singleton as S;
use FW\Response  as R;

/**
 * Класс ответа формата XML
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class XMLResponse implements ResponseTypeInterface
{
    /**
     * @var array   Массив данных 
     */
    private $_data;
    
    
    /**
     * @var \SimpleXMLElement  Объект SimpleXMLElement
     */
    private $_xml;
    
        
    /**
     * Конструктор класса - заполнение данных
     * 
     * @param array $data   Данные для вывода в формате JSON
     */
    public function __construct(array $data = []) 
    {
        $this->_data = $data;
        
        $this->_xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
    }
    
    
    /**
     * Распечатка результата
     */
    public function __toString() 
    {
        S::get('Response')->setContentType(R::CONTENT_TYPE_XML)
                          ->applyHeaders();
        
        return $this->getContent();
    }
    
    
    /**
     * Утилитарная функция преобразования массива в ноды XML
     * 
     * @param array             $array  Исходный массив
     * @param \SimpleXMLElement $xml    Объект с XML
     * 
     * @return void
     */
    private function _arrayToXML($array, &$xml)
    {
        foreach ($array as $key => $val) 
        {
            if (is_array($val)) {
                
                if (is_numeric($key)) {
                    $key = 'item_' . $key;
                }
                
                $subnode = $xml->addChild($key);
                $this->_arrayToXML($val, $subnode);
                
            } 
            else {
                $xml->addChild("$key", htmlspecialchars("$val"));
            }
        }   
    }
    
    
    /**
     * Ответ в виде успешного результата
     * 
     * @param string    $message    Сообщение об успехе
     * 
     * @return \FW\ResponseType\XMLResponse
     */
    public function ok($message = '')
    {
        $result = [
            'result'    => 'ok',
            'data'      => $this->_data,
            'message'   => $message,
        ];

        $this->_arrayToXML($result, $this->_xml);
        
        return $this;
    }
    
    
    /**
     * Ответ в виде ошибки
     * 
     * @param string    $message    Сообщение об ошибке
     * 
     * @return \FW\ResponseType\XMLResponse
     */
    public function error($message = '')
    {
        $result = [
            'result'    => 'error',
            'data'      => $this->_data,
            'message'   => $message,
        ];

        $this->_arrayToXML($result, $this->_xml);
        
        return $this;
    }
    
    
    /**
     * Ответ в виде перенаправления
     * 
     * @return \FW\ResponseType\XMLResponse
     */
    public function redirect()
    {
        $result = [
            'result'    => 'redirect',
            'data'      => $this->_data,
        ];

        $this->_arrayToXML($result, $this->_xml);        
        
        return $this;
    }    
    
    
    /**
     * Распечатка результата
     */
    public function getContent()
    {
        return $this->_xml->asXML();
    }
}
