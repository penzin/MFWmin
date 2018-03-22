<?php

namespace FW\Widget\GridElements;

use FW\Widget\Widget;

class Actions extends Widget
{
    
    private $_actions;
    
    
    private $_class = 'form-control w200';
    
    
    private $_name = 'sb_group_action';
    
    
    /**
     * 
     * @param array $actions
     */
    public function __construct($actions = []) 
    {
        if (!is_array($actions) || count($actions) == 0) {
            $this->_actions = ['Удалить' => 'delete'];
            return;
        } 
        
        $this->_actions = $actions;
    }

    
    public function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }
    
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
            
    
    
    public function __toString() 
    {
        $output  = "<span class='mr_5'>Действие для выделенных элементов</span> ";
        $output .= "<select class='mr_5 " . $this->_class . "' id='" . $this->_id . "'>";
        foreach ($this->_actions as $label => $action)
        {
            $output .= "<option value='" . $action . "'>" . $label . "</option>";
        }
        $output .= "</select>";
        $output .= "<button class='btn btn-success'>Применить</button>";
        return $output;
    }
    

}
