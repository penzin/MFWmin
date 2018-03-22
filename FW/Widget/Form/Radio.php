<?php

namespace FW\Widget\Form;

/**
 * Класс для работы с радио-кнопками
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Radio extends FormElement 
{
    /**
     * @var array   Массив данных для перечня переключателей
     */
    private $_data;

    
    /**
     * Распечатка элемента
     * 
     * @return string
     */
    public function __toString() 
    {
        //HTML представление для настроек        
        $id = (empty($this->_id)) ? "" : "id='{$this->_id}'";
        $name = (empty($this->_name)) ? "" : "name='{$this->_name}'";
        $classes = (empty($this->_classes)) ? "" : "class='{$this->_classes}'";
        $style = (empty($this->_style)) ? "" : "style='{$this->_style}'";

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
        
        
        $out = "";
        foreach ($this->_data as $row)
        {
            $checked = ($this->_value == $row['value']) ? "checked" : "";
            
            $out .= "<label><input type='radio' $id $name $classes $style "
                . "value='{$row['value']}' $checked $title $custom_attr "
                . "$enabled $visible $readonly $required>{$row['name']}</label>";
        }
        
        return $out;
    }
    
    
    /**
     * Установка перечня значений для переключателя
     * Массив должен представлять из себя однородную матрицу, шириной в 2 столбца.
     * Наличие ключей name и value обязательно
     * 
     * 
     * @param array $data   массив значений
     * 
     * @return \FW\Widget\Form\Radio
     */
    public function setData($data)
    {
        //проверка валидности входных данных
        if (is_array($data) && array_key_exists(0, $data)
                && array_key_exists('name', $data[0]) 
                && array_key_exists('value', $data[0])) {
            
            $this->_data = $data;
        }
        
        return $this;
    }
}
