<?php

namespace FW\Widget\GridElements;

use FW\Widget\Widget;

class Button extends Widget
{
    
    /**
     * Кнопка редактирование
     */
    const EDIT = 1;
    
    
    /**
     * Кнопка удаления
     */
    const DELETE = 2;
    
    
    /**
     * Класс для элемента
     * 
     * @var string
     */
    private $_class;
    
    
    /**
     * Иконка на кнопке
     * 
     * @var string
     */
    private $_icon = '';
    
    
    /**
     * Ссылка
     * 
     * @var string
     */
    private $_href = '';
    
    /**
     * Текст на кнопке
     * 
     * @var string
     */
    private $_text = '';
    
    
    /**
     * Специальный класс для разных типов кнопок
     * 
     * @var string
     */
    private $_special_class = '';
    
    
    /**
     * ID элемента
     * 
     * @var int
     */
    private $_row_id;
    

    /**
     * Тип кнопки
     * 
     * @var string
     */
    private $_type;
    
    
    
    /**
     * Конструктор
     */
    public function __construct($type = null) 
    {
        $this->_type = $type;
        switch ($type) 
        {
            case self::EDIT:
                $this->_icon = '<span class="glyphicon glyphicon-pencil"></span>';
                break;

            case self::DELETE:
                $this->_icon = '<span class="glyphicon glyphicon-remove"></span>';
                $this->_special_class = 'btn-delete-row';
                break;    
            
            default:
                $this->_icon = '';
        }
    }


    /**
     * Задать CSS класс элементу
     * 
     * @param string $class
     * @return \FW\Widget\GridElements\Button
     */
    public function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }
    
    
    /**
     * Задать ссылку 
     * 
     * @param string $href
     * @return \FW\Widget\GridElements\Button
     */
    public function link($href)
    {
        $this->_href = $href;
        return $this;
    }
    
    
    /**
     * Задать текст кнонки
     * 
     * @param type $text
     * @return \FW\Widget\GridElements\Button
     */
    public function text($text)
    {
        $this->_text = $text;
        return $this;
    }
    
    
    public function rowID($value)
    {
        $this->_row_id = $value;
        return $this;
    }
    
    
    /**
     * Получение HTML кода виджета
     * 
     * @return string
     */
    public function __toString() 
    {
        $output  = "<a ";
        $output .= $this->_getDataBind();
        $output .= "href='" . $this->_getHREF() . "' ";
        $output .= "class='btn btn-default " . $this->_special_class . " " . $this->_class . "' ";
        $output .= "id='" . $this->_id . "' ";
        $output .= ">";
        $output .= $this->_icon;
        $output .= $this->_text;
        $output .= "</a>";
        return $output;
    }
    
    
    /**
     * Ссылка для редактирования элемента
     * 
     * @return string
     */
    private function _getHREF()
    {
        if (!empty($this->_href)) {
            return $this->_href;
        }
        
        if (empty($this->_row_id) || $this->_type != self::EDIT) {
            return '#';
        }
        
        /* @var $r \FW\Request */
        $r = \FW\Singleton::get('Request');
        $query_string = $r->getQueryString();
        $query = (!empty($query_string)) ? '?' . $query_string : '';
        return $r->getRequestURI() . $this->_row_id . "/edit/" . $query;
    }
    

}
