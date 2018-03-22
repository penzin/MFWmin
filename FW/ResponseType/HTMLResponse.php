<?php

namespace FW\ResponseType;

use FW\Singleton as S;
use FW\Response  as R;

/**
 * Класс ответа формата HTML
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class HTMLResponse implements ResponseTypeInterface
{
    /**
     * @var string  HTML
     */
    private $_content;
    
    
    /**
     * Конструктор класса - заполнение данных
     * 
     * @param string $content   Данные для вывода в формате HTML
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
        S::get('Response')->setContentType(R::CONTENT_TYPE_HTML)
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
