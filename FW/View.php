<?php

namespace FW;

/**
 * Базовый клас представления
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class View
{
    /**
     * @var string  Содержимое представления
     */
    private $_content = '';
    
    
    /**
     * @var array   Подключаемые файлы скриптов
     */
    private $_scripts = [];
    
    
    /**
     * @var array   Подключаемые файлы стилей CSS
     */
    private $_css = [];
    
    
    /**
     * @var string  Заголовок страницы
     */
    private $_title;
    
    
    /**
     * @var string  Метатег keywords
     */    
    private $_meta_keywords;
    
    
    /**
     * @var string  Метатег author
     */       
    private $_meta_author;
    
    
    /**
     * @var string  Метатег copyright
     */       
    private $_meta_copyright;
    
    
    /**
     * @var string  Метатег description
     */   
    private $_meta_description;
    
    
    /**
     * @var string  Метатег robots
     */       
    private $_meta_robots;
    

    /**
     * @var string  Метатег resource type
     */   
    private $_meta_resource_type;
    
    
    /**
     * @var string    Метатег content-type
     */
    private $_meta_content_type;
    
    
    /**
     * @var string  Метатег charset   
     */
    private $_meta_content_charset;
    
    
    /**
     * @var string  Метатег generator
     */       
    private $_meta_generator;
    
    
    /**
     * @var string Иконка страницы
     */
    private $_favicon;
    
    
    /**
     * <link rel="canonical" href="..." />
     * @var string 
     */
    private $_canonical;
    
    
    /**
     * Конструктор представления - загрузка файла представления
     * 
     * @param array  $params    параметры, передаваемые в представление
     * @param string $name      Имя представления (начиная с имени модуля, и без расширения)
     */
    public function __construct($params = [], $name = '') 
    {
        $this->_title                = (defined('VIEW_DEFAULT_TITLE') ? VIEW_DEFAULT_TITLE : '');
        $this->_meta_keywords        = (defined('VIEW_DEFAULT_KEYWORDS') ? VIEW_DEFAULT_KEYWORDS : '');
        $this->_meta_description     = (defined('VIEW_DEFAULT_DESCRIPTION') ? VIEW_DEFAULT_DESCRIPTION : '');
        $this->_meta_copyright       = (defined('VIEW_DEFAULT_COPYRIGHT') ? VIEW_DEFAULT_COPYRIGHT : '');
        $this->_meta_robots          = (defined('VIEW_DEFAULT_ROBOTS') ? VIEW_DEFAULT_ROBOTS : '');
        $this->_meta_resource_type   = (defined('VIEW_DEFAULT_RESOURCE_TYPE') ? VIEW_DEFAULT_RESOURCE_TYPE : '');
        $this->_meta_content_type    = (defined('VIEW_DEFAULT_CONTENT_TYPE') ? VIEW_DEFAULT_CONTENT_TYPE : '');
        $this->_meta_content_charset = (defined('VIEW_DEFAULT_CONTENT_CHARSET') ? VIEW_DEFAULT_CONTENT_CHARSET : '');
        $this->_meta_generator       = (defined('VIEW_DEFAULT_GENERATOR') ? VIEW_DEFAULT_GENERATOR : '');
        $this->_favicon              = (defined('VIEW_DEFAULT_FAVICON') ? VIEW_DEFAULT_FAVICON : '');
        $this->_meta_author          = (defined('VIEW_DEFAULT_AUTHOR') ? VIEW_DEFAULT_AUTHOR : '');
        
        
        $view_name = $this->_getViewName($name);

        if (!file_exists($view_name)) {
            throw new \Exception('Файл представления "' . $view_name . '" не найден!');
        }
        
        if (!array_key_exists('H', $params)) {
            $params['H'] = (\FW\HTML\HTML::class);
        }        
        
        //записываем содержимое представления во внутреннюю переменную
        ob_start();
            extract($params);
            include $view_name;
            $this->_content = ob_get_contents();
        ob_end_clean();
    }
    
    
    /**
     * 
     * @param string $str
     * @return string
     */
    public function esc($str)
    {
        return \FW\HTML\HTML::esc($str);
    }
    
    
    /**
     * Возвращает имя файла представления
     * 
     * @param string $name      
     * @return string|null           Путь к файлу представления
     */
    private function _getViewName($name)
    {
        //получаем имя модуля и имя метода из роутера
        $router = Singleton::get('FW\Router');
        
        //получаем массив представлений из конфигов модулей
        $module_views = Singleton::get('FW\Config')->getViews();
        
        $module     = $router->getModule();
        $controller = $router->getController();
        $action     = $router->getAction();        
        
        //если задан полный путь для представления
        if (strpos($name, "/") !== false) {
            return  ROOT_DIR . 'Modules' . DS . $name . '.phtml';            
        } 
        
        //если имя представления задано в конфиге - берем из него
        if (isset($module_views[$name])) {
            return ROOT_DIR . 'Modules' . DS . $module_views[$name];                    
        }        
        
        //если имя представления не задано, то берем имя действия
        if (empty($name)) {
            return ROOT_DIR . 'Modules' . DS . $module . DS . 'View' . DS . 
                     $controller. DS . $action . ".phtml";
        } 
        
        return ROOT_DIR . 'Modules' . DS . $module . DS . 'View' . DS . 
                     $controller. DS . $name . ".phtml";        
    }
    
    
    /**
     * Распечатка представления
     */
    public function __toString() 
    {
        return $this->getContent();
    }
    
    
    /**
     * Возвращает содержимое представления
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }
    
    
    /**
     * Добавление скрипта в список используемых в представлении
     * 
     * @param string $script    Имя файла скрипта
     * 
     * @return \FW\View
     */
    public function addScript($script)
    {
        $this->_scripts[] = $script;
        
        return $this;
    }
    
    
    /**
     * Добавление таблицы стилей в список используемых в представлении
     * 
     * @param string $css   Имя файла стиля
     * 
     * @return \FW\View
     */
    public function addCSS($css)
    {
        $this->_css[] = $css;
        
        return $this;
    }
    
    
    /**
     * Задать канонический URL
     * 
     * @param string $link Ссылка
     * 
     * @return \FW\View
     */
    public function setCanonical($link)
    {
        $this->_canonical = $link;
        
        return $this;
    }
    
    
    /**
     * Установка заголовка страницы
     * 
     * @param string $title Заголовок страницы
     * 
     * @return \FW\View
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        
        return $this;
    }
    
    
    /**
     * Установка МЕТА-тега ключевые слова
     * 
     * @param string $keywords Перечень ключевых слов
     * 
     * @return \FW\View
     */
    public function setMetaKeywords($keywords)
    {        
        $this->_meta_keywords = $keywords;
        
        return $this;
    }
    
    
    /**
     * Установка МЕТА-тега описание страницы
     * 
     * @param string $description Описание
     * 
     * @return \FW\View
     */
    public function setMetaDescription($description)
    {        
        $this->_meta_description = $description;
        
        return $this;
    }
    
    
    /**
     * Установка МЕТА-тега автор
     * 
     * @param string $author Автор
     * 
     * @return \FW\View
     */
    public function setMetaAuthor($author)
    {        
        $this->_meta_author = $author;
        
        return $this;
    }    
    
    
    /**
     * Установка МЕТА-тега копирайт страницы
     * 
     * @param string $copyright Копирайт страницы
     * 
     * @return \FW\View
     */
    public function setMetaCopyright($copyright)
    {        
        $this->_meta_copyright = $copyright;
        
        return $this;
    }    
    
    
    /**
     * Установка МЕТА-тега генератор
     * 
     * @param string $generator Генератор
     * 
     * @return \FW\View
     */
    public function setMetaGenerator($generator)
    {        
        $this->_meta_generator = $generator;
        
        return $this;
    }    
    
    
    /**
     * Установка МЕТА-тега для Роботов
     * 
     * @param string $robots Роботы
     * 
     * @return \FW\View
     */
    public function setMetaRobots($robots)
    {        
        $this->_meta_robots = $robots;
        
        return $this;
    }    
    
    
    /**
     * Установка МЕТА-тега для типа ресурса
     * 
     * @param string $resource_type Тип ресурса
     * 
     * @return \FW\View
     */
    public function setMetaResourceType($resource_type)
    {        
        $this->_meta_resource_type = $resource_type;
        
        return $this;
    }    
    
    
    /**
     * Установка МЕТА-тега для типа содержания
     * 
     * @param string $content_type Тип контента
     * 
     * @return \FW\View
     */
    public function setMetaContentType($content_type)
    {        
        $this->_meta_content_type = $content_type;
        
        return $this;
    }     
    
    
    /**
     * Установка МЕТА-тега для кодировки содержания
     * 
     * @param string $content_charset Кодировка
     * 
     * @return \FW\View
     */
    public function setMetaContentCharset($content_charset)
    {        
        $this->_meta_content_charset = $content_charset;
        
        return $this;
    }     
    
    
    /**
     * Получение списка скриптов из представления в виде HTML кода
     * 
     * @return string   HTML код подключения скриптов представления
     */
    public function getScripts()
    {
        $out = '';
        
        foreach ($this->_scripts as $script)
        {
            $out .= "<script type='text/javascript' src='$script'></script>\n";
        }
        return $out;
    }
    
    
    /**
     * Получение списка таблиц стилей из представления в виде HTML кода
     * 
     * @return string   HTML код подключения стилей представления
     */
    public function getCSS()
    {
        $out = '';
        
        foreach ($this->_css as $css)
        {
            $out .= "<link href='$css' media='screen' rel='stylesheet' type='text/css'>\n";
        }
        return $out;
    }
    
    
    /**
     * Вывод заголовка представления в виде HTML
     * 
     * @return string    HTML тэг заголовка
     */
    public function getTitleTag()
    {
        return "<title>{$this->_title}</title>\n";
    }
    
    
    /**
     * Вывод <link rel='canonical'> в виде HTML
     * 
     * @return string    Канонический URL
     */
    public function getCanonical()
    {
        return !empty($this->_canonical) ? "<link rel='canonical' href='{$this->_canonical}' />\n" : '';
    }
    
    
    /**
     * Вывод заголовка представления (просто текст)
     * 
     * @return string    Текст заголовка
     */
    public function getTitle()
    {
        return $this->_title;
    }    
    
    
    /**
     * Получение МЕТА-тега ключевые слова
     * 
     * @return string    Текст МЕТА-тега ключевые слова
     */
    public function getMetaKeywordsTag()
    {        
        return "<meta name='keywords' content='{$this->_meta_keywords}' />\n";
    }
    
    
    /**
     * Получение МЕТА-тега описание страницы
     * 
     * @return string    Текст МЕТА-тега описание страницы
     */
    public function getMetaDescriptionTag()
    {        
        return "<meta name='description' content='{$this->_meta_description}' />\n";
    }
    
    
    /**
     * Получение МЕТА-тега автор
     * 
     * @return string    Текст МЕТА-тега автор
     */
    public function getMetaAuthorTag()
    {        
        return "<meta name='author' content='{$this->_meta_author}' />\n";
    }    
    
    
    /**
     * Получение МЕТА-тега копирайт страницы
     * 
     * @return string    Текст МЕТА-тега Копирайт страницы
     */
    public function getMetaCopyrightTag()
    {        
        return "<meta name='copyright' content='{$this->_meta_copyright}' />\n";
    }    
    
    
    /**
     * Получение МЕТА-тега генератор
     * 
     * @return string    Текст МЕТА-тега генератор
     */
    public function getMetaGeneratorTag()
    {        
        return "<meta name='generator' content='{$this->_meta_generator}' />\n";
    }    
    
    
    /**
     * Получение МЕТА-тега для Роботов
     * 
     * @return string    Текст МЕТА-тега для Роботов
     */
    public function getMetaRobotsTag()
    {        
        return "<meta name='robots' content='{$this->_meta_robots}' />\n";
    }    
    
    
    /**
     * Получение МЕТА-тега для типа ресурса
     * 
     * @return string    Тег для типа ресурса
     */
    public function getMetaResourceTypeTag()
    {        
        return "<meta name='resource-type' content='{$this->_meta_resource_type}' />\n";
    }   
    
    
    /**
     * Получение МЕТА-тега для типа контента и кодировки
     * 
     * @return string    Тег для типа контента и кодировки
     */
    public function getMetaContentTypeTag()
    {        
        return "<meta name='content-type' "
             . "content='{$this->_meta_content_type}; "
             . "charset={$this->_meta_content_charset}' />\n";
    }               
    
    
    /**
     * Установка иконки страницы
     * 
     * @param string $icon  Файл иконки страницы
     * 
     * @return \FW\View
     */
    public function setFavicon($icon)
    {
        $this->_favicon = $icon;
        
        return $this; 
    }
    
    
    /**
     * Получение иконки страницы в виде HTML тэга
     * 
     * @return string   Тэг иконки страницы
     */
    public function getFaviconTag()
    {
        return "<link rel='shortcut icon' href='{$this->_favicon}' />\n";
    }
    
}
