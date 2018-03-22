<?php

namespace FW;

use FW\Singleton as S;
use FW\Widget\Breadcrumbs;

/**
 * Базовый класс шаблонизатора (Страницы)
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Page
{
    /**
     * @var FW\View Представление; 
     */
    private $_view;
    
    
    /**
     * @var type Текущий отображаемый шаблон
     */
    private $_layout = '';
    
    
    /**
     * @var boolean Флаг использования шаблонов при отображении страницы
     */
    private $_use_layouts = true;
    
    
    /**
     *
     * @var Breadcrumbs Хлебные крошки
     */
    private $_breadcrumbs;
    
    
    /**
     * Определение имени файла шаблона по наименованию
     * 
     * @param string $name  Наименование шаблона
     * 
     * @return string;
     * @throws \Exception
     */
    private function _getLayoutByName($name)
    {
        $layouts = S::get('Config')->getLayouts();
        
        if (!empty($layouts) && is_array($layouts) && array_key_exists($name, $layouts)) {
            return $layouts[$name];
        }
        
        throw new \Exception('Шаблон ' . $name . ' не найден');
    }
    
    
    /**
     * Вывод представления на экран с/без примением/я шаблонов
     */
    public function render()
    {
        if ($this->_use_layouts) 
        {  
            //если шаблон не установлен, берем шаблон по умолчанию
            if (empty($this->_layout)) {
                $this->_layout = 'default';
            } 
            
            $layout_fname = ROOT_DIR . "Modules" . DS . $this->_getLayoutByName($this->_layout);

            //проверка существования файла шаблона
            if (!file_exists($layout_fname)) {
                throw new \Exception("Шаблон {$this->_layout} не найден!");
            }
            
            //Save current dir
            $cur_dir = getcwd();
            
            ob_start();
                
                //Меняем текущий каталог (для нормального инклюда файлов в шаблоне)
                $layout_path = pathinfo(realpath($layout_fname));               
                chdir($layout_path['dirname']);
                
                require $layout_fname;
                $layout = ob_get_contents();
                
            ob_end_clean();
            
            //Restore saved dir
            chdir($cur_dir);
            
            if (!defined('ADMIN_MODULE') || ADMIN_MODULE === false) {
                $this->_inlineWidgets($layout);
            }
            
            echo $layout;
        }
        else {
            $layout = $this->_view->getContent();
            if (!defined('ADMIN_MODULE') || ADMIN_MODULE === false) {
                $this->_inlineWidgets($layout);
            }
            echo $layout;
        }
        exit();
    }
    
    
    
    /**
     * Загрузка виджета по тэгу вида {{SOME_WIDGET}}
     * 
     * @param string $layout    Тело страницы
     * 
     * @return boolean
     */
    private function _inlineWidgets(&$layout)
    {
        $temp = [];
        preg_match_all("/{{2}[A-z0-9_=]+:{0,1}[0-9]{0,}}{2}/", $layout, $temp);
        if (!array_key_exists(0, $temp)) {
            return false;
        }

        $widgets = array_unique($temp[0]);
           
        $registered_widgets = S::get('Config')->getWidgets();
        
        foreach ($widgets as &$c)
        {
            $t = explode(':', str_replace(['{{','}}'], '' , $c));
            
            if (($t === false) || (array_key_exists(0, $t) && !array_key_exists($t[0], $registered_widgets))) {
                continue;
            }
            
            $widget_class = explode("/", $registered_widgets[$t[0]]);
            
            $params = array_key_exists(1, $t) ? $t[1] : null;
            
            $class = DS . "Modules" . DS . $widget_class[0] . DS . "Controller" . DS . $widget_class[1] . "Controller";
            
            $action = $widget_class[2] . "Action";
            
            if (file_exists(ROOT_DIR . $class.".php")) {
                ob_start();
                
                    S::get('Router')->saveState();
                    
                    S::get('Router')->setModule($widget_class[0]);
                    S::get('Router')->setController($widget_class[1]);
                    S::get('Router')->setAction($widget_class[2]);
                    
                    $class = str_replace(DS, "\\", $class);
                    
                    echo (new $class($params))->$action($params);
                    $view = ob_get_contents();
                    
                    S::get('Router')->restoreState();
                    
                ob_end_clean();
                $layout = str_replace($c, $view, $layout);
            }
        }        
    }    
    
    
    
    /**
     * Отключение шаблонов
     * 
     * @return void
     * 
     * @return \FW\Page
     */
    public function disableLayouts()
    {
        $this->_use_layouts = false;
        
        return $this;
    }
    
    
    /**
     * Включение шаблонов
     * 
     * @return void
     * 
     * @return \FW\Page
     */
    public function enableLayouts()
    {
        $this->_use_layouts = true;
        
        return $this;
    }  
    
    
    /**
     * Установить текущий шаблон
     * 
     * @param string $name  Наименование шаблона
     * 
     * @return \FW\Page
     */
    public function setLayout($name)
    {
        $this->_layout = $name;
        
        return $this;
    }
    
    
    /**
     * Установка основного представления в шаблон
     * 
     * @param \FW\View $view
     * 
     * @return \FW\Page
     */
    public function setView(View $view)
    {
        //загрузили предствление
        $this->_view = $view;
        
        return $this;
    }
    
    
    /**
     * Возвращает основное представление
     * 
     * @return \FW\View
     */
    public function getView()
    {
        return $this->_view;
    }
    
    
    /**
     * Установка хлебных крошек
     * 
     * @param Breadcrumbs $breadcrumbs  Объект хлебных крошек
     * 
     * @return Page
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->_breadcrumbs = $breadcrumbs;
        
        return $this;
    }
    
    
    /**
     * Возвращает объект хлебных крошек
     * 
     * @return Breadcrumbs
     */
    public function getBreadcrumbs()
    {
        return $this->_breadcrumbs;
    }
    

    /**
     * Возвращает любое представление в шаблон
     * 
     * @param array     $params     Параметры, передаваемые в представление
     * @param string    $name       Имя представления
     * 
     * @return \FW\View
     */
    public function getCustomView($params = [], $name = '')
    {
        return new View($params, $name);
    }
    
    
    /**
     * Возращает виджет модуля
     * 
     * @param string $widget_name
     * @param array $params
     * @return \FW\Widget\ModuleWidget
     */
    public function getWidget($widget_name, $params = [])
    {
        $widget_class_fn = "Modules" . DS . str_replace("\\", DS, $widget_name);
        $widget_class    = "Modules\\" . $widget_name;
        
        if (!file_exists(ROOT_DIR . $widget_class_fn . '.php')) {
            throw new \Exception('Файл класса ' . $widget_class . ' не найден!');
        }
        
        if (!is_array($params)) {
            throw new \Exception('Неверный тип аргумента при создании класса ' . $widget_class);
        }        
        
        return new $widget_class($params);
    }
    
    
}
