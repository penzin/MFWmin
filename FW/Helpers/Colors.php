<?php

namespace FW\Helpers;

/**
 * Работа с цветами
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
class Colors
{
    

    /**
     * Темнее
     * 
     * @param string $rgb           Цвет в формате HEX
     * @param int $darker           Степень
     * @return string               Цвет в формате HEX (без #)
     */
    public static function darken($rgb, $darker = 1.2) 
    {
        if (empty($rgb)) {
            return '';
        }
        
        $darker = ($darker > 1) ? $darker : 1;

        list($R16, $G16, $B16) = str_split(str_replace('#', '', $rgb), 2);

        $R = sprintf("%02X", floor(hexdec($R16) / $darker));
        $G = sprintf("%02X", floor(hexdec($G16) / $darker));
        $B = sprintf("%02X", floor(hexdec($B16) / $darker));

        return $R . $G . $B;
    }

    
    /**
     * Контраст
     * 
     * @param string $back_color        Цвет в формате HEX
     * @return string                   Цвет в формате HEX (без #)
     */
    public static function contrastColor($back_color)
    {
        if (empty($back_color)) {
            return '';
        }        
        
        list($R16, $G16, $B16) = str_split(str_replace('#', '', $back_color), 2);

        (double) $a = 1 - (0.299 * floor(hexdec($R16)) + 0.587 * floor(hexdec($G16)) + 0.114 * floor(hexdec($B16))) / 255;

        $d = ($a < 0.5) ? $d = 0 : $d = 255;

        $R = sprintf("%02X", $d);
        $G = sprintf("%02X", $d);
        $B = sprintf("%02X", $d);
        
        return $R . $G . $B;
    }    
    
    
}