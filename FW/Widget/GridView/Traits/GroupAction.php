<?php

namespace FW\Widget\GridView\Traits;

use FW\Request as R;

/**
 * Трейт для роутинга групповых действий в объектах типа GridView
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
trait GroupAction 
{
    /**
     * Роутинг групповых действий
     * 
     * <b>для обработки действия с именем <i>name</i> должен быть приватный 
     * метод вида <i>_nameGroupAction</i></b>, иначе будет возвращено 404
     * 
     * @return mixed
     */
    public function group_actionAction()
    {
        $post_data = R::post();
        
        //валидация
        if (empty($post_data) || !isset($post_data['selected_id']) 
                || !is_array($post_data['selected_id']) 
                || !isset($post_data['sb_group_action'])
                || !method_exists(
                        $this, 
                        "_" . $post_data['sb_group_action'] . "GroupAction")) {
            return $this->_pageNotFound404();
        }
        
        //роутинг
        $method_name = "_" . $post_data['sb_group_action'] . "GroupAction";
        return $this->$method_name($post_data['selected_id']);
    }   
}