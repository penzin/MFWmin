<?php

namespace FW\ResponseType;

use FW\Singleton as S;
use FW\Response  as R;

/**
 * Класс ответа формата Plain Text
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class PlainTextResponse implements ResponseTypeInterface
{
    /**
     * @var string Вывод строки
     */
    private $_content;
    
    
    /**
     * Конструктор класса - заполнение данных
     * 
     * @param string $content   Данные для вывода в формате обычного текста
     */
    public function __construct($content) 
    {
        $this->_content = $content;
    }
    
    
    /**
     * Распечатка результата
     */
    public function __toString() 
    {
        S::get('Response')->setContentType(R::CONTENT_TYPE_PLAIN_TEXT)
                          ->applyHeaders();
        
        return $this->getContent();
    }
        
    
    /**
     * Распечатка результата
     */
    public function getContent()
    {
        return $this->_content;
    }
}
