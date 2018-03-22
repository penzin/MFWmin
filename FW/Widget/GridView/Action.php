<?php

namespace FW\Widget\GridView;

/**
 * Класс для описания действия (группового или внутристрочного) в GridView
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
abstract class Action
{
    /**
     * @var string  Идентификатор 
     */
    protected $_name;
    
    
    /**
     *
     * @var string  Заголовок действия / всплывающая подсказка
     */    
    protected $_label;
}
