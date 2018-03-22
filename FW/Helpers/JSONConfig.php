<?php

namespace FW\Helpers;


/**
 * Класс для работы с конфигом приложения в формате JSON
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
class JSONConfig
{    
    
    /**
     * @var type Массив значений
     */
    private $_params;
    
    
    /**
     * @var type Имя файла конфига
     */
    private $_file_name;

    
    /**
     * Флаг возможности записи в файл
     * 
     * @var boolean
     */
    private $_is_writeable = true;
    
    
    
    /**
     * Флаг возможности чтения из файла
     * 
     * @var boolean
     */
    private $_is_readable = true;
    
    
    /**
     * Конструктор класса: создание или загрузка конфига
     * 
     * @param string $file_name Имя файла
     * 
     * @return void
     */
    public function __construct($file_name = APP_JSON_CONFIG_FILE_NAME) 
    {
        if (empty($file_name)) {
            return;
        }

        if (!is_readable($file_name)) {
            $this->_is_readable = false;
        }
        
        $folder = \FW\Files\File::getFileDir($file_name);
        if (!is_writable($folder)) {
            $this->_is_writeable = false;
        }
        
        $this->_file_name = $file_name;

        if (!file_exists($file_name)) {
            $this->_params = [];
            if ($this->isWriteable()) {
                file_put_contents($file_name, '');
            }
            return;
        } 
        
        if ($this->isReadable()) {
            $buf = file_get_contents($file_name);
            $this->_params = json_decode($buf, true);
        }
    }
    
    
    /**
     * Проверка, является ли массив ассоциативным
     * 
     * @param array $arr
     * @return boolean
     */
    private function _isAssoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }      

    
    /**
     * Дамп массива параметров - значений
     * 
     * @return string
     */
    public function __toString()
    {
        $str  = "<pre>";
        $str .= print_r($this->_params, true);
        $str .= "</pre>";
        
        return $str;
    }    

    
    /**
     * Вовзарщает массив значений
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
    
    
    /**
     * Сохранение конфига
     * 
     * @return boolean|int      
     */
    public function save()
    {
        if (!file_exists($this->_file_name) || (!$this->isWriteable())) {
            return false;
        }
        
        return file_put_contents($this->_file_name, json_encode($this->_params));
    }
    
    
    /**
     * Массив названий параметров
     * 
     * @return boolean|array
     */
    public function keys()
    {
        if (!is_array($this->_params)) {
            return false;
        }
        
        return array_keys($this->_params);
    }
    
    
    /**
     * Чтение значения параметра
     * 
     * @param string $param     Ключ параметра
     * 
     * @return boolean|mixed
     */
    public function read($param)
    {
        if (!isset($this->_params[$param])) {
            return false;
        }
        
        return $this->_params[$param];
    }
    
    
    /**
     * Запись значения параметра
     * 
     * @param string $param         Ключ (название параметра)
     * @param mixed $value          Значение
     * 
     * @return \FW\Helpers\JSONConfig
     */
    public function set($param, $value)
    {
        $this->_params[$param] = $value;
        
        return $this;
    }

    
    /**
     * Установка параметров в виде ассоциативного массива      
     * (с заменой значений при совпадении ключей)
     * 
     * @param array $assoc_array            Ассоциативный массив
     * 
     * @return \FW\Helpers\JSONConfig
     */
    public function setArray($assoc_array)
    {
        if (is_array($assoc_array) 
                && (count($assoc_array) > 0) 
                && $this->_isAssoc($assoc_array)) {
            $this->_params = $assoc_array;
        }
        
        return $this;
    }
    

    /**
     * Добавление параметров в виде ассоциативного массива      
     * (с заменой значений при совпадении ключей)
     * 
     * @param array $assoc_array            Ассоциативный массив
     * 
     * @return \FW\Helpers\JSONConfig
     */
    public function addArray($assoc_array)
    {
        if (is_array($assoc_array) 
                && (count($assoc_array) > 0) 
                && $this->_isAssoc($assoc_array)) {
            $this->_params = array_merge($this->_params_, $assoc_array);
        }
        
        return $this;
    }    
    
    
    /**
     * Удалить параметр
     * 
     * @param string $param
     * 
     * @return \FW\Helpers\JSONConfig
     */
    public function remove($param)
    {
        if (isset($this->_params[$param])) {
            unset($this->_params[$param]);
        }
        
        return $this;
    }
    
    
    /**
     * Создает резервную копию файла конфигурации
     * 
     * @return boolean|string
     */
    public function make_bak()
    {
        if (!$this->isWriteable()) {
            return false;
        }
        
        if (!empty($this->_file_name) && file_exists($this->_file_name)) {
            $path_info = pathinfo($this->_file_name);
            $file_name = $path_info['filename'];
            $path      = $path_info['dirname'];
            
            $bak_file  = $path . "/" . $file_name . ".json.bak";
            
            if (copy($this->_file_name, $bak_file)) {
                return $bak_file;
            }
        }
        
        return false;
    }
    
    
    /**
     * Восстановить конфиг из резервной копии
     * 
     * @return boolean
     */
    public function restore_bak()
    {
        if (!$this->isReadable()) {
            return false;
        }
        
        if (!empty($this->_file_name) && file_exists($this->_file_name)) {
            $path_info = pathinfo($this->_file_name);
            $file_name = $path_info['filename'];
            $path      = $path_info['dirname'];
            
            $bak_file  = $path . "/" . $file_name . ".json.bak";
            
            if (copy($bak_file, $this->_file_name)) {
                return true;
            }            
        }
        
        return false;
    }
    
    
    /**
     * Сериализация массив параметров
     * 
     * @deprecated
     * 
     * @return type
     */
    public function serialize()
    {
        if (count($this->_params) > 0) {
            return serialize($this->_params);
        }
    }
    
    
    /**
     * Is JSON file writeable?
     *  
     * @return boolean
     */
    public function isWriteable()
    {
        return $this->_is_writeable;
    }
    
    
    /**
     * Is JSON file readable?
     *  
     * @return boolean
     */
    public function isReadable()
    {
        return $this->_is_readable;
    }    
    
}