<?php

namespace FW\Widget\Form;

/**
 * Класс для работы с чекбоксом
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Checkbox extends FormElement 
{
    /**     
     * @var boolean  Установлен ли флажок
     */
    private $_checked = false;
    
    
    /**
     * @var string  Подпись
     */
    private $_label = '';
        
    
    /**
     * Распечатка элемента
     * 
     * @return string
     */
    public function __toString() 
    {
        //HTML представление для настроек
        $checked = ($this->_checked) ? "checked" : "";       
        $label = (empty($this->_label)) ? "" : $this->_label;
        
        
        $id          = (empty($this->_id)) ? "" : "id='{$this->_id}'";
        $name        = (empty($this->_name)) ? "" : "name='{$this->_name}'";
        $classes     = (empty($this->_classes)) ? "" : "class='{$this->_classes}'";
        $style       = (empty($this->_style)) ? "" : "style='{$this->_style}'";
        $value       = (empty($this->_value)) ? "value='1'" : "value='{$this->_value}'";
        $title       = (empty($this->_title)) ? "" : "title='{$this->_title}'";
        $custom_attr = (empty($this->_custom_attr)) ? "" : $this->_custom_attr;
        $enabled     = ($this->_enabled) ? "" : "disabled='disabled'";
        $readonly    = ($this->_readonly) ? "readonly='readonly'" : "";
        $required    = ($this->_required) ? "required='required'" : "";
             
        if (!$this->_visible) {
            $visible = "style='display:none;'";
        }
        else {
            $visible = "";
        }
        
        //возвращаем код
        return "<label $visible $title><input type='checkbox' $id $name $classes $style $value $checked"
                . " $title $custom_attr $enabled $readonly $required> $label</label>";
    }
    
    
    /**
     * Установка флажка
     * 
     * @param boolean $checked  Установлен ли флажок (флаг)
     * 
     * @return \FW\Widget\Form\Checkbox
     */
    public function setChecked($checked)
    {
        $this->_checked = (boolean)$checked;
        
        return $this;
    }     
    
    
    /**
     * Установка подписи к флажку
     * 
     * @param string $label  Подпись
     * 
     * @return \FW\Widget\Form\Checkbox
     */
    public function setLabel($label)
    {
        $this->_label = $label;
        
        return $this;
    }       
}
