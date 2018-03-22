<?php

namespace FW\Widget;


use FW\Singleton as S;

/**
 * Класс для отображения элементов управления пагинацией
 * 
 * @author fedyak <fedyak.82@gmail.com>
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 * 
 */
class Paginator extends Widget
{
    
    
    /**
     * @var int Номер текущей страницы
     */
    private $_cur_page;
   
    
    /**
     * @var int Общее количество страниц
     */
    private $_total_pages;
   
    
    /**
     * @var string  Имя GET параметра, для управления страницами
     */
    private $_get_param = "page";
    
    
   
    /**
     * @var type Количество страниц для вывода в виджите
     */
    private $_range;
    
    
              
    /**
     * @var string Кастомный URI адрес (для случая, если таблицу вставляют во
     *             вкладку и т.п.)
     */
    private $_custom_uri = '';
    
    
    
    /**
     *
     * @var string  Фрагмент (символы после #, если есть)
     */
    private $_custom_uri_fragment = '';    
    
    
    /**
     * Распечатка объекта
     * 
     * @return string
     */
    public function __toString() 
    {
        return (string)$this->_getContent();
    }
   
    
    /**
     * Конструктор класса - инициализация объекта пагинации
     * 
     * @param integer $count           Общее количество элементов
     * @param integer $page            Текущая страница
     * @param integer $per_page        Записей на странице
     * @param integer $range           Количество страниц, отображаемых на виджете
     * @param string $request_param   Имя параметра пагинации из GET запроса
     */
    public function __construct($count, $page, $per_page, $range = 5, $request_param = 'page') 
    {
        if ((int)$count == 0) {
            return false;
        }

        $total_pages = intval(($count - 1) / $per_page) + 1;

        if ($page == 0) {
            $page = 1;
        }
        
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $this->_total_pages = (int) $total_pages;        
        $this->_cur_page    = (int) $page;        
        $this->_range       = (int) $range;        
        $this->_get_param   = $request_param;        
    }    
  
   
   
    /**
     * Получение HTML-кода виджета пагинации
     * 
     * @todo Рефакторинг?
     * 
     * @return string|null
     */
    private function _getContent()
    {        
        $page           = $this->_cur_page;
        $total_pages    = $this->_total_pages;        
        
        if ($total_pages == 1 || $total_pages < $page || empty($total_pages)) {
            return '';
        }

        //массив get-параметров, без учета $this->_get_param (page)
        $params = array_diff_key(
                S::get('Request')->getQueryParams(), 
                [$this->_get_param => null]);                        
        
        //текст запроса
        $uri        = (empty($this->_custom_uri)) ? 
                S::get('Request')->getRequestURI() : $this->_custom_uri;
        
        //сепараторы
        if (count($params) > 0) {
            $separator1 = "?";
            $separator2 = "&amp;";
        }
        else {
            $separator1 = "";
            $separator2 = "?";
        }

        $script = $uri . $separator1 . http_build_query($params) . $separator2 . $this->_get_param;

        $pervpage = $lastpage = $browse_left = $browse_right = '';

        $curr = "<li class='active'><span>" . $page . "</span></li>";

        if ($page - 1 > 0) {
            $browse_left  = "<li>";
            $browse_left .= "<a data-page='" . ($page - 1) . "' href='" . $script . "=" . ($page - 1) . "{$this->_custom_uri_fragment}'>";
            $browse_left .= "<span>&laquo;</span>";
            $browse_left .= "</a>";
            $browse_left .= "</li>";
        }
        
        if ($page + 1 <= $total_pages) {
            $browse_right  = "<li>";
            $browse_right .= "<a data-page='" . ($page + 1) . "' href='" . $script . "=". ($page + 1) . "{$this->_custom_uri_fragment}'>";
            $browse_right .= "<span>&raquo;</span>";
            $browse_right .= "</a>";
        }

        $pages_left = $pages_right = '';
        
        for ($i = 1; $i < $this->_range; $i++)
        {
            if ($page - $i > 0) {
                $p  = "<li>";
                $p .= "<a data-page='" . ($page - $i) . "' href='" . $script . "=" . ($page - $i) . "{$this->_custom_uri_fragment}'>" . ($page - $i) . "</a>";
                $p .= "</li>";
                $pages_left  = $p . $pages_left;
            }

            if ($page + $i <= $total_pages) {
                $p  = "<li>";
                $p .= "<a data-page='" . ($page + $i) . "' href='" . $script . "=" . ($page + $i) . "{$this->_custom_uri_fragment}'>" . ($page + $i) . "</a>";
                $p .= "</li>";
                $pages_right  = $pages_right . $p;
            }
        }

        $pagination_str = "<nav><ul class='pagination'>";
        $pagination_str .= $browse_left . $pervpage . $pages_left . $curr . 
                           $pages_right . $lastpage . $browse_right;
        $pagination_str .= "</ul></nav>";

        return $pagination_str;
    } 
    
    
    /**
     * Задать кастомный URI для пагинатора
     * 
     * @param string $uri  Кастомный uri
     * 
     * @return \FW\Widget\Paginator
     */
    public function setCustomURI($uri)
    {
        $parts = explode("#", $uri);
        
        if (count($parts) > 1 ) {
            $this->_custom_uri = $parts[0];
            $this->_custom_uri_fragment = "#" . $parts[1];
        }
        else {        
            $this->_custom_uri = $uri;
        }
        
        return $this;
    }        
     
    
}