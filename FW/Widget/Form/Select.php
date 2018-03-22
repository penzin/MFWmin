<?php

namespace FW\Widget\Form;

/**
 * Класс для работы с выпадающим списком
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Select extends FormElement 
{
    /**     
     * @var boolean  Возможность множественного выбора
     */
    private $_multiple = false;
    
    
    /**     
     * @var integer  Количество строк в списке
     */    
    private $_size = 1;
    
    
    /**
     * @var array   Массив данных для списка 
     */
    private $_data;
    
    
    /**
     * @var boolean Пустой ли первый вариант списка
     */
    private $_first_empty = false;
        
    
    /**
     * @var type Значение пустого варианта
     */
    private $_empty_val = '';
    
    
    /**
     * Распечатка элемента
     * 
     * @return string
     */
    public function __toString() 
    {
        //HTML представление для настроек
        $multiple       = ($this->_multiple) ? "multiple" : "";       
        $size           = (empty($this->_size)) ? "" : "size='{$this->_size}'";
        
        $id             = (empty($this->_id)) ? "" : "id='{$this->_id}'";
        $name           = (empty($this->_name)) ? "" : "name='{$this->_name}'";
        $classes        = (empty($this->_classes)) ? "" : "class='{$this->_classes}'";
        $style          = (empty($this->_style)) ? "" : "style='{$this->_style}'";        
        $value          = (empty($this->_value)) ? [] : $this->_value;        
        $title          = (empty($this->_title)) ? "" : "title='{$this->_title}'";
        $custom_attr    = (empty($this->_custom_attr)) ? "" : $this->_custom_attr;
        $enabled        = ($this->_enabled) ? "" : "disabled='disabled'";
        $readonly       = ($this->_readonly) ? "readonly='readonly'" : "";
        $required       = ($this->_required) ? "required='required'" : "";
             
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
        $out = "<select $id $name $classes $style $title $custom_attr "
                . "$enabled $visible $readonly $multiple $required $size>";
        
        if (!empty($this->_data)) 
        {
            //установка первого пустого варианта
            if ($this->_first_empty) {
                array_unshift($this->_data, ['name' => '', 'value' => $this->_empty_val]);
            }

            //определим наличие ключей типа data
            $probe_elem = ($this->_first_empty && isset($this->_data[1])) ? $this->_data[1] : $this->_data[0];
            $data_keys = array_filter(array_keys($probe_elem), function($val){
                    if (strpos($val, 'data-') === 0) {
                        return true;
                    }
                    else {
                        return false;
                    }
            });            
;
            //сформируем код
            foreach ($this->_data as $row) 
            {       
                $data_attrs = '';
                if (!empty($data_keys)) {                    
                    foreach ($data_keys as $data_key)
                    {
                        $data_val = (isset($row[$data_key])) ? $row[$data_key] : '';
                        $data_attrs .= " $data_key='$data_val' ";
                    }
                }
                        
                $selected = (in_array($row['value'], $value)) ? "selected" : "";
                $out .= "<option value='{$row['value']}' $selected $data_attrs>{$row['name']}</option>";
            }
        }        

        $out .= "</select>";
        
        return $out;
    }
    
    
    /**
     * Переопределение метода установки значения, чтобы оно задавалось в виде массива
     * 
     * @param array $value  Массив выделенных значений
     */
    public function setValue($value) {
        
        $this->_value = (array)$value;
        return $this;
    }
    
    
    /**
     * Установка возможности множественного выбора
     * 
     * @param boolean $multiple  Возможность множественного выбора (флаг)
     * 
     * @return \FW\Widget\Form\Select
     */
    public function setMultiple($multiple)
    {
        $this->_multiple = (boolean)$multiple;
        
        return $this;
    }
    
    
    /**
     * Установка количества строк
     * 
     * @param integer $size  Высота в символах
     * 
     * @return \FW\Widget\Form\Select
     */
    public function setSize($size)
    {
        $this->_size = (int)$size;
        
        return $this;
    }       
    
    
    /**
     * Установка перечня значений для списка выбора
     * Массив должен представлять из себя однородную матрицу, шириной в 2 поля.
     * Наличие ключей name и value обязательно. Также могут быть ключи вида "data-",
     * они будут добавлены как data-атрибуты в option
     * 
     * 
     * @param array $data   массив значений
     * 
     * @return \FW\Widget\Form\Select
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
    
    
    /**
     * Установка пустого первого варианта в списке
     * 
     * @param boolean $first_empty  Пустой ли первый вариант (флаг)
     * @param string  $empty_val    Значение пустого варианта
     * 
     * @return \FW\Widget\Form\Select
     */
    public function setFirstEmpty($first_empty = false, $empty_val = '')
    {
        $this->_first_empty = (boolean)$first_empty;
        $this->_empty_val = (string)$empty_val;
        
        return $this;
    }       
}
