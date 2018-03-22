<?php

namespace FW\Widget\GridView;


/**
 * Класс для описания колонки в GridView
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Column
{
    /**
     * @var string  Идентификатор 
     */
    private $_name;
    
    
    /**
     * @var string  Идентификатор 
     */
    private $_label;
    
    
    /**
     * @var string  Перечень классов 
     */
    private $_classes; 
    
    
    /**
     * @var string  Инлайн стиль 
     */
    private $_style;
    
    
    /**
     * @var string  Перечень классов для заголовка
     */
    private $_header_classes; 
    
    
    /**
     * @var string  Инлайн стиль для заголовка
     */
    private $_header_style;  
    
    
    /**
     * @var string Текст в ячейке до значения из базы
     */
    private $_td_text_before = '';
    
    
    /**
     * @var string Текст в ячейке после значения из базы
     */    
    private $_td_text_after = '';
    
    
    /**    
     * @var string  Отображение пустого значения 
     */
    private $_empty_val = '-';
    
    
    /**
     * @var type Признак сортировки по данному полю
     */
    private $_sortable = false;
    
    
    /**
     * @var type Выражение, используемое для сортировки
     */
    private $_sort_expr = '';    
    
    
    /**
     *
     * @var array Массив для мэппинга значений
     */
    private $_value_mapping = [];
  
    
    /**
     *
     * @var type Форматирование значения
     */
    private $_format = '';
    
    
    /**
     *
     * @var array параметры форматирования
     */
    private $_format_params = [];
    
    
    /**
     * Конструктор класса - инициализация колонки
     * 
     * @param array $column
     */
    public function __construct($column) 
    {
        if (!is_array($column) || !isset($column['name'])) {
            throw new \Exception('Не задано имя колонки GridView');
        }
        
        //имя колонки - ключ
        $this->_name = $column['name'];
        
        //заголовок колонки
        if (isset($column['label'])) {
            $this->_label = $column['label'];
        }          
        
        //классы для td
        if (isset($column['classes'])) {
            $this->_classes = $column['classes'];
        }
        
        //инлайн стиль для td
        if (isset($column['style'])) {
            $this->_style = $column['style'];
        }      
        
        //классы для заголовка
        if (isset($column['header_classes'])) {
            $this->_header_classes = $column['header_classes'];
        }
        
        //инлайн стиль для заголовка
        if (isset($column['header_style'])) {
            $this->_header_style = $column['header_style'];
        }    
                
        //текст в ячейке до значения
        if (isset($column['text_before'])) {
            $this->_td_text_before = $column['text_before'];
        }    
        
        //текст в ячейке после значения
        if (isset($column['text_after'])) {
            $this->_td_text_after = $column['text_after'];
        }      
        
        //представление пустого значения
        if (isset($column['empty_val'])) {
            $this->_empty_val = $column['empty_val'];
        }     
        
        //наличие сортировки
        if (isset($column['sortable'])) {
            $this->_sortable = (boolean)$column['sortable'];
        }      
        
        //мэппинг значений
        if (isset($column['value_mapping'])) {
            $this->_value_mapping = $column['value_mapping'];
        }    
        
        //форматирование
        if (isset($column['format'])) {
            $this->_format = $column['format'];
        }    
        
        //параметры форматирования
        if (isset($column['format_params'])) {
            $this->_format_params = $column['format_params'];
        }           
        
        //выражение для сортировки
        if (isset($column['sort_expr'])) {
            $this->_sort_expr = $column['sort_expr'];
        }           
    }   
    
    
    /**
     * Возвращает заголовок колонки (или имя, в случае, если заголовка нет)
     * 
     * @return string
     */
    public function getLabel()
    {
        return (empty($this->_label))? $this->_name : $this->_label;
    }
    
    
    /**
     * Возвращает имя колонки
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    } 
    
    
    /**
     * Возвращает классы для тега th
     * 
     * @return string
     */
    public function getHeaderClasses()
    {
        return $this->_header_classes;
    }
    
    
    /**
     * Возвращает инлайн-класс для тега th
     * 
     * @return string
     */
    public function getHeaderStyle()
    {
        return $this->_header_style;
    }   
    
    
    /**
     * Возвращает Текст в ячейке до значения из базы
     * 
     * @return string
     */
    public function getTdTextBefore()
    {
        return $this->_td_text_before;
    }    
    
    
    /**
     * Возвращает Текст в ячейке после значения из базы
     * 
     * @return string
     */
    public function getTdTextAfter()
    {
        return $this->_td_text_after;
    }   


    /**
     * Возвращает представление пустого значения
     * 
     * @return string
     */
    public function getEmptyVal()
    {
        return $this->_empty_val;
    } 
    
    
    /**
     * Возвращает инлайн-класс для тега td
     * 
     * @return string
     */
    public function getStyle()
    {
        return $this->_style;
    }   
    
    
    /**
     * Возвращает набор классов для тега td
     * 
     * @return string
     */
    public function getClasses()
    {
        return $this->_classes;
    }   
    
    
    /**
     * Проверка наличия сортировки по полю
     * 
     * @return boolean
     */
    public function isSortable()
    {
        return $this->_sortable;
    }
    

    /**
     * Возвращает признак активности сортировки по текущему полю на основании
     * данных в GET запросе
     * 
     * @param array $query_params Текущий массив параметров строки запроса
     * 
     * @return boolean
     * 
     * @throws \Exception
     */
    public function getSortActive($query_params)
    {
        if (!$this->_sortable) {
            throw new \Exception("Попытка работы с сортировкой для поля без инициализации");
        }

        //имя поля для сортировки
        $sort_name = (empty($this->_sort_expr)) ? $this->_name : $this->_sort_expr;
        
        //активна ли сортировка по полю, определение её направления
        if (isset($query_params['sort_by']) 
                && $query_params['sort_by'] == $sort_name
                && isset($query_params['sort_dir'])) {
            
            return true;
        }
        
        return false;                
    }
    
    
    /**
     * Возвращает текущее направление сортировки по полю
     * 
     * @param array $query_params Текущий массив параметров строки запроса
     * 
     * @return string (ASC, DESC, NO)
     * 
     * @throws \Exception
     */
    public function getSortDir($query_params)
    {
        if (!$this->_sortable) {
            throw new \Exception("Попытка работы с сортировкой для поля без инициализации");
        }
        
        //имя поля для сортировки
        $sort_name = (empty($this->_sort_expr)) ? $this->_name : $this->_sort_expr;
        
        //активна ли сортировка по полю, определение её направления
        if (isset($query_params['sort_by']) 
                && $query_params['sort_by'] == $sort_name
                && isset($query_params['sort_dir'])) {
            
            return $query_params['sort_dir'];
        }

        return "NO";
    }  
    
    
    /**
     * Возвращает часть строки запроса после "?" для ссылки в элементе управления
     * сортировкой. Все прочие GET параметры будут сохранены. 
     * Также поддерживается указание фрагмента (кусок после #)
     * 
     * @param array $query_params Текущий массив параметров строки запроса
     * 
     * @return string
     * 
     * @throws \Exception
     */
    public function getSortQuery($query_params)
    {
        if (!$this->_sortable) {
            throw new \Exception("Попытка работы с сортировкой для поля без инициализации");
        }
        
        $current_dir = $this->getSortDir($query_params);
        
        $inverted_sort_dir = ['ASC' => 'DESC', 'DESC' => 'ASC', 'NO' => 'ASC'];
        unset($query_params['sort_by']);
        unset($query_params['sort_dir']);
        
        //имя поля для сортировки
        $sort_name = (empty($this->_sort_expr)) ? $this->_name : $this->_sort_expr;
        
        if (!empty($query_params)) {            
            $query = "?" . http_build_query($query_params) 
                    . "&sort_by=" . $sort_name . "&sort_dir=" . $inverted_sort_dir[$current_dir];
        } 
        else {
            $query = "?sort_by=" . $sort_name . "&sort_dir=" . $inverted_sort_dir[$current_dir];
        }
        
        return $query;
    }
    
    
    /**
     * Мэппинг значений колонки
     * 
     * @param mixed $value  Исходное значение (из базы)
     * 
     * @return string   На что оно заменяется
     */
    public function mapValue($value)
    {
        if (empty($this->_value_mapping) || !isset($this->_value_mapping[$value])) {
            return $value;
        }
        
        return $this->_value_mapping[$value];
    }
    
    
    /**
     * Возвращает способ форматирования значения
     * 
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }  
    
    
    /**
     * Возвращает параметры форматирования
     * 
     * @return array
     */
    public function getFormatParams()
    {
        return $this->_format_params;
    }      
    
    
    /**
     * Форматирование значения по пресету
     * 
     * @param mixed $value  Исходное значение
     * 
     * @return mixed    Возвращаемое значение
     */
    public function format($value)
    {
        switch ($this->getFormat())
        {
            case 'datetime' : return date('d.m.Y H:i:s', strtotime($value));
            
            case 'date' : return date('d.m.Y', strtotime($value));
                
            case 'image' :  $width = (isset($this->_format_params['width'])) 
                                    ? " width='{$this->_format_params['width']}' " : "";
                            $height = (isset($this->_format_params['height'])) 
                                    ? " height='{$this->_format_params['height']}' " : "";
                            $class = (isset($this->_format_params['class'])) 
                                    ? " class='{$this->_format_params['class']}' " : "";
                            $return = "<img src='$value' $width $height $class border='0'>";
                            return $return;
            
            case 'link' :   $href = (isset($this->_format_params['href'])) 
                                    ? $this->_format_params['href'] : $value;
                
                            $text = (isset($this->_format_params['text'])) 
                                    ? $this->_format_params['text'] : $value;
                
                            $target = (isset($this->_format_params['target'])) 
                                    ? " target='{$this->_format_params['target']}' " : "";
                                    
                            $title = (isset($this->_format_params['title'])) 
                                    ? " title='{$this->_format_params['title']}' " : "";                                    
                
                            $return = "<a href='$href' $target $title>$text</a>";
                            return $return;
            
            case 'imagelink' : 
                            $width = (isset($this->_format_params['width'])) 
                                    ? " width='{$this->_format_params['width']}' " : "";
                            $height = (isset($this->_format_params['height'])) 
                                    ? " height='{$this->_format_params['height']}' " : "";
                            $class = (isset($this->_format_params['class'])) 
                                    ? " class='{$this->_format_params['class']}' " : "";
                
                            $href = (isset($this->_format_params['href'])) 
                                    ? $this->_format_params['href'] : $value;
                
                            $target = (isset($this->_format_params['target'])) 
                                    ? " target='{$this->_format_params['target']}' " : "";
                                    
                            $title = (isset($this->_format_params['title'])) 
                                    ? " title='{$this->_format_params['title']}' " : "";                                      
                
                            $return = "<a href='$href' $target $title><img src='$value' $width $height $class border='0'></a>";
                            return $return;
            
            default: return $value;
        }
    }
}

