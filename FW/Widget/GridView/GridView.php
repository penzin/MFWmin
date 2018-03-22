<?php

namespace FW\Widget\GridView;

use FW\Widget\Widget;
use FW\Widget\Form\Select;
use FW\Singleton as S;

/**
 * Класс работы с активной таблицей данных
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class GridView extends Widget
{
    /**
     * Файл шаблона кнопки "Удалить"
     */
    const ACTION_ROW_DELETE_TEMPLATE = 'delete';
    
    
    /**
     * Файл шаблона кнопки "Редактировать"
     */
    const ACTION_ROW_EDIT_TEMPLATE = 'edit';
    
    
    /**
     * файл шаблона кнопки "кастомное действие"
     */
    const ACTION_ROW_CUSTOM_TEMPLATE = 'custom';
    
    
    /**
     * файл с набором дефолтных скриптов
     */
    const ACTION_TEMPLATE_SCRIPT = 'actions';

    
    /**
     * @var string Текущий URI
     */
    private $_uri;
    
    
    /**
     * @var string Текущая строка Query параметров
     */
    private $_query_string;
    
    
    /**
     * @var string  Перечень CSS классов виджета
     */
    private $_classes; 
    
    
    /**
     * @var string  Инлайн стиль виджета
     */
    private $_style;
    
    
    /**
     * @var array данные для формирования строк
     */
    private $_data;
            
    
    /**
     *
     * @var array Колонки Таблицы
     */
    private $_columns = [];
    
    
    /**
     * @var boolean Флаг использования заголовка
     */
    private $_use_header = true;
    
    
    /**
     * @var string Ключевое поле (будет отображаться в теге tr в виде параметра data-id)
     */
    private $_key_filed_name = '';
    
    
    /**
     * @var type массив действий для каждой строки (напр., редактировать, удалить элемент)
     */
    private $_row_actions = [];
    
    
    /**
     * @var type Массив действий для нескольких выбранных строк (в подвале таблицы)
     */
    private $_group_actions = [];
    
    
    /**
     *
     * @var boolean Флаг использования скриптов по умолчанию для экшнов виджета
     *              (прописанных в темплейтах), а также групповых действий
     */
    private $_use_default_scripts = true;
    
    
    /**
     * @var type Двумерный массив, содержащий особые стили для конкретных ячеек
     */
    private $_extra_classes = [];
    
    
    /**     
     * @var type Флаг использования серийных номеров строк
     */
    private $_use_serial_numbers = false;
          
    
    /**
     * @var string Кастомный URI адрес (для случая, если таблицу вставляют во
     *             вкладку и т.п.)
     */
    private $_custom_uri = '';
    
    
    /**
     * @var string  Фрагмент (символы после #, если есть)
     */
    private $_custom_uri_fragment = '';
    
    
    /**
     * @var string Кастомный адрес дл яобработчиков внутристрочных и гурпповых 
     *             действий
     */
    private $_custom_actions_uri = '';
    
    
    /**
     *
     * @var array Правило, по которому строка маркируется как readonly 
     */
    private $_readonly_rule = [];
    
    
    /**
     * Конструктор объекта - инициализация URI и QUERY_STRING
     */
    public function __construct() 
    {
        $this->_uri = S::get('Request')->getRequestURI();

        $this->_query_string = S::get('Request')->getQueryString();
        $this->_query_string = 
                (empty($this->_query_string)) ? "" : "?" . $this->_query_string;        
    }
    
    
    /**
     * распечатка таблицы
     * 
     * @return string
     */
    public function __toString() 
    {
        //если колонки не заданы
        if (empty($this->_columns)) {
            return (string)false;
        }
        
        //класс и стили для таблицы
        $classes     = (empty($this->_classes)) ? "" : "class='{$this->_classes}'";
        $style       = (empty($this->_style)) ? "" : "style='{$this->_style}'";            
        
        //если указаны групповые действия, заворачиваем таблицу в форму
        if (!empty($this->_group_actions) && !empty($this->_key_filed_name)) {
            $path =  $this->_uri . "group_action/" . $this->_query_string;
            $content = "<form id='grid_view_frm' method='POST' action='$path' ><table $classes $style>";
        }
        else {
            $content = "<table $classes $style>";
        }
                    
        //заголовок таблицы, если приказано использовать
        if ($this->_use_header) {
            $content .= "<thead><tr>";
            
            //колонка серийных номеров
            if ($this->_use_serial_numbers) {
                $content .= "<th class='td_serial_number'></th>";
                $i = 0;
            }   
            
            //колонка множественного выбора
            if (!empty($this->_group_actions) && !empty($this->_key_filed_name)) {
                $content .= "<th><input type='checkbox' name='cb_select_all_rows' value='1'></label></th>";
            }                    
                        
            //содержательные колонки
            foreach ($this->_columns as $column)
            {
                $header_classes = (empty($column->getHeaderClasses())) ? "" : "class='{$column->getHeaderClasses()}'";
                $header_style = (empty($column->getHeaderStyle())) ? "" : "class='{$column->getHeaderStyle()}'";
                              
                //сортировка, если указана для колонки
                if ($column->isSortable()) {  
                    $query_params = S::get('Request')->getQueryParams();
                    
                    $sort_class = ($column->getSortActive($query_params)) ? 'sort_active' : '';
                    $sort_dir   = $column->getSortDir($query_params);                    
                    $query      = $column->getSortQuery($query_params);
                    $uri        = (empty($this->_custom_uri)) ? $this->_uri : $this->_custom_uri;
                    
                    $content .= "<th $header_classes $header_style>"
                             . "<a title='Сортировать по данному полю' "
                             . "href='{$uri}{$query}{$this->_custom_uri_fragment}' class='sortable $sort_class sort_dir_$sort_dir'>"
                             . "<span>{$column->getLabel()}</span></a></th>";
                }
                else {
                    $content .= "<th $header_classes $header_style>{$column->getLabel()}</th>";
                }
            }
            
            //блок внутристрочных действий, если нужно
            if (!empty($this->_row_actions)) {
                $content .= "<th></th>";
            }
            
            $content .= "</thead></tr>";            
        }
        
        if (empty($this->_data)) {
            $colspan = count($this->_columns) + 1 + (int)(bool)$this->_row_actions + (int)$this->_use_serial_numbers;
            $content .= "<tr><td colspan='$colspan'>Нет записей</td></tr>";
        }
        else {
            //содержание        
            foreach ($this->_data as $row)
            {
                //установка ключевого аттрибута, если есть
                $key_attr    = (empty($this->_key_filed_name)) ? "" : "data-id='{$row[$this->_key_filed_name]}'";
                $readonly_class = (empty($this->_readonly_rule) || $row[$this->_readonly_rule['name']] != $this->_readonly_rule['value']) ? "" : " class='just-grey-text' ";
                $content .= "<tr $key_attr $readonly_class>";

                //колонка с серийными номерами
                if ($this->_use_serial_numbers) {
                    ++$i;
                    $content .= "<td class='td_serial_number'>$i</td>";
                }  

                //колонка множественного выбора, если заданы групповые действия и строка не ридонли
                if (!empty($this->_group_actions) && !empty($this->_key_filed_name)) {
                    if (!empty($this->_readonly_rule) 
                            && ($row[$this->_readonly_rule['name']] == $this->_readonly_rule['value']) ) {
                        $readonly = ' disabled="disabled" ';
                    }
                    else {
                        $readonly = '';
                    }
                    $content .= "<td><input $readonly type='checkbox' class='cb_selected_id' name='selected_id[]' value='{$row[$this->_key_filed_name]}'></td>";
                }            

                //содержательные колонки
                foreach ($this->_columns as $column)
                {    
                    $extra_classes = '';
                    if (!empty($this->_key_filed_name) 
                            && isset($this->_extra_classes[$column->getName()][$row[$this->_key_filed_name]])) {
                        $extra_classes = $this->_extra_classes[$column->getName()][$row[$this->_key_filed_name]];
                    }
                    $classes = $extra_classes . " " . $column->getClasses();
                    $td_classes = (empty($classes)) ? "" : "class='$classes'";
                    $td_style   = (empty($column->getStyle())) ? "" : "style='{$column->getStyle()}'" ;

                    $td_text_before = $column->getTdTextBefore();
                    $td_text_after  = $column->getTdTextAfter();
                    $td_text        = (!isset($row[$column->getName()]) || $row[$column->getName()] == '') ? 
                                            $column->getEmptyVal() : $row[$column->getName()];
                    //мэппинг
                    $td_text = $column->mapValue($td_text);                    

                    //форматирование
                    $td_text = $column->format($td_text);
                    
                    //Замена плейсхолдеров на значения соответствующих колонок
                    $td_text = $this->_mapRow($td_text, $row);                    

                    $content .= "<td $td_classes $td_style>{$td_text_before}{$td_text}{$td_text_after}</td>";
                }
                
                //блок внутристрочных действий, если есть
                if (!empty($this->_row_actions)) {
                    
                    //если строка является readonly, кнопки не рисуем
                    if (!empty($this->_readonly_rule) 
                            && ($row[$this->_readonly_rule['name']] == $this->_readonly_rule['value']) ) {
                        $content .= "<td align='right'></td>";
                    }
                    else {
                        $content .= "<td align='right'><div class='grid-view-row-btns btn-group'>";

                        foreach ($this->_row_actions as $ra)
                        {
                              $content .= $ra;
                        }

                        $content .= "</div></td>";
                        }                                        
                }
                $content .= "<tr>";
            }
        }                
        
        //блок группового действия в подвале таблицы
        if (!empty($this->_group_actions) && !empty($this->_key_filed_name)) {
            $colspan = count($this->_columns) + 1 + (int)(bool)$this->_row_actions + (int)$this->_use_serial_numbers;
            $group_actions_sb = new Select();
            $group_actions_sb->setName('sb_group_action')
                             ->setId('sb_group_action_' . $this->_id)
                             ->setFirstEmpty(false)
                             ->setClasses('form-control w200')
                             ->setStyle('display: inline-block;')
                             ->setData($this->_groupActionsToArray());
                                        
            $content .= "<tr><td class='group_action_row' align='right' colspan='$colspan'>"
                    . "Действие для выделенных элементов&nbsp;&nbsp;$group_actions_sb"
                    . "&nbsp;&nbsp;<button class='btn btn-success btn-do-action'>Применить</button></td></tr>";
        }
        else {
            $content .= "</table></form>";
        }
                
        //дефолтные обработчики, если приказано использовать
        if ($this->_use_default_scripts) {

            $script = str_replace(
                [":uri", ":query"], 
                [
                    (empty($this->_custom_actions_uri)) ? $this->_uri : $this->_custom_actions_uri, 
                    $this->_query_string
                ], 
                $this->_getWidgetTemplate(self::ACTION_TEMPLATE_SCRIPT)); 

            $content .= "<script type='text/javascript'>$script</script>";
        }
                
        return $content;
    }
    
    
    /**
     * Замена для текущей строки всех плейсхолдеров их значениями
     * 
     * @param string $text  Результирующий текст строки
     * @param array $row     Массив колонок
     * 
     */
    private function _mapRow($text, $row)
    {
        if (!is_array($row) || empty($row)) {
            return $text;
        }
        
        foreach($row as $name => $val)
        {
            $text = str_replace("{{" . $name . "}}", $val, $text);
        }
        
        return $text;
    }
    
    
    /**
     * Преобразование перечня групповых действий в массив значений для списка 
     * выбора
     * 
     * @return array
     */
    private function _groupActionsToArray()
    {
        $out = [];
        foreach ($this->_group_actions as $action)
        {
            $out[] = [
                'name' => $action->getLabel(), 
                'value' => $action->getAction()
            ];
        }
        return $out;
    }
    
    
    /**
     * Добавление колонки в описание таблицы<br/><br/>
     * <b>Обязательные</b> ключи массива $column:<br/>
     * name             - Имя колонки, должно соответвовать имени колонки в массиве data в GridView<br/><br/>
     * <b>Прочие</b> ключи массива $column:<br/>
     * label            - Заголовок колонки, если не указан, то в шапке будет отображаться name<br/>
     * classes          - CSS классы для ячейки колонки<br/>
     * style            - инлайн стиль для ячейки колонки<br/>
     * header_classes   - CSS классы для тега th в заголовке<br/>
     * header_style     - инлайн стиль для тега th в заголовке<br/>
     * text_before      - строка, добавляемая перед значением в td<br/>
     * text_after       - строка, добавляемая после значения в td<br/>
     * empty_val        - строка, отображаемая вместо пустой колонки<br/>
     * sortable         - признак сортировки по данной колонке<br/>
     * value_mapping    - мэппинг значений - массив элементов вида 'значение' => 'текст отображения'<br>
     * format           - форматирование значения. Возможные варианты: date, datetime, image, link, imagelink<br/>
     * format_params    - параметры форматирования. Для image: width, height, class. 
     *                    Для link: href, target, text, title. Для imagelink: width, height, class, href, target, title.<br/>
     * 
     * @param array $column
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function addColumn($column)
    {
        $this->_columns[] = new Column($column);
        
        return $this;
    }
    
    
    /**
     * Задать CSS классы для таблицы
     * 
     * @param string $classes  Перечень классов
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function setClasses($classes)
    {
        $this->_classes = $classes;
        
        return $this;
    }
    
    
    /**
     * Задать инлайн стиль для таблицы
     * 
     * @param string $style  инлайн стиль
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function setStyle($style)
    {
        $this->_style = $style;
        
        return $this;
    }    
    
    
    /**
     * Задать кастомный URI для таблицы
     * 
     * @param string $uri  Кастомный uri
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function setCustomURI($uri)
    {
        $parts = explode("#", $uri);
        
        if (count($parts) > 1 ) {
            $this->_custom_uri = $parts[0];
            $this->_custom_uri_fragment = "#" . $parts[1];
        }
        else {        
            $this->_custom_uri = $uri;
        }
        
        return $this;
    }    


    /**
     * Задать кастомный адрес для обработки внутристрочных и групповых действий
     * стандартными средствами
     * 
     * @param string $uri  URI
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function setCustomActionsURI($uri)
    {
        $this->_custom_actions_uri = $uri;
        
        return $this;
    }   
    
    
    /**
     * Установка массива данных - источника строк
     * Массив должен представлять из себя однородную матрицу, шириной >= количества колонок
     * 
     * @param array $data   массив значений
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function setData($data)
    {
        //проверка валидности входных данных
        if (is_array($data) && array_key_exists(0, $data)
                && count($data[0]) >= count($this->_columns)) { 
            
            $this->_data = $data;
        }
        
        return $this;
    }
    
    
    /**
     * Устанавливает флаг использования заголовка в таблице
     * 
     * @param bolean $flag  Флаг использования заголовка
     * 
     * @return \FW\Widget\GridView\GridView
     * 
     */
    public function useHeader($flag = true)
    {
        $this->_use_header = $flag;
        
        return $this;
    }

    
    /**
     * Устанавливает флаг использования обработчиков по умолчанию в экшенах
     * 
     * @param bolean $flag  Флаг использования обработчиков по умолчанию
     * 
     * @return \FW\Widget\GridView\GridView
     * 
     */
    public function useDefaultScripts($flag = true)
    {
        $this->_use_default_scripts = $flag;
        
        return $this;
    }    
    
    
    /**
     * Установка ключевого поля для строк таблицы
     * 
     * @param string $name
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function setKeyName($name)
    {
        $this->_key_filed_name = (string)$name;
        
        return $this;
    }      
    
    
    /**
     * Добавление к таблице функционала редактирования в колонке Actions
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function addEditRowAction()
    {
        $action = [
            'name'      =>  'edit',
            'content'   =>  $this->_getWidgetTemplate(self::ACTION_ROW_EDIT_TEMPLATE),
        ];

        $this->_row_actions[] = new RowAction($action);
        
        return $this;
    }
    
    
    /**
     * Добавление к таблице функционала удаления в колонке Actions
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function addDeleteRowAction()
    {
        $action = [
            'name'      =>  'delete',
            'content'   =>  $this->_getWidgetTemplate(self::ACTION_ROW_DELETE_TEMPLATE),
        ];
        
        $this->_row_actions[] = new RowAction($action);
        
        return $this;
    }
    
    
    /**
     * Добавление к таблице произвольного функционала в колонке Actions<br/><br/>
     * <b>Обязательные</b> ключи массива $action:<br/>
     * name             - Имя элемента<br/><br/>
     * <b>Прочие</b> ключи массива $action:<br/>
     * template         - Имя шаблона, который будет загружен в качестве элемента управления. 
     *                    Если параметр опущен, будет загружен шаблон по умолчанию<br/>
     * 
     * @param array $action массив для создания действия
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function addCustomRowAction($action = [])
    {
        if (!isset($action['template'])) {
            $action['content'] = $this->_getWidgetTemplate(self::ACTION_ROW_CUSTOM_TEMPLATE);
        }
        else {
            $action['content'] = $this->_getWidgetTemplate($action['template']);
        }
        
        unset($action['template']);
        
        $this->_row_actions[] = new RowAction($action);
        
        return $this;
    }
    
    
    /**
     * Добавление к таблице функции группового удаления
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function addDeleteGroupAction()
    {
        $action = [
            'name'      => 'delete',
            'label'     => 'Удалить',
            'action'    => 'delete',
        ];
        
        $this->_group_actions[] = new GroupAction($action);
        
        return $this;
    }
    
    
    /**
     * Добавление к таблице произвольной групповой функции
     * 
     * @param array $action массив для создания действия
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function addCustomGroupAction($action)
    {
        $this->_group_actions[] = new GroupAction($action);
        
        return $this;
    }
    
    
    /**
     * Задает для отдельной ячейки особый стиль
     * 
     * @param string $column_name   Имя колонки (атрибут name)
     * @param string $line_key      Ключ строки (атрибут key)
     * @param string $class         Список классов
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function addTDExtraClass($column_name, $line_key, $class)
    {
        $this->_extra_classes[$column_name][$line_key] = $class;
        
        return $this;
    }
    
    
    /**
     * Устаналивает правило для задания readonly-строки (запрет редактирования
     * строки у которой значение $column_name = $value)
     * 
     * @param string $column_name   Имя колонки
     * @param string $value         КОнтрольное значение
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function setReadonlyRowRule($column_name, $value) 
    {
        $this->_readonly_rule = ['name' => $column_name, 'value' => $value];
        
        return $this;
    }
    
    
    
    /**
     * Установка режима отображения столбца с серийными номерами
     * 
     * @param boolean $flag     Флаг отображения серийных номеров
     * 
     * @return \FW\Widget\GridView\GridView
     */
    public function useSerialNumbers($flag = true)
    {
        $this->_use_serial_numbers = $flag;
        
        return $this;
    }
}

