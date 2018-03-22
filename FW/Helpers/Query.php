<?php

namespace FW\Helpers;

/**
 * Работа с запросом
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
final class Query
{  

    /**
     * Удаление параметра из строки запроса
     * 
     * @param string $query
     * @param string $param_name
     * @return type
     */
    public static function removeParamFromRequest($query, $param_name)
    {
        parse_str($query, $data);
        if (isset($data[$param_name])) {
            unset($data[$param_name]);
        }
        return http_build_query($data);
    }

}