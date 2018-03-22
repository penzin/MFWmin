<?php

namespace FW\Widget\GridView;

/**
 * Класс для работы с внутристрочным действием в GridView
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class RowAction extends Action
{
    /**
     * @var string Шаблон (код HTML) для отображения виджета кнопки действия
     */
    private $_content;
    
    
    /**
     * Конструктор действия - инициализация полей
     * 
     * @param array     $action                 массив для инициализации действия
     *                                          обработки
     */
    public function __construct($action) 
    {
        if (!is_array($action) || !isset($action['name']) 
                || !isset($action['content']))
        {
            throw new \Exception('Не заданы обязательные параметры '
                    . 'при инициализации внутристрочного действия');
        }
        
        $this->_name = $action['name'];
        
        $this->_content = $action['content'];      
        
        //заголовок действия
        if (isset($action['label'])) {
            $this->_label = $action['label'];
        } 
        else {
            $this->_label = $this->_name;
        }
    }
    
    
    /**
     * Распечатка элемента
     * 
     * @return string
     */
    public function __toString() 
    {
        $ph = [':name', ':label'];
        
        return str_replace($ph, [$this->_name, $this->_label], $this->_content);
    }     
}
