<?php

namespace FW\Widget\Form;

/**
 * Класс для работы с многострочным полем ввода
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Textarea extends FormElement 
{
    /**     
     * @var integer  Количество колонок
     */
    private $_cols = 20;
    
    
    /**     
     * @var integer  Количество строк
     */    
    private $_rows = 2;
        
    
    /**     
     * @var string  Перенос слов
     */    
    private $_wrap = 'soft';
    
    
    /**     
     * @var integer  Максимальное количество символов
     */    
    private $_maxlength;
    
    
    
    /**
     * Распечатка элемента
     * 
     * @return string
     */
    public function __toString() 
    {
        //HTML представление для настроек
        $wrap = (empty($this->_wrap)) ? "" : "wrap='{$this->_wrap}'";       
        $cols = (empty($this->_cols)) ? "" : "cols='{$this->_cols}'";
        $rows = (empty($this->_rows)) ? "" : "rows='{$this->_rows}'";
        $maxlength = (empty($this->_maxlength)) ? "" : "maxlength='{$this->_maxlength}'";
        
        $id = (empty($this->_id)) ? "" : "id='{$this->_id}'";
        $name = (empty($this->_name)) ? "" : "name='{$this->_name}'";
        $classes = (empty($this->_classes)) ? "" : "class='{$this->_classes}'";
        $style = (empty($this->_style)) ? "" : "style='{$this->_style}'";
        $value = (empty($this->_value)) ? "" : $this->_value;
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
        return "<textarea $id $name $classes $style $title $custom_attr $required"
                . "$enabled $visible $readonly $cols $rows $wrap $maxlength>"
                . "$value</textarea>";
    }
    
    
    /**
     * Установка ширины в символах
     * 
     * @param integer $cols  Ширина в символах
     * 
     * @return \FW\Widget\Form\Textarea
     */
    public function setCols($cols)
    {
        $this->_cols = (int)$cols;
        
        return $this;
    }
    
    
    /**
     * Установка высоты в символах
     * 
     * @param integer $rows  Высота в символах
     * 
     * @return \FW\Widget\Form\Textarea
     */
    public function setRows($rows)
    {
        $this->_rows = (int)$rows;
        
        return $this;
    }   
    
    
    /**
     * Установка максимального количества симовлов
     * 
     * @param integer $maxlength  Максимальное количество символов
     * 
     * @return \FW\Widget\Form\Textarea
     */
    public function setMaxlength($maxlength)
    {
        $this->_maxlength = (int)$maxlength;
        
        return $this;
    }  


    /**
     * Установка переносов
     * 
     * @param string $wrap  Тип переносов
     * 
     * @return \FW\Widget\Form\Textarea
     */
    public function setWrap($wrap)
    {
        $possible_wraps = [
            'soft', 
            'hard', 
            'off', 
        ];
        
        if (in_array($wrap, $possible_wraps)) {
            $this->_wrap = $wrap;
        }
                
        return $this;
    }         
    
    
}
