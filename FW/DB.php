<?php

namespace FW;
use PHPSQLParser\PHPSQLParser;
use PHPSQLParser\PHPSQLCreator;

/**
 * Класс для работы с БД через PDO
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class DB
{

    const FETCH_DEFAULT = \PDO::FETCH_ASSOC;

    const FETCH_ASSOC = \PDO::FETCH_ASSOC;

    const FETCH_NUM = \PDO::FETCH_NUM;


    /**
     * @access private      PDO object
     * @var \PDO 
     */
    protected $_pdo = null;
     
    
    /**
     * Порядок сортировки
     * 
     * @access private
     * @var string
     */
    private $_order = null;
    
    
    /**
     * Параметры фильтрации в запросе
     * 
     * @access private
     * 
     * @var string 
     */    
    private $_filter = [];

    
    /**
     * @var type способ объединения в фильтре условий для одинаковых имен столбцов
     */
    private $_filter_logic_mode = 'OR';
    

    /**
     * Ограничения
     * 
     * @var type 
     */
    private $_limit = null;
    

    /**
     * @var type Метод извлечения
     */
    protected $_fetch_mode;


    /**
     * @var boolean 
     */
    protected $_restore_fetch_default;
  
        
    /**
     * Использовать пагинацию
     * 
     * @var boolean
     */
    private $_pager = false;
    
    
    /**
     * Текущая страница при выборке с пагинацией
     * 
     * @var int
     */
    private $_pager_cur_page;
    
    
    /**
     * Кол-во записей на странице
     * 
     * @var int
     */
    private $_pager_count;
    
    
    /**
     * Режим подавления исключений 
     * Исключение не выбрасывается - вместо этого методы вернут false
     * 
     * @var boolean
     */
    private $_silent_mode;
    
    
    /**
     *
     * @var array Сортировка для произвольного запроса. Массив элементов вида
     *            ['field_name' => 'sort_dir'] 
     */
    private $_sort;

    
    /**
     * Конструктор
     * 
     * @param array $db_config
     */
    public function __construct(array $db_config) 
    {
        $this->setFetchMode(self::FETCH_DEFAULT);
        $this->_restore_fetch_default = true;

        $this->_connect($db_config);
    }
    
    
    /**
     * Деструктор класса
     * 
     */
    public function __destruct() {
        $this->_disconnect();
    } 
    
    
    /**
     * Клонирование объекта
     * 
     */
    public function __clone()
    {
        $this->_fetch_mode = self::FETCH_DEFAULT;
        $this->_limit = null;
        $this->_order = null;
    }
    
    
    
    /**
     * Подключение к серверу БД, создание объекта PDO
     * 
     * @param array $db_config
     * @return \FW\DB
     * @throws \Exception
     */
    private function _connect($db_config)
    {
        $port = '';
        if (isset($db_config['db_port'])) {
            $port = ";port=".$db_config['db_port'];
        }  

        try
        {
            $this->_pdo = new \PDO(
                "mysql:host=".$db_config['db_host'].$port.";dbname=".$db_config['db_name'].";charset=utf8", 
                $db_config['db_login'], 
                $db_config['db_password']//,[\PDO::ATTR_PERSISTENT => true]
            );
            
            $this->_pdo->exec("SET NAMES utf8");
        }
        catch (\PDOException $e)
        {
            throw new \Exception($e);
        }

        //$this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);  
        $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); 
        //$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT); //режим не выводящий ошибок
        //$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);

        return $this;
    }
    
    
    /**
     * Отключение от сервера
     * 
     * @return boolean
     */
    private function _disconnect()
    {
        $this->_pdo = null;
        return true;
    }
        
    
    /**
     * Запрос на получение общего кол-ва строк для пагинации
     * 
     * @param string $query             Исходный запрос
     * @param array $place_holders      Плейсхолдеры
     * @return int|null                 Кол-во строк
     */
    private function _pagerCountQuery($query, &$place_holders)
    {
        $query_build  = "SELECT COUNT(*) FROM (";
        $query_build .= $query;
        $query_build .= ") __count";        

        $sth = $this->_pdo->prepare($query_build);

        $res = $this->_execute($sth, $place_holders);
        
        return ($res->rowCount() > 0) ? $res->fetch(\PDO::FETCH_NUM)[0] : null;            
    }
    
    
    /**
     * Изменяет запрос (добавление LIMIT) для пагинации
     * 
     * @param string $query
     * @return string
     */
    private function _pagerLimitQuery($query)
    {
        $from = (int)($this->_pager_cur_page * $this->_pager_count - $this->_pager_count);
        $count = (int)($this->_pager_count);
        return $query . " LIMIT $from, $count";
    }    
    

    /**
     * Лимит для пагинатора (через парсер)
     * 
     * @deprecated
     * @return type
     */
    private function _pagerLimitQuery__()
    {
        $limit = [
            'offset'   => (int)($this->_pager_cur_page * $this->_pager_count - $this->_pager_count),
            'rowcount' => (int)($this->_pager_count)
        ];        
        
        //распарсиваем SQL запрос
        $parsed_query = (new PHPSQLParser($query))->parsed;
        
        $injected_limit = $parsed_query;
        $injected_limit['LIMIT'] = $limit;
        
        //собираем запрос
        return (new PHPSQLCreator($injected_limit))->created;           
    }

    
    /**
     * Изменяет запрос (добавляет ORDER для сортировки)
     * 
     * @param string $query
     * 
     * @return string
     */
    private function _addSortQuery($query)
    {        
        //заменяем множественные пробелы на один в исходном запросе
        $query = preg_replace('!\s+!', ' ', $query);
        
        //распарсиваем SQL запрос
        $parsed_query = (new PHPSQLParser($query, true))->parsed;
        
        //формируем фрагмент запроса (сортировка)
        $sort_clause = ' ORDER BY ' . key($this->_sort) . ' ' 
                . current($this->_sort) . ' ';
        
        //проверить наличие LIMIT, если нет - пишем в конец запроса
        if (!isset($parsed_query['LIMIT'])) {
            //проверить наличие предыдущей сортировки, если нет - просто пишем в конец
            if (!isset($parsed_query['ORDER'])) {
                $query = rtrim($query, ';') . $sort_clause;
            }
            //если есть - затираем в конце
            else {
                //позиция начала ORDER
                $order_pos = $parsed_query['ORDER'][0]['position'] - 10;
                
                $query = mb_substr($query, 0, $order_pos) 
                        . $sort_clause;
            }
        }
        //если есть, вычисляем его позицию и вставляет сортировку ДО него
        else {
            //получим позицию ЛИМИТА (Парсер почему-то не возвращает позицию для LIMIT)
            $limit_pos = strpos($query, 'LIMIT ');
            
            //проверить наличие предыдущей сортировки, если нет - просто вклиниваем до LIMIT
            if (!isset($parsed_query['ORDER'])) {   
                $query = substr_replace($query, $sort_clause, $limit_pos, 0);
            }
            //если есть - затираем и вписываем ДО LIMIT
            else {
                //позиция начала ORDER
                $order_pos = $parsed_query['ORDER'][0]['position'] - 10;
                
                $query = substr_replace(
                        $query, 
                        $sort_clause, 
                        $order_pos, 
                        $limit_pos - $order_pos);
            }            
        }
        return $query;
    }
    
    
    /**
     * Сортировка массива с фильтрами (по имени поля)
     * 
     * @param array $array  Исходный массив
     * 
     * @return void
     */
    private function _sortFilterArray(&$array)
    {
        usort($array, function($a, $b){
            
            //если элементы не массивы - не сравниваем
            if (!is_array($a) || !is_array($b)) {
                return 0;
            } 

            //одинаковые ключи
            if ($a['field'] == $b['field']) {
                return 0;
            }

            return ($a['field'] > $b['field']) ? 1 : -1;
        });      
    }
    
    
    /**
     * Возвращает SQL оператор для запроса по режиму фильтрации
     * 
     * @param string $mode  Режим фильтрации
     * 
     * @return string
     */
    private function _getFilterOperator($mode = '=') 
    {
        switch ($mode) 
        {
            case  '=': 
            case '!=':  
            case  '<':
            case  '>':
            case '<=':
            case '>=':
                $result = $mode;
                break;

            case '%': 
                $result = ' LIKE ';
                break;
            
            case 'isnull':
                $result = ' IS NULL ';
                break;
            
            default:
                $result = '=';
                break;
        }
        
        return $result;
    }
    
    
    /**
     * Возвращает SQL операнд для запроса по режиму фильтрации
     * 
     * @param string $sample    Образец фильтрации
     * @param string $mode
     * 
     * @return string
     */
    private function _getFilterOperand($sample, $mode = '=')
    {
        switch ($mode) 
        {
            case '%':
                $result = "'%" . $sample . "%'";
                break;
            
            case 'isnull':
                $result = "";
                break;
            
            default:
                $result = "'$sample'";
                break;
        }
        
        return $result;
    }
    
    
    /**
     * Определение места вставки условий запроса
     * 
     * @param string $query   Текст запроса
     * 
     * @return integer
     */
    private function _getFilterInsertPosition($query)
    {
        $parsed_query = (new PHPSQLParser($query, true))->parsed;

        //если не массив возвращаем ноль
        if (!is_array($parsed_query)) {
            return 0;
        }
        
        //если есть блок GROUP и пофиг что там после него
        if (array_key_exists('GROUP', $parsed_query)) {
            return $parsed_query['GROUP'][0]['position'] - mb_strlen("GROUP BY ");
        }
        
        //если нет блока GROUP но есть блок ORDER и пофиг что там после него
        if (array_key_exists('ORDER', $parsed_query)) {
            return $parsed_query['ORDER'][0]['position'] - mb_strlen("ORDER BY ");
        }
        
        //если нет блока GROUP и ORDER но есть блок LIMIT
        if (array_key_exists('LIMIT', $parsed_query)) {            
            return mb_strpos(mb_strtoupper($query), 'LIMIT');
        }

        //если нет ничего из вышеперечисленного, то возвращаем просто размер строки
        return mb_strlen($query);
    }
        
    
    /**
     * Изменяет запрос (добавляет условия для фильтрации)
     * 
     * @param string $query
     * 
     * @return string
     */
    private function _addFilterQuery($query)
    {       
        //заменяем множественные пробелы на один
        $query = preg_replace('!\s+!', ' ', $query);
        $parsed_query = (new PHPSQLParser($query, true))->parsed;

        //если есть блок WHERE на верхнем уровне (подзапросы не учитываются)
        if (array_key_exists('WHERE', $parsed_query)) {
            $add_query = ' AND ( ';
        }
        //если его нет, дописываем сами
        else {
            $add_query = ' WHERE ( ';
        }
        unset($parsed_query);

        //ни одна внутренняя скобка еще не открыта
        $bracket_open = false;
        
        //сортируем фильтры так, чтобы элементы с одинаковыми именами были собраны рядом
        $this->_sortFilterArray($this->_filter);

        //перебираем массив с фильтрами
        foreach ($this->_filter as $i => $filter)
        {
            //если это не последний элемент и следующий элемент имеет то же имя поля
            if (isset($this->_filter[$i + 1]) && $filter['field'] == $this->_filter[$i + 1]['field']) {
                //если скобка еще не открыта, открываем
                if (!$bracket_open) {
                    $add_query .= " ( ";
                    $bracket_open = true;
                }
                
                //дополняем запрос очередным блоком
                $add_query .= $filter['field'] 
                           . $this->_getFilterOperator($filter['mode']) 
                           . $this->_getFilterOperand($filter['sample'], $filter['mode']);
                
                //ставим оператор ИЛИ / И, в зависимости от текущего режима
                $add_query .= " {$this->_filter_logic_mode} ";
            }
            
            //если дальше идет отличающееся поле и скобка была открыта ранее
            elseif ($bracket_open) {                
                //дополняем запрос очередным блоком
                $add_query .= $filter['field'] 
                           . $this->_getFilterOperator($filter['mode']) 
                           . $this->_getFilterOperand($filter['sample'], $filter['mode']);                
                
                //закрываем скобку
                $add_query .= " ) AND ";
                $bracket_open = false;
            }
            //если это последний элемент или одиночное поле и за ним идет другое
            else {
                $add_query .= $filter['field'] 
                           . $this->_getFilterOperator($filter['mode']) 
                           . $this->_getFilterOperand($filter['sample'], $filter['mode'])
                           . " AND ";
            }
        }
        
        //убираем крайний AND и прикрываем финальной скобкой
        $add_query = mb_substr($add_query, 0, mb_strlen($add_query) - 5) . ') ';
        
        //вставляем сгенерированный блок с фильтрами в нужное место запроса
        $insert_pos = $this->_getFilterInsertPosition($query);
        $before = mb_substr($query, 0, $insert_pos);
        $after = mb_substr($query, $insert_pos);
        $query = $before . $add_query . $after;

        return $query;
    }
    
    
    /**
     * Выполнение подготовленного выражения (для всех запросов)
     * 
     * @param PDOStatement  $sth                PDO Prepare Statement
     * @param array         $place_holders      Плейсхолдеры
     * @param boolean       $ignore_dubles      Игнорировать дубли
     * @return boolean|PDOStatement
     */
    protected function _execute($sth, $place_holders = [], $ignore_dubles = false)
    {
        try 
        {
            $sth->execute($place_holders);
            return $sth;
        } 
        catch (\PDOException $e)
        {
            //если игнорируем дубли
            if ($ignore_dubles && $e->getCode() == '23000') {
                return true;
            }
            
            //если игнорируем все ошибки
            //сбрасывает "тихий" режим после запроса
            if ($this->_silent_mode) {
                $this->_silent_mode = false;
                return false;
            }
                        
            //во всех остальных случаях
            else {
                throw new \Exception($e);                
            }
        }
    }    
    
    
    /**
     * Включение режима подавления выбрасывания исключения
     * При включенном режиме - методы вернут <strong>FALSE</strong> вместо
     * выбрасывания исключения
     * 
     * @return \FW\DB
     */
    public function silentMode()
    {
        $this->_silent_mode = true;
        return $this;
    }
    
    
    /**
     * Выполнение запроса, которое при ошибке вернет
     * false, без выбрасывания исключения.
     * (Для транзакционных запросов)
     * 
     * @param string        $query              PDO Prepare Statement
     * @param array         $place_holders      Плейсхолдеры
     * 
     * @return boolean;
     */
    public function execNoException($query, $place_holders = [])
    {
        try 
        {
            $sth = $this->_pdo->prepare($query);
            return $sth->execute($place_holders);
        } 
        catch (\PDOException $e)
        {
            return false;
        }
    }


    
    /**
     * Возвращает реальное имя поля с префиксом по псевдониму
     * т.е. вернет se.name для name2 в запросе se.name as name2
     * 
     * @param string $name      Короткое имя поля
     * @param string $query     Текст запроса полный
     * 
     * @return string
     */
    protected function _getFieldNameByTitle($name, $query)
    {
        $query = strtolower(preg_replace('/\s+/', ' ',$query));
        $chunks = explode(" ", $query);
        $chunks = array_slice($chunks, 0, array_search("from", $chunks));
        array_walk($chunks, function(&$v){$v = trim($v, ',');});
        $key = array_search($name, $chunks);

        if ($key === false || $key < 2) {
            return $name;
        }
        else {
            return $chunks[$key - 2];
        }
    }
    
    
    /**
     * Установка режима выдачи
     * 
     * @param int $mode
     * 
     * @return \FW\DB
     */
    public function setFetchMode($mode)
    {
        $this->_fetch_mode = $mode;
        return $this;
    }

    
    /**
     * Установка режима сортировки
     * 
     * @todo НЕ БЕЗОПАСНО! (sql inj)
     * 
     * @param string $field
     * @param string $direction
     * 
     * @return \FW\DB
     */
    public function setOrder($field, $direction = "ASC")
    {
        $this->_order = "ORDER BY $field $direction";
        return $this;
    }


    /**
     * Установка лимита выдачи
     * 
     * @param string $limit
     * @param string $count
     * 
     * @return \FW\DB
     */
    public function setLimit($limit, $count = null)
    {
        if (!empty($limit)) {
            $this->_limit = "LIMIT " . (int)$limit;
        }
        
        if (!is_null($count)) {
            $this->_limit = "LIMIT " . (int)$limit . "," . (int)$count;
        }
        
        return $this;
    }
    
  
    /**
     * SELECT
     * 
     * @param string $table_name
     * @param string $fields
     * @param string $conditions
     * @param array $place_holders
     * 
     * @return array
     */
    public function select($table_name, $fields, $conditions = '', $place_holders = [], $distinct = false)
    {
        $distinct_clause = ($distinct) ? " DISTINCT " : "";
        $order = (empty($this->_order)) ? '' : $this->_order;
        $limit = (empty($this->_limit)) ? '' : $this->_limit;
                
        if (!empty($conditions)) {
            $conditions = "WHERE " . $conditions;
        }

        $sql = "SELECT $distinct_clause" . $fields . " FROM " . $table_name . " " . $conditions . " " . $order . " " . $limit;
                   
        if ($this->_pager) 
        {
            $total_records = $this->_pagerCountQuery($sql, $place_holders);
            if ((int)$total_records == 0) 
            {
                $this->_purgeSettings();                                           
                return ['data' => [], 'total' => '0'];
            }
            
            //добавляем в запрос LIMIT
            $sql = $this->_pagerLimitQuery($sql);
        }   
        
        //фильтрация, если задана
        if (!empty($this->_filter)) {
            $sql = $this->_addFilterQuery($sql);
        }        
        
        $sth = $this->_pdo->prepare($sql);
        $sth->setFetchMode($this->_fetch_mode);        
        
        if ($this->_pager) 
        {
            $this->_purgeSettings(); 
            return $result = [
                'data'  =>  $this->_execute($sth, $place_holders)->fetchAll(),
                'total' =>  $total_records
            ];            
        }         
        
        $this->_purgeSettings(); 
        return $this->_execute($sth, $place_holders)->fetchAll(); 
    }


    /**
     * Выбор одной строки
     * 
     * @param string $table_name
     * @param string $fields
     * @param string $conditions
     * @param array $place_holders
     * 
     * @return array
     */
    public function selectOneRow($table_name, $fields, $conditions = '', $place_holders = null)
    {
        $order = (empty($this->_order)) ? '' : $this->_order;

        if (!empty($conditions)) {
            $conditions = "WHERE " . $conditions;
        }

        $sql = "SELECT " . $fields . " FROM " . $table_name . " " . $conditions . " " . $order . " LIMIT 1";        
        
        //фильтрация, если задана
        if (!empty($this->_filter)) {
            $sql = $this->_addFilterQuery($sql);
        }        
        
        $sth = $this->_pdo->prepare($sql);

        $this->_purgeSettings();        
        return $this->_execute($sth, $place_holders)->fetch(\PDO::FETCH_ASSOC);   
    }  
    
    
    /**
     *  Выбор одного значения
     *
     *  @param string $table_name
     *  @param string $field
     *  @param string $conditions
     * 
     *  @return mixed
     */
    public function selectValue($table_name, $field, $conditions = null, $place_holders = null)
    {
        $order = (empty($this->_order)) ? '' : $this->_order;
        
        if (!empty($conditions)) {
            $conditions = "WHERE " . $conditions;
        }

        $sql = "SELECT $field FROM $table_name $conditions $order LIMIT 1";

        //фильтрация, если задана
        if (!empty($this->_filter)) {
            $sql = $this->_addFilterQuery($sql);
        }           
        
        $sth = $this->_pdo->prepare($sql);

        //восстанавливаем fetch mode по умолчанию
        if ($this->_restore_fetch_default) {
            $this->setFetchMode(self::FETCH_DEFAULT);
        }        
        
        //убираем фильтрацию для следующего запроса
        if (!empty($this->_filter)) {
            $this->_filter = [];
        }        
        


        $res = $this->_execute($sth, $place_holders);

        return ($res->rowCount() > 0) ? $res->fetch(\PDO::FETCH_NUM)[0] : null;
    }           
    
    
    /**
     * INSERT
     * 
     * @param string    $table_name - Имя таблицы
     * @param array     $insert_array - Ассоциативный массив для вставки
     * @param boolean   $insert_ignore - игнорировать дубли
     * 
     * @return int      Возвращает ID добавленного элемента
     */
    public function insert($table_name, $insert_array, $insert_ignore = false)
    {
        $fields = "`" . implode("`, `", array_keys($insert_array)) . "`";
        $bind_fields = ":" . implode(", :", array_keys($insert_array));

        $IGNORE = $insert_ignore ? ' IGNORE ' : '';
        
        $sql = "INSERT " . $IGNORE . " INTO " . $table_name ." (" . $fields . ") VALUES (" . $bind_fields . ")";
        $sth = $this->_pdo->prepare($sql);
        
        $res = $this->_execute($sth, $insert_array);
        return $res === false ? false : $this->_pdo->lastInsertId();
    }
    
    
    /**
     * INSERT - вставка массива значений
     * 
     * @param string    $table_name - Имя таблицы
     * @param array     $insert_array - матрица из ассоциативных массивов для вставки
     * 
     * @return boolean - Возвращает true при успешной вставке,
     *         false - если во время вставки произошла ошибка 
     */
    public function insertArray($table_name, $insert_array)
    {
        if (empty($insert_array)) {
            return true;
        }
        
        $fields = "`" . implode("`, `", array_keys($insert_array[0])) . "`";
        $bind_fields = ":" . implode(", :", array_keys($insert_array[0]));

        $sql = "INSERT INTO " . $table_name ." (" . $fields . ") VALUES (" . $bind_fields . ")";
        $sth = $this->_pdo->prepare($sql);
        
        $this->transactionStart();
        foreach ($insert_array as $block)
        {
            if (!$this->_execute($sth, $block)) {
                $this->transactionRollback();
                return false;
            }            
        }
        $this->transactionCommit();
        return true;
    }    
    
    
    /**
     * INSERT with dubles ignore
     * 
     * @param string    $table_name - Имя таблицы
     * @param array     $insert_array - Ассоциативный массив для вставки
     * @deprecated since version 0.2)))
     * @return boolean
     */
    public function insertIgnoreDubles($table_name, $insert_array)
    {
        $fields = "`" . implode("`, `", array_keys($insert_array)) . "`";
        $bind_fields = ":" . implode(", :", array_keys($insert_array));

        $sql = "INSERT INTO " . $table_name ." (" . $fields . ") VALUES (" . $bind_fields . ")";
        $sth = $this->_pdo->prepare($sql);
        
        return (boolean)$this->_execute($sth, $insert_array, true);                    
    }    
    

    /**
     * UPDATE
     * 
     * @param string $table_name
     * @param array $update_array
     * @param string $conditions
     * 
     * @return int          Количество обновленных строк  
     */
    public function update($table_name, $update_array, $conditions)
    {   
        $order = (empty($this->_order)) ? '' : $this->_order;
        $limit = (empty($this->_limit)) ? '' : $this->_limit;
            
        if (!empty($conditions)) {
            $conditions = "WHERE " . $conditions;
        }
            
        foreach ($update_array as $key => $val) 
        {
            $valstr[] = $key . "=:" . $key;
        }
        
        $valstr = implode(', ', $valstr);

        $sql = "UPDATE " . $table_name . " SET " . $valstr . " " . $conditions . " " . $order . " " . $limit;

        unset($this->_order);
        unset($this->_limit);

        $sth = $this->_pdo->prepare($sql);
        
        $res = $this->_execute($sth, $update_array);
        
        return (int) $res->rowCount();
    }


    /**
     * DELETE
     * 
     * @param string $table_name
     * @param string $conditions
     * @param array $place_holders
     * 
     * @return int
     */
    public function delete($table_name, $conditions, $place_holders = null)
    {
        $sql = "DELETE FROM " . $table_name . " WHERE " . $conditions;

        $sth = $this->_pdo->prepare($sql);
        return $this->_execute($sth, $place_holders)->rowCount();
    }


    /**
     * Произвольный запрос НА ВЫБОРКУ
     * 
     * @param string $query                 Текст запроса
     * @param array $place_holders          Плейсхолдеры
     * 
     * @return array
     */
    public function query($query, $place_holders = [])
    {
        //пагинация
        if ($this->_pager) 
        {
            $query_total = $query;
            
            //фильтрация, если задана
            if (!empty($this->_filter)) {
                $query_total = $this->_addFilterQuery($query);
            }            

            $total_records = $this->_pagerCountQuery($query_total, $place_holders);
            if ((int)$total_records == 0) 
            {                
                $this->_purgeSettings();
                return ['data' => [], 'total' => '0'];
            }
            
            //добавляем в запрос LIMIT
            $query = $this->_pagerLimitQuery($query);
        }
        
        //сортировка, если включена
        if (!empty($this->_sort) && is_array($this->_sort)) {
            $query = $this->_addSortQuery($query);        
        }
        
        //фильтрация, если задана
        if (!empty($this->_filter)) {
            $query = $this->_addFilterQuery($query);
        }
        
        $sth = $this->_pdo->prepare($query);
        $sth->setFetchMode($this->_fetch_mode);

        if ($this->_pager) 
        {     
            $this->_purgeSettings();
            return $result = [
                'data'  =>  $this->_execute($sth, $place_holders)->fetchAll(),
                'total' =>  $total_records
            ];            
        } 
        
        $this->_purgeSettings();
        return $this->_execute($sth, $place_holders)->fetchAll();
    }
    
    
    /**
     * Сброс кастомных настроек выборки
     * 
     * @return void
     */
    private function _purgeSettings()
    {
        //убираем пагинацию для следующего запроса  
        $this->_pager = false;
        
        //убираем фильтрацию для следующего запроса
        if (!empty($this->_filter)) {            
            $this->_filter = [];
        }    
        
        //отключаем сортировку
        if (!empty($this->_sort) && is_array($this->_sort)) {
            $this->_sort = null;
        }    
        
        //восстанавливаем fetch mode по умолчанию
        if ($this->_restore_fetch_default) {
            $this->setFetchMode(self::FETCH_DEFAULT);
        }   

        //Убираем прочие настройки
        unset($this->_order);
        unset($this->_limit);  
        $this->_filter_logic_mode = 'OR';
    }

    
    /**
     * Произвольный запрос, возвращающий только 1 запись (0)
     * 
     * @param string $query
     * @param array $ph
     * @return array
     */
    public function queryOneRow($query, $ph = [])
    {
        $res = $this->query($query, $ph);
        return array_key_exists(0, $res) ? $res[0] : [];
    }
    
    
    /**
     * Включение пагинации
     * 
     * @param int $page     Текущая страница 
     * @param type $count   Кол-во записей на странице
     * @return \FW\DB
     */
    public function pager($page, $count)
    {
        $this->_pager_cur_page = ((int)$page == 0) ? 1 : (int)$page;
        $this->_pager_count = $count;
        $this->_pager = true;
        return $this;
    }
    
    
    /**
     * Включение сортировки для произвольных запросов
     * 
     * @param string $sort_by   Сортировка по полю
     * @param string $sort_dir  Направление сортировки
     * 
     * @return \FW\DB
     */
    public function sort($sort_by, $sort_dir = 'ASC')
    {
        $this->_sort = [$sort_by => $sort_dir];
        
        return $this;
    }
    
    
    /**
     * Добавление фильтра для вставки в запрос
     * 
     * @todo НЕ БЕЗОПАСНО! (sql inj)
     * 
     * @param string $field     Имя поля, по которому осуществляется поиск
     * @param string $sample    Значение образца
     * @param string $mode      Способ фильтрации
     * 
     * @return \FW\DB
     */
    public function addFilter($field, $sample, $mode = '=')
    {
        if (!empty($field) && !empty($mode)) {
            $this->_filter[] = [
                'field'     => $field,
                'sample'    => $sample,
                'mode'      => $mode,
            ];
        }
        
        return $this;
    }
    
    
    /**
     * Произвольный запрос НА МОДИФИКАЦИЮ
     * Если не просто Update, то будет возвращать 0 при успешной операции!
     * 
     * @param string $query
     * @param array $place_holders
     * 
     * @return int
     */
    public function exec($query, $place_holders = null)
    {
        $sth = $this->_pdo->prepare($query);
        $res = $this->_execute($sth, $place_holders);
        return !is_bool($res) ? $res->rowCount() : $res;
    }


    /**
     * Очистка таблицы
     * 
     * @param string $table_name
     * 
     * @return boolean
     */
    public function truncate($table_name)
    {
        $sth = $this->_pdo->prepare("TRUNCATE TABLE " . $table_name);
        $this->_execute($sth);
        return true;
    }
    
    
    /**
     * Распечатка существующих драйверов
     */
    public function printDrvs()
    {
        print_r(\PDO::getAvailableDrivers());
    }
    
 
    /**
     * Начало транзакции
     * 
     * @return boolean
     */
    public function transactionStart()
    {
        return $this->_pdo->beginTransaction();
    }
  
    
    /**
     * Фиксация транзакции
     * 
     * @return boolean
     */
    public function transactionCommit()
    {
        return $this->_pdo->commit();
    }    
  

    /**
     * Откат транзакции
     * 
     * @return boolean
     */
    public function transactionRollback()
    {
        return $this->_pdo->rollBack();
    }        
    
    
    /**
     * Проверка на существование заданного значения в столбце
     * 
     * @param string $table_name        
     * @param string $conditions            
     * @param mixed $place_holders          
     * @return boolean
     */
    public function selectExists($table_name, $conditions, $place_holders = [])
    {
        $res = $this->query("
            SELECT EXISTS(SELECT 1 FROM {$table_name} WHERE $conditions) AS _exists
        ", $place_holders);
            
        return $res[0]['_exists'] == '1' ? true : false;
    }
    
    
    /**
     * Установка режима логического объединения блоков фильтра
     * 
     * @param string $mode OR или AND
     * 
     * @return $this
     */
    public function setFilterLogicMode($mode = 'OR')
    {
        $this->_filter_logic_mode = (in_array($mode, ['OR', 'AND'])) ? $mode : 'OR';
        return $this;
    }
    
}


