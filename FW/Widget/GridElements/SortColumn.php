<?php

namespace FW\Widget\GridElements;

use FW\Widget\Widget;

class SortColumn extends Widget
{
    
    private $_label;
    
    
    private $_field_name;
    
    
    /**
     * 
     * @param string $label
     * @param string $field_name
     */
    public function __construct($label, $field_name) 
    {
        $this->_label      = $label;
        $this->_field_name = $field_name;
    }
    
    
    public function __toString()
    {
        /* @var $r \FW\Request */
        $r = \FW\Singleton::get('Request');
        
        $query_params = $r->getQueryParams();        
        
        $class = $sort_dir = '';
        if ($this->_isActive($query_params)) {
            $sort_dir = strtolower($this->_getSortDir($query_params));
            $class = 'sort ';
        }
        
        $output  = "<a class='" . $class . $sort_dir . "' ";
        $output .= "href='" . $this->_getSortQuery($query_params) . "'>";
        $output .= $this->_label;
        $output .= "</a>";
        
        return $output;
    }
    
    
    /**
     * Возвращает признак активности сортировки по текущему полю на основании
     * данных в GET запросе
     * 
     * @param array $query_params Текущий массив параметров строки запроса
     * 
     * @return boolean
     * 
     * @throws \Exception
     */
    private function _isActive($query_params)
    {
        //активна ли сортировка по полю, определение её направления
        if (isset($query_params['sort_by']) && $query_params['sort_by'] == $this->_field_name) {
            return true;
        }
        
        return false;                
    }
    
    
    /**
     * Возвращает текущее направление сортировки по полю
     * 
     * @param array $query_params Текущий массив параметров строки запроса
     * 
     * @return string (ASC, DESC, NO)
     * 
     * @throws \Exception
     */
    private function _getSortDir($query_params)
    {
        //активна ли сортировка по полю, определение её направления
        if (isset($query_params['sort_by']) 
                && $query_params['sort_by'] == $this->_field_name
                && isset($query_params['sort_dir'])) {
            
            return $query_params['sort_dir'];
        }
        
        return "NO";
    }  
    
    
    /**
     * Возвращает часть строки запроса после "?" для ссылки в элементе управления
     * сортировкой. Все прочие GET апраметры будут сохранены
     * 
     * @param array $query_params Текущий массив параметров строки запроса
     * 
     * @return string
     * 
     * @throws \Exception
     */
    private function _getSortQuery($query_params)
    {
        $current_dir = $this->_getSortDir($query_params);
        
        $inverted_sort_dir = ['ASC' => 'DESC', 'DESC' => 'ASC', 'NO' => 'ASC'];
        
        unset($query_params['sort_by']);
        unset($query_params['sort_dir']);
        
        if (!empty($query_params)) {            
            $query = "?" . http_build_query($query_params) 
                    . "&sort_by=" . $this->_field_name . "&sort_dir=" . $inverted_sort_dir[$current_dir];
        } 
        else {
            $query = "?sort_by=" . $this->_field_name . "&sort_dir=" . $inverted_sort_dir[$current_dir];
        }
        
        return $query;
    }    
    
    

    
    
}