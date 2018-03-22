<?php

namespace FW;

/**
 * Хранилище объектов приложения
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Singleton
{
    /**
     * @var array   Массив объектов приложения
     */
    private static $_classes = [];
    
    
    /**
     * Запрос/инициализация объекта приложения
     * 
     * @param string    $name       Имя объекта
     * @param array     $params     Массив параметров для инициализации
     * 
     * @return mixed    Экземпляр класса (одиночка)
     */
    public static function get($name, $params = [])
    {
        if (strpos($name, '\\') === false) {
            $name = 'FW\\' . $name;
        }
        
        if (array_key_exists($name, self::$_classes)) {
            return self::$_classes[$name];
        }
        
        self::$_classes[$name] = new $name($params);
        
        return self::$_classes[$name];
    }
}

