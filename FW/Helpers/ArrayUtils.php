<?php

namespace FW\Helpers;

/**
 * Хэлпер для работы со сложными массивами
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
final class ArrayUtils
{
    /**
     * Очистка ключей у массива массивов
     * 
     * @param type $array
     * 
     * @return boolean
     */
    private static function _eraseKeys(&$array)
    {   
        foreach($array as $v)
        {
            if (!is_array($v)) {
                return false;
            }
        }
        $array = array_values($array);
    }
    
    
    
    /**
     * Очистка порядковых ключей у многомерного массива рекурсивно
     * 
     * @param type $array   Исходный массив
     * 
     * @return boolean
     */
    public static function eraseKeysRecursive(&$array) 
    {   
        self::_eraseKeys($array);
        foreach($array as &$v)
        {
            if (is_array($v)) {
               self::eraseKeysRecursive($v);
            }
        }
    }
    
    
    /**
     * Возвращает значение одного из полей матрицы по значению другого поля
     * 
     * @param array     $haystack   Исходный массив
     * @param string    $needle     Искомое значение
     * @param string    $key_name   Имя поля, по которому осуществляется поиск
     * @param string    $value_name Имя поля, которое должно быть на выходе
     * 
     * @return array|boolean
     */
    public static function getMatrixKeyValue($haystack, $needle, $key_name, $value_name)
    {
        if (!is_array($haystack) || empty((string)$needle) || 
                empty((string)$key_name) || empty((string)$value_name)) {
            return false;
        }
                                               
        $res = array_combine(array_column($haystack, $key_name), 
            array_column($haystack, $value_name));
        
        if (!array_key_exists($needle, $res)) {
            return false;
        }
        
        return $res[$needle];
    }
         
    
    /**
     * Возвращает массив, сформированный на базе исходной матрицы, 
     * в котором выбранное поле становится ключом
     * 
     * @param array     $array  Исходный массив (матрица)
     * @param string    $key    Имя поля, которое будет ключем
     * 
     * @return array|boolean
     */
    public static function getHashFromMatrix($array, $key)
    {
        if (!is_array($array) || empty((string)$key)) {
            return false;
        }
            
        $keys = array_column($array, $key);
        if (count($keys) != count($array)) {
            return false;
        }
        
        return array_combine(array_column($array, $key), $array);
    }
    
    
    /**
     * Возвращает массив, сформированный на базе исходной матрицы, 
     * в котором выбранное поле становится ключом. При этом, если
     * ключи дублируются, то они не перезатираются, а образуют перечисляемый массив
     * 
     * @param array     $array  Исходный массив (матрица)
     * @param string    $key    Имя поля, которое будет ключем
     * 
     * @return array|boolean
     */
    public static function getHashFromMatrixSafe($array, $key)
    {
        if (!is_array($array) || empty((string)$key)) {
            return false;
        }
            
        $result = [];
        
        foreach ($array as $row) 
        {
            if (!array_key_exists($row[$key], $result)) {
                $result[$row[$key]] = $row;
            }                    
            else {
                //если под этим ключем одиночный массив
                if (array_key_exists($key, $result[$row[$key]])) {
                    $temp = $result[$row[$key]];
                    unset($result[$row[$key]]);
                    $result[$row[$key]][] = $temp;
                    $result[$row[$key]][] = $row;
                }
                //иначе уже есть список
                else {
                    $result[$row[$key]][] = $row;
                }
                
                
            }
        }
                
        return $result;
    } 
    
    
    /**
     * Возвращает массив, сформированный на базе исходной матрицы, 
     * в котором выбранное поле становится ключом. При этом, если
     * ключи дублируются, то они не перезатираются, а образуют перечисляемый массив.
     * Если ключ встречается один раз, то массив все-равно будет перечисляемым,
     * только с одним индексом - 0.
     * 
     * @param array     $array  Исходный массив (матрица)
     * @param string    $key    Имя поля, которое будет ключем
     */
    public static function getHashListFromMatrix($array, $key)
    {
        if (!is_array($array) || empty((string)$key)) {
            return false;
        }
            
        $result = [];
        
        foreach ($array as $row) 
        {
            if (!array_key_exists($row[$key], $result)) {
                $result[$row[$key]][0] = $row;
            }                    
            else {
                $result[$row[$key]][] = $row;
            }
        }
                
        return $result;        
    }
    
    
    /**
     * Возвращает массив - простой ХЭШ в виде 
     * [key1 => value1, key2 => value2, ...] сформированный из матрицы
     * 
     * @param array     $array  Исходный массив (матрица)
     * @param string    $key    Имя поля, которое будет ключем
     * @param string    $value  Имя поля, которое будет значением
     * 
     * @return array|boolean
     */
    public static function getSimpleHashFromMatrix($array, $key, $value)
    {
        if (!is_array($array) || empty((string)$key) || empty((string)$value)) {
            return false;
        }
            
        $keys = array_column($array, $key);
        $values = array_column($array, $value);
        
        if (count($keys) != count($array) || count($values) != count($array)) {
            return false;
        }
        
        return array_combine($keys, $values);
    }
    
  
  
    /**
     * Совмещенная проверка array_key_exist и isset
     * т.е. проверка не пустой ли ключ
     * 
     * @param string    $key        Ключ для поиска в массиве
     * @param array     $array      Массив для поиска
     * 
     * @return boolean
     * 
     */
    public static function akeAndIsset($key, $array)
    {
        if (array_key_exists($key, $array) && isset($array[$key])) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Совмещенная проверка array_key_exist и !empty
     * т.е. проверка установлен ли ключ
     * 
     * @param string    $key        Ключ для поиска в массиве
     * @param array     $array      Массив для поиска
     * 
     * @return boolean
     * 
     */
    public static function akeAndNotEmpty($key, $array)
    {
        if (is_array($array) 
                && array_key_exists($key, $array) 
                && !empty($array[$key])) {
            return true;
        }
        
        return false;
    }   
    
    
    /**
     * Удаление столбцов в матрице
     * 
     * @param array     $keys_array    Ключи столбцов к удалению
     * @param array     $array         Массив
     * 
     * @return array|boolean
     */
    public static function deleteColumns($keys_array, $array)
    {
        if (!is_array($array)) {
            return false;
        }
        
        if (!array_walk($array, function(&$v) use ($keys_array){
            foreach ($keys_array as $key)
            {
                unset($v[$key]);
            }
        })) {
            return false;
        }
        
        return $array;
    }
    
    
    /**
     * Собирает матрицу из плоского списка или ХЭШа, используя указанное имя ключа
     * 
     * @param array  $list
     * @param string $col_name
     * 
     * @return array|boolean
     */
    public static function getMatrixFromList($list, $col_name)
    {
        if (!is_array($list)) {
            return false;
        }
        
        $res = [];
        
        foreach($list as $val)
        {
            $res[] = [$col_name => $val];
        }
        
        return $res;
    }
    
    
    /**
     * @see self::getMatrixFromList
     * 
     */
    public static function getMatrixFromHash($list, $col_name)
    {
        return self::getMatrixFromList($list, $col_name);
    }
    
    
    
    /**
     * Добавляет колонку к матрице
     * 
     * @param array     $matrix     Исходная матрица
     * @param string    $col_name   Имя колонки
     * @param array     $values     значения колонки (список)
     * 
     * @return array|boolean
     * 
     */
    public static function addColumn($matrix, $col_name, $values)
    {
        if (!is_array($matrix) || !is_array($values) 
                || !is_array($matrix[0]) 
                || count($matrix) != count($values)) {
            return false;
        }
        
        foreach ($matrix as $i => &$val)
        {
            $val[$col_name] = $values[$i];
        }
        
        return $matrix;
    }
    
    
    /**
     * Удаляет промежуточный уровень вложенности в однородном массиве.
     * Например, преобразует
     * 
     * ['key1' => ["value" => "val"]] 
     * 
     * в
     * 
     * ['key1' => "val"]
     * 
     * @param array $array  Исходный массив
     * @return array|boolean
     */
    public static function deleteOneLevel($array)
    {        
        if (!is_array($array) || !is_array(reset($array))) {
            return false;
        }
        
        foreach ($array as &$elem)
        {
            $elem = array_pop($elem);
        }
        
        return $array;
    }
    
    
    /**
     * Возвращает колонку строк, где каждая строка представляет собой склееные
     * значения столбцов исходной матрицы
     * 
     * @param array     $array  Исходный массив (матрица)
     * @param string    $glue   Набор символов, используемый для склеивания
     * 
     * @return array|boolean
     */
    public static function getColumnFromMatrix($array, $glue = '')
    {
        if (!is_array($array)) {
            return false;
        }
         
        foreach ($array as &$row)
        {
            $row = implode($glue, $row);
        }
        
        return $array;
    }    
  
}
