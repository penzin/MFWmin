<?php

namespace FW\Helpers;

/**
 * Хэлпер для работы с датами
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
final class DateUtils
{
    /**
     * Представление секунд в удобоваримом для млекопитающего формате
     * 
     * @param integer $seconds        Количество секунд
     * @param boolean $with_days      Добавлять ли сутки в ответ
     * 
     * @return string
     */
    public static function secToHuman($seconds, $with_days = false)
    {      
        if ($with_days) {
            $out_days = floor($seconds / (60 * 60 * 24));
        }
        else {
            $out_days = 0;
        }

        $out_hours = floor(($seconds - $out_days * 60 * 60 * 24) / (60 * 60));

        $out_minutes = floor(($seconds - $out_days * 60 * 60 * 24 - $out_hours * 60 * 60) / 60);

        $out_seconds = $seconds - $out_days * 60 * 60 * 24 - $out_hours * 60 * 60 - $out_minutes * 60;

        $out_days = ($with_days) ? str_pad($out_days, 2, "0", STR_PAD_LEFT) . ":" : "";
        $out_hours = str_pad($out_hours, 2, "0", STR_PAD_LEFT);
        $out_minutes = str_pad($out_minutes, 2, "0", STR_PAD_LEFT);
        $out_seconds = str_pad($out_seconds, 2, "0", STR_PAD_LEFT);

        return "$out_days$out_hours:$out_minutes:$out_seconds";
    }  
    
    
    /**
     * преобразование даты в ГОСТ формат
     * 
     * @param string $date  Дата в формате ISO
     * 
     * @return string
     */
    public static function toGOST($date)
    {
        return date('d.m.Y H:i:s', strtotime($date));
    }
    
    
    /**
     * Перевод Даты в формате GOST в ISO формат
     * 
     * @param type $gost_date
     */
    public static function GOSTtoISO($gost_date)
    {
        return date('Y-m-d H:i:s', strtotime($gost_date));
    }    
}
