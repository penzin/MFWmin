<?php

namespace FW\Widget\Form;

use FW\Widget\Widget;

/**
 * Базовый класс элемента управления HTML Формы
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
abstract class FormElement extends Widget 
{
    /**
     * @var string  Перечень классов 
     */
    protected $_classes; 
    
    
    /**
     * @var string  Инлайн стиль 
     */
    protected $_style;
    
    
    /**
     * @var string  Имя элемента 
     */
    protected $_name;
    
    
    /**
     * @var boolean  доступность элемента
     */     
    protected $_enabled = true;
    
    
    /**
     * @var type Элемент только для чтения
     */
    protected $_readonly = false;
    
    /**
     * @var string  Заголовок элемента
     */    
    protected $_title = '';
    
    
    /**
     * @var mixed  Значение элемента
     */    
    protected $_value;

    
    /**
     * @var type Необходимое поле
     */
    protected $_required;
    

    /**
     * @var string  Кастомный атрибут
     */
    protected $_custom_attr;
    
        
    /**
     * Задать CSS классы для элемента
     * 
     * @param string $classes  Перечень классов
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setClasses($classes)
    {
        $this->_classes = $classes;
        
        return $this;
    }
    
    
    /**
     * Задать инлайн стиль для элемента
     * 
     * @param string $style  инлайн стиль
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setStyle($style)
    {
        $this->_style = $style;
        
        return $this;
    }
    
    
    /**
     * Задать Имя для элемента
     * 
     * @param string $name  Имя
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setName($name)
    {
        $this->_name = $name;
        
        return $this;
    } 
    
    
    /**
     * Задать доступность элемента
     * 
     * @param boolean $enabled  Флаг доступности
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setEnabled($enabled = true)
    {
        $this->_enabled = (bool)$enabled;
        
        return $this;
    }  


    /**
     * Задать Title элемента
     * 
     * @param string $title
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        
        return $this;
    }     
    
    
    /**
     * Задать значение элемента
     * 
     * @param mixed $value
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setValue($value)
    {
        $this->_value = $value;
        
        return $this;
    }    
    
    
    /**
     * Задать свойство Readonly
     * 
     * @param boolean $readonly Только для чтения (флаг)
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setReadonly($readonly)
    {
        $this->_readonly = (boolean)$readonly;
        
        return $this;
    }
    
    
    /**
     * Задать свойство Required
     * 
     * @param boolean $required Обязательное поле (флаг)
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setRequired($required)
    {
        $this->_required = (boolean)$required;
        
        return $this;
    }    
    
    
    /**
     * Установка произвольного атрибута инлайн
     * 
     * @param string $chunk Кусок html кода
     * 
     * @return \FW\Widget\Form\FormElement
     */
    public function setCustomAttr($chunk)
    {
        $this->_custom_attr = $chunk;
        
        return $this;
    }
}
