<?php

namespace FW\Widget;

/**
 * Класс работы с хлебными крошками
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Breadcrumbs extends Widget
{
    /**
     * @var type Перечень ссылок
     */
    private $_links = [];
    
    
    /**
     * Констурктор класса - иницализация массива ссылок, при необходимости.
     * Массив должен быть матрицей, каждый элемент которой имеет формат: 
     * ['title', 'uri']
     * 
     * @param array $links
     */
    public function __construct($links = [])
    {
        if (is_array($links) && isset($links[0]) 
                && isset($links[0][0]) && isset($links[0][1])) {
            $this->_links = $links;
        }
    }
    
    /**
     * Распечатка виджета
     */
    public function __toString()
    {
        return $this->getContent();
    }
    
    
    /**
     * Добавление ссылки в перечень
     * 
     * @param string $title   Текст ссылки
     * @param string $uri     Адрес перехода
     * 
     * @return \FW\Widget\Breadcrumbs
     */
    public function addLink($title, $uri)
    {
       $this->_links[] = [$title, $uri];
    }
    
    
    /**
     * Формирует строковое представление виджета
     * 
     * @return string
     */
    public function getContent()
    {
        $content = "<div id='crumbs'><ul>";

        foreach ($this->_links as $link)
        {
            if (!empty($link[1])) {
                $content .= "<li><a href='" . $link[1] . "'>" . $link[0] . "</a></li>";
            }
            else {
                $content .= "<li class='last'><a href='#'>" . $link[0] . "</a></li>";
            }
        }

        return $content .= "</ul></div>";
    }
    
}