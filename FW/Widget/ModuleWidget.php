<?php

namespace FW\Widget;

use FW\Widget\Widget;
use FW\Singleton as S;

/**
 * Базовый класс ModuleWidget
 * Родитель для виджета модуля (элемента, отображаемого на странице как 
 * отдельный блок)
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
abstract class ModuleWidget extends Widget
{
    
    /**
     * Получение модели в текущем модуле
     * 
     * @param string    $name   Имя модели
     * @param array     $params Параметры инициализации модели
     * 
     * @return mixed    Инстанс модели
     */
    public function getModel($name, $params = [])
    {
        //относительный путь к модели
        if (strpos($name, '\\') === false) {
            $module_name = S::get('Router')->getModule();
            $model_name = "Modules" . DS . $module_name . DS . "Model" . DS . $name;
        }
        //абсолютный путь к модели
        else {
            $model_name    = str_replace('\\', DS, $name);
        }
        
        if (!file_exists(ROOT_DIR . $model_name . '.php')) {
            throw new \Exception('Класс "' . $model_name . '" не найден!');
        }
        
        $model_name = str_replace(DS, "\\", $model_name);
        return new $model_name($params);
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
        
        if (!file_exists($template_path . $file_name . ".phtml")) {
            return null;
        }
        
        ob_start();
            extract($params);
            include $template_path . $file_name . ".phtml";
            $output = ob_get_contents();
        ob_end_clean();
        
        return $output;
    }
    
    

}