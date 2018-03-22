<?php

namespace FW\Traits;

/**
 * Трейт для формирования вложенного массива из плоского с полем id_parent
 * 
 * @author fedyak <fedyak.82@gmail.com>
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
trait ArrayTree {


    /**
     * Рекурсивная функция формирования дерева
     * 
     * @param type $list
     * @param type $parent
     * @return type
     */
    private function _createTree(&$list, $parent) 
    {
        $tree = [];
        
        foreach ($parent as $k => &$l) {
            if(isset($list[$l['id']])) {
                $l['nodes'] = $this->_createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        } 
        
        return $tree;
    }     
    
    
    /**
     * Функция инициализции рекурсивного алгоритма формирования дерева
     * 
     * @param array $array          Исходный массив
     * @param string $parent_name   Имя колонки с id_parent
     * 
     * @return array
     */
    private function _getTreeFromArray(&$array, $parent_name = 'id_parent')
    {
        $new = [];
        
        foreach ($array as $a) {
            if (empty($a[$parent_name])) {
                $a[$parent_name] = 0;
            }
            $new[$a[$parent_name]][] = $a;
        }
        
        $tree = $this->_createTree($new, $new[0]); 
        
        return $tree;
    }
    
       
    /**
     * Рекурсивная функция разворачивания дерева в плоский массив
     * 
     * @param array     $array          Исходный массив
     * @param string    $childs_name    Имя ключа, содержащего родителей
     * @param mixed     $id_parent      Значение parent_id родительского элемента
     * 
     * @return array
     */
    private function _getPlainArrayFromTreeRecursive(&$array, $childs_name = 'children', $id_parent = null)
    {
        $out = [];
        foreach ($array as $a)
        {
            if (isset($a[$childs_name])) {
                $pack = $this->_getPlainArrayFromTreeRecursive($a[$childs_name], $childs_name, $a['id']);
                $a['id_parent'] = $id_parent;
                unset($a[$childs_name]);
                $out[] = $a;                
                $out = array_merge($out, $pack);
            }
            else {
                $a['id_parent'] = $id_parent;
                $out[] = $a;                
            }            
        }
        
        return $out;
    }
    
    
    
}