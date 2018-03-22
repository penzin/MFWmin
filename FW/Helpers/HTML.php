<?php

namespace FW\Helpers;

/**
 * Хелпер HTML
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
final class HTML
{

    /**
     * Конвертирует все символы в строке (кроме букв) в мнемоники HTML. 
     * Используется для защиты от XSS, являясь более гибким аналогом htmlspecialchars.
     * 
     * @param string $text
     * @return string
     */
    public static function esc($text)
    {
        return htmlentities($text, ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8');
    }
    
    
    
    
}