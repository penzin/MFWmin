<?php

namespace FW\Widget\GridElements;

use FW\Widget\Widget;

class Checkbox extends Widget
{
    
    /**
     * Имена дочерних чекбоксов
     * 
     * @var string 
     */
    private $_child_name = 'cb_select_all_rows[]';
    
    
    /**
     * ID родительского чекбокса
     * 
     * @var string
     */
    private $_child_class = 'cb_selected_id';
    
    
    /**
     * Конструктор
     */
    public function __construct() 
    {
        $this->_id = "cb_selected_id_all";
    }

    
    /**
     * Задает имя дочерним чекбоксам
     * 
     * @param string $name
     * @return \FW\Widget\GridElements\Checkbox
     */
    public function setChildName($name)
    {
        $this->_child_name = $name;
        return $this;
    }
            
    
    /**
     * Получние HTML кода для дочернего чекбокса
     * 
     * @param mixed $value      
     * @return string
     */
    public function child($value)
    {
        $output  = "<input type='checkbox' ";
        $output .= "class='" . $this->_child_class . "' ";
        $output .= "name='" . $this->_child_name . "' ";
        $output .= "value='" . $value . "' ";
        $output .= "/>";
        return $output;
    }
    
    
    /**
     * Получение HTML кода для родительского чекбокса 
     * 
     * @return string
     */
    public function __toString() 
    {
        $output  = "<input type='checkbox' ";
        $output .= "id='" . $this->_id . "' ";
        $output .= "/>";
        $output .= $this->_getWidgetTemplate('checkbox', [
            'id'            =>  $this->_id,
            'child_class'   =>  $this->_child_class
        ]);
        return $output;
        
    }
    

}
