<?php

namespace FW;

/**
 * Класс для вывода объекта \Exception на печать (диагностика)
 */
class DisplayException
{
    /**
     * Распечатка исключения
     * 
     * @param \Exception $e Перехваченное исключение
     * 
     * @return satring
     */
    public static function show(\Exception $e)
    {
        $return = "<table cellspacing='2' cellpadding='5' border ='1'><tr><th colspan='2'>"
                . "<h2>При работе приложения возникло исключение!</h2></th></tr>"
                . "<tr><td>Код исключения</td><td>" . $e->getCode() . "</td></tr>"
                . "<tr><td>Имя файла</td><td>" . $e->getFile() . "</td></tr>"
                . "<tr><td>Строка</td><td>" . $e->getLine() . "</td></tr>"
                . "<tr><td>Сообщение об ошибке</td><td><pre>" . $e->getMessage() . "</pre></td></tr>"
                . "<tr><td>Предыдущее исключение</td><td>" . $e->getPrevious() . "</td></tr>"
                . "<tr><td>Трассировка</td><td>" . str_replace('#', '<br>', $e->getTraceAsString()) . "</td></tr>"
                . "</table>";
        
        return $return;
    }
}
