<?php

namespace FW\Widget\GridView;

/**
 * Класс для работы с групповым действием в GridView
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class GroupAction extends Action
{   
    /**
     *
     * @var string Имя действия
     */
    private $_action = '';
    
    
    /**
     * Конструктор группового действия - инициализация полей
     * 
     * @param array     $action                 массив для инициализации действия
     *                                          обработки
     */
    public function __construct($action) 
    {
        if (!is_array($action) || !isset($action['name']) 
                || !isset($action['action']))
        {
            throw new \Exception('Не заданы обязательные параметры '
                    . 'при инициализации группового действия');
        }
        
        $this->_name = $action['name'];
        $this->_action = $action['action'];
        
        //заголовок действия
        if (isset($action['label'])) {
            $this->_label = $action['label'];
        } 
        else {
            $this->_label = $this->_name;
        }
    }
    
    
    /**
     * Возвращает имя действия
     * 
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    
    /**
     * Возвращает заголовок действия
     * 
     * @return string
     */
    public function getlabel()
    {
        return (empty($this->_label)) ? $this->_name : $this->_label;
    }
}
