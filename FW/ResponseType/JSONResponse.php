<?php

namespace FW\ResponseType;

use FW\Singleton as S;
use FW\Response  as R;

/**
 * Класс ответа формата JSON
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class JSONResponse implements ResponseTypeInterface
{
    /**
     * @var array   Массив данных 
     */
    private $_data;
    
    
    /**
     * @var string  JSON 
     */
    private $_content;
    
    
    /**
     * Конструктор класса - заполнение данных
     * 
     * @param array $data   Данные для вывода в формате JSON
     */
    public function __construct(array $data = []) 
    {
        $this->_data = $data;
    }
    
    
    /**
     * Распечатка результата
     */
    public function __toString() 
    {
        S::get('Response')->setContentType(R::CONTENT_TYPE_JSON)
                          ->applyHeaders();
        
        return $this->getContent();
    }
    
    
    /**
     * Ответ в виде перенаправления
     * 
     * @return \FW\ResponseType\JSONResponse
     */
    public function redirect()
    {
        $this->_content = json_encode([
            'result'    => 'redirect',
            'data'      => $this->_data,
        ]);
        
        return $this;
    }
    
    
    /**
     * Ответ в виде успешного результата
     * 
     * @param string    $message    Сообщение об успехе
     * 
     * @return \FW\ResponseType\JSONResponse
     */
    public function ok($message = '')
    {
        $this->_content = json_encode([
            'result'    => 'ok',
            'data'      => $this->_data,
            'message'   => $message
        ], JSON_NUMERIC_CHECK);     
        
        return $this;
    }
    
    
    /**
     * Ответ в виде ошибки
     * 
     * @param string    $message    Сообщение об ошибке
     * 
     * @return \FW\ResponseType\JSONResponse
     */
    public function error($message = '')
    {
        $this->_content = json_encode([
            'result'    => 'error',
            'data'      => $this->_data,
            'message'   => $message
        ], JSON_NUMERIC_CHECK);     
        
        return $this;
    }
    
    
    /**
     * Распечатка результата
     */
    public function getContent()
    {
        return $this->_content;
    }
}
