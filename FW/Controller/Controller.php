<?php

namespace FW\Controller;

use FW\Singleton as S;
use FW\View;
use FW\Widget\Breadcrumbs;


/**
 * Базовый класс контроллера
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Controller
{
    
    /**
     * Получение модели в текущем модуле
     * 
     * @param string    $name   Имя модели
     * @param array     $params Параметры инициализации модели
     * 
     * @return \FW\Model    Инстанс модели
     */
    public function getModel($name, $params = [])
    {
        $module_name = S::get('Router')->getModule();
        $model_name = "Modules" . DS . $module_name . DS . "Model" . DS . $name;
        
        if (!file_exists(ROOT_DIR . $model_name . '.php')) {
            throw new \Exception('Класс ' . $model_name . ' не найден!');
        }
        
        $model_name = str_replace(DS, "\\", $model_name);
        return new $model_name($params);
    }
    
    
    /**
     * Получение представления
     * 
     * @param string    $name   Имя представления
     * @param array     $params Параметры, передаваемые в представление
     * 
     * @return \FW\View
     */
    public function getView($name = '', $params = [])
    {
        return new View($name, $params);
    }
    
    
    /**
     * Получение шаблона
     * 
     * @return \FW\Page
     */
    public function getPage()
    {
        return S::get('Page');
    }      
    
    
    /**
     * Получение блока работы с ответом приложения
     * 
     * @return \FW\Response
     */
    public function getResponse()
    {
        return S::get('Response');
    }
    
    
    /**
     * Редирект
     */
    protected function _redirect($url)
    {
        return $this->getResponse()->redirect($url);
    }
    
    
    /**
     * Страница не найдена (404)
     * 
     * @throws \Exception
     */
    protected function _pageNotFound404()
    {
        $this->getResponse()->setCode(404);
        
        //возвращаем в App шаблон страницы 404
        return $this->_setLayout(PAGE_DEFAULT_404);
    }
    
    
    /**
     * Устанавливает шаблон
     * 
     * @param string $layout
     * @return \FW\Page
     */
    protected function _setLayout($layout)
    {
        return $this->getPage()->setLayout($layout);        
    }
    
    
    /**
     * Инициализация и установка хлеба в страницу (Page)
     * 
     * @param array $bc_array
     *     
     * @return \FW\Pages 
     */
    public function setBreadcrumbs($bc_array)
    {
        $bc = new Breadcrumbs($bc_array);
        
        return $this->getPage()->setBreadcrumbs($bc);  
    }
    
        
    /**
     * Отключение шаблонов
     * 
     * @return \FW\Pages 
     */
    public function disableLayouts()
    {
        return $this->getPage()->disableLayouts();
    }
    
    
    /**
     * Включение шаблонов
     * 
     * @return \FW\Pages 
     */
    public function enableLayouts()
    {
        return $this->getPage()->enableLayouts();
    }        
}