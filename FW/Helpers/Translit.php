<?php

namespace FW\Helpers;

/**
 * Транслитерация
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 * @author fedyak <fedyak.82@gmail.com>
 */
class Translit 
{
    
    /**
     * РУС в АНГЛ
     * 
     * @param type $str
     * @param boolean $remove_dot       Удалить "." (по умолчанию true),
     *                                  Если false "." преобразуется в "-"
     * @return type
     */
    public static function ru2en($str, $remove_dot = true)
    {
        $tr = [
            "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g","Д"=>"d",
            "Е"=>"e","Ж"=>"zh","З"=>"z","И"=>"i","Й"=>"y",
            "К"=>"k","Л"=>"l","М"=>"m","Н"=>"n","О"=>"o",
            "П"=>"p","Р"=>"r","С"=>"s","Т"=>"t","У"=>"u",
            "Ф"=>"f","Х"=>"h","Ц"=>"c","Ч"=>"ch","Ш"=>"sh",
            "Щ"=>"sch","Ъ"=>"","Ы"=>"y","Ь"=>"","Э"=>"e",
            "Ю"=>"yu","Я"=>"ya",
            "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
            "е"=>"e","ж"=>"zh","з"=>"z","и"=>"i","й"=>"y",
            "к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o",
            "п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u",
            "ф"=>"f","х"=>"h","ц"=>"c","ч"=>"ch","ш"=>"sh",
            "щ"=>"sch","ъ"=>"","ы"=>"y","ь"=>"","э"=>"e",
            "ю"=>"yu","я"=>"ya",
            " "=> "-", "/"=> "-", "-"=> "-", ":"=>"-", "."=>"-"
        ];

        if ($remove_dot) {
            $tr["."] = "";
        }    

        $str = str_replace("  ", " ", trim($str));

        $text = strtr($str, $tr);

        if (preg_match('/[^A-Za-z0-9_\-\.]/', $text)){
            $text = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $text);
        }

        $text = str_replace("--", "-", $text);

        return $text;
    }
  
  
    
  
}


