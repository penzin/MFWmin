<?php

namespace FW\Widget;

use FW\Singleton as S;

/**
 * Базовый класс Widget
 * Родитель для любого виджета (элемента, отображаемого на странице как 
 * отдельный блок)
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
abstract class Widget 
{
    
    /**
     * @var array Массив для DATA-bind
     */
    private $_data = [];
    
    
    /**
     * @var string  Идентификатор 
     */
    protected $_id;
    
    
    /**
     * @var boolean Видимость
     */
    protected $_visible = true;


    /**
     * Задать ID для виджета
     * 
     * @param string $id    Идентификатор объекта
     * 
     * @return \FW\Widget
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    
    /**
     * Добавить свойство DATA
     * 
     * @param string $name
     * @param mixed $value
     * @return \FW\Widget\Widget
     */
    public function data($name, $value = null)
    {
        if (is_null($value)) {
            return array_key_exists($name, $this->_data) ? $this->_data[$name] : null;
        }
        
        $this->_data[$name] = $value;
        
        return $this;
    }
    

    
    protected function _getDataBind()
    {
        if (count($this->_data) == 0) {
            return null;
        }
        
        $data = '';
        foreach ($this->_data as $name => $val) {
            $data .= "data-{$name}='{$val}' ";
        }
        
        return $data;
    }



    /**
     * Задать режим видимости
     * 
     * @param boolean $visible  Флаг видимости
     * 
     * @return \FW\Widget
     */
    public function setVisible($visible = true)
    {
        $this->_visible = (bool)$visible;
        return $this;
    }
    
    
    /**
     * Возвращает кусок HTML(JS) кода для виджета
     * 
     * @param string $file_name     Имя файла шаблона (должен располагаться
     *                              в папке Templates виджета)
     * @param array $params         Переменные которые можно внедрить в шаблон
     * @return string
     */
    protected function _getWidgetTemplate($file_name, $params = [])
    {
        $r = new \ReflectionClass($this);
        $template_path = \FW\Files\File::getFileDir($r->getFileName()) . DS . "Templates" . DS;
        
        //если файл не найден в директории виджета, попробуем поискать
        //его в директории Templates текущего модуля
        if (!file_exists($template_path . $file_name . ".phtml")) {
            $router = S::get('FW\Router');
            $template_path = ROOT_DIR . 'Modules' . DS . $router->getModule()
                    . DS . 'Templates' . DS;

            //если и в модуле такого шаблона нет - вернем null
            if (!file_exists($template_path . $file_name . ".phtml")) {            
                return null;
            }
        }
        
        ob_start();
            extract($params);
            include $template_path . $file_name . ".phtml";
            $output = ob_get_contents();
        ob_end_clean();
        
        return $output;
    }


    /**
     * Распечатка объекта
     */
    abstract public function __toString();    
}
