<?php

namespace FW;

use FW\Singleton as S;

/**
 * Базовый класс модели
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Model
{
    /**     
     * @var \FW\DB  Коннект к базе данных
     */
    protected $_db;
    
    
    /**
     * Конструктор модели - создание подключения к БД
     * 
     * @param array $params Параметры инициализации модели
     */
    public function __construct($params = [])
    {
        //создали коннект к БД
        $this->_db = S::get('DB', S::get('Config')->getParam('db'));      
    }
    
    
    /**
     * Преобразует массив в выражение для запроса вида "1,2,3,4"
     * 
     * @param array $ids
     * @param boolean $string
     * @param boolean $safe Вырезать из списка тектовых значений опасные конструкции
     * 
     * @return string
     */
    public function in($ids, $string = false, $safe = true)
    {
        if ($string) {
            
            //если выставлен флаг safe, выполняем замену опасных конструкций в
            //исходном массиве
            if ($safe) {
                array_walk_recursive($ids, function(&$val, $key){
                    $val = strip_tags($val);
                    $val = preg_replace(
                            "#(\(|\)|\={1,2}|\<|\>|\?|\+|\-{2}|\*|\/|\#)#", 
                            "", $val);
                });
            }
            
            return "'".implode("', '", $ids)."'";
        } else {
            //явно приводим перечень значений к инту
            array_walk_recursive($ids, function(&$val, $key){
                $val = (int)$val;
            });
            
            return implode(", ", $ids);
        }
    }    
    
    
    /**
     * Следующий запрос будет с пагинацией<br>
     * <b>ВНИМАНИЕ! Если метод дергается из контроллера, надо иметь ввиду, 
     * что pager окажет влияние только на первый запрос!<b>
     * 
     * @param int $page         Номер страницы
     * @param int $count        Кол-во записей на странице
     * @return \FW\Model        
     */
    public function pager($page, $count)
    {
        $this->_db->pager($page, $count);
        return $this;
    }
    
    
    /**
     * Следующий запрос будет с сортировкой
     * 
     * @param string $sort_by   Сортировка по полю
     * @param string $sort_dir  Направление сортировки
     * 
     * @return \FW\Model
     */
    public function sort($sort_by, $sort_dir)
    {
        if (!empty($sort_by)) {
            $this->_db->sort($sort_by, $sort_dir)->setOrder($sort_by, $sort_dir);
        }
        return $this;
    }     
    
    
    /**
     * Добавить в запрос фильтрацию по полю
     * 
     * @param string $field     Имя поля, по которому осуществляется поиск, 
     *                          может быть как с именем таблицы, так и без него 
     *                          (внимание! случае отсутствия имени таблицы возможны 
     *                          ошибки, если в запросе участвует несколько таблиц
     *                          с одинаковыми полями)
     * @param string $sample    Значение образца (если значение пустое - фильтр не будет добавлен)
     * @param string $mode      Способ фильтрации (возможные варианты: =, !=, <, >, <=, >=, %, isnull)
     * 
     * @return \FW\Model
     */
    public function addFilter($field, $sample, $mode = '=')
    {
        if (!empty($field) && !empty($mode)) {
            $this->_db->addFilter($field, $sample, $mode);
        }
        
        return $this;
    }
    
    
    /**
     * Включение режима подавления исключений
     * Вместо выбрасывания исключений методы возвращают <strong>FALSE</strong>
     * 
     * @return \FW\Model
     */
    public function silentMode()
    {
        $this->_db->silentMode();
        return $this;
    }
    
    
    
}

