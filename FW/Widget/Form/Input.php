<?php

namespace FW\Widget\Form;

/**
 * Класс для работы с полем ввода
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Input extends FormElement 
{
    /**     
     * @var string  Тип поля ввода 
     */
    private $_type = 'text';
    
    
    
    /**
     * Распечатка элемента
     * 
     * @return string
     */
    public function __toString() 
    {
        //HTML представление для настроек
        $type = (empty($this->_type)) ? "" : "type='{$this->_type}'";       
        $id = (empty($this->_id)) ? "" : "id='{$this->_id}'";
        $name = (empty($this->_name)) ? "" : "name='{$this->_name}'";
        $classes = (empty($this->_classes)) ? "" : "class='{$this->_classes}'";
        $style = (empty($this->_style)) ? "" : "style='{$this->_style}'";
        $value = (empty($this->_value)) ? "" : "value='{$this->_value}'";
        $title = (empty($this->_title)) ? "" : "title='{$this->_title}'";
        $custom_attr = (empty($this->_custom_attr)) ? "" : $this->_custom_attr;
        $enabled = ($this->_enabled) ? "" : "disabled='disabled'";
        $readonly = ($this->_readonly) ? "readonly='readonly'" : "";
        $required = ($this->_required) ? "required='required'" : "";
        
        if (!$this->_visible) {
            if (!empty($style)) {
                if (strpos(str_replace(" ", "", strtolower($this->_style)), "display:none;") === false) {
                    $style =  rtrim($style,"'") . " display:none;'";
                    $visible = "";
                }
            }
            else {
                $visible = "style='display:none;'";
            }
        }
        else {
            $visible = "";
        }
        
        //возвращаем код
        return "<input $type $id $name $classes $style $value "
                . "$title $custom_attr $enabled $visible $readonly $required>";
    }
    
    
    /**
     * Установка типа поля ввода
     * 
     * @param string $type  Тип
     * 
     * @return \FW\Widget\Form\Input
     */
    public function setType($type)
    {
        $possible_types = [
            'text', 
            'password', 
            'color', 
            'email', 
            'number', 
            'range', 
            'search', 
            'tel',
        ];
        
        if (in_array($type, $possible_types)) {
            $this->_type = $type;
        }
        
        return $this;
    }
    
    
}
