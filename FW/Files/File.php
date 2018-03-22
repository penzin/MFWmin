<?php

namespace FW\Files;


/**
 * Хэлпер для работы с файлами
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
class File 
{
   
    
    /**
     * Исходный файл
     * 
     * @var string
     */
    protected $_source_file = null;
    
    
    /**
     * Имя файла после загрузки
     * 
     * @var string
     */
    protected $_destination_file = null;
    
    
    /**
     * @var type Размер загружаемого файла
     */
    protected $_file_size = null;
    
    
    
    
    /**
     * Генерирует уникальный md5 хэш
     * 
     * @return string   (32 символа)
     */
    protected function _generateUniqueHash()
    {
        return md5(uniqid(rand(),1));
    }
    
    
    /**
     * Создает структуру папок на диске
     * 
     * @param string $path      Путь к файлу (не к папке!)
     * @return boolean
     */
    public static function makePath($path)
    {
        $dir = pathinfo($path, PATHINFO_DIRNAME);

        if (is_dir($dir)) {
            return true;
        } 
        else {
            if (self::makePath($dir)) {
                if (mkdir($dir)) {
                    chmod($dir, 0700);
                    return true;
                }
            }
        }

        return false;
    }     
    
    
    /**
     * 
     * @param string $src
     * @param string $dst
     */
    public static function recurseCopy($src, $dst) 
    { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ($file = readdir($dir))) 
        { 
            if (($file != '.') && ($file != '..')) { 
                if (is_dir($src . '/' . $file)) { 
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file, $dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }    

    
    /**
     * Возвращает расширение файла
     * 
     * @access static
     * 
     * @param string $file_name
     * @return mixed
     */
    public static function getFileExt($file_name)
    {
        return pathinfo($file_name, PATHINFO_EXTENSION);
    }    
    
    
    /**
     * Возвращает каталог файла
     * 
     * @access static
     * 
     * @param string $file_name
     * @return mixed
     */
    public static function getFileDir($file_name)
    {
        return pathinfo($file_name, PATHINFO_DIRNAME);
    }    
    
    
    
    /**
     * Возвращает имя файла без расширения
     * 
     * @access static
     * 
     * @param string $file_name
     * @return mixed
     */
    public static function getFileName($file_name)
    {
        return pathinfo($file_name, PATHINFO_FILENAME);
    }      
    
    
    /**
     * Возвращает массив:<br><br>
     *      [<br>
     *          'directory'     =>      <br>
     *          'file_name'     =>      <br>
     *          'file_ext'      =>      <br>  
     *      ]
     *  
     * @access static
     * 
     * @param string $file_name
     * @return array
     */
    public static function getFilePathInfo($file_name)
    {
        return [
            'directory'     =>      self::getFileDir($file_name),
            'file_name'     =>      self::getFileName($file_name),
            'file_ext'      =>      self::getFileExt($file_name),
        ];
    }
    
    
    /**
     * Удаление файла
     * 
     * @access static
     * 
     * @param string $file_name     Полный путь к файлу
     * @return boolean              
     */
    public static function unlink($file_name)
    {
        if (file_exists($file_name) && is_file($file_name)) {
            return unlink($file_name);
        }
        
        return false;
    }
    
    
    /**
     * Проверяет ошибки при загрузке файлов
     * 
     * @param string $file
     * @return boolean
     */
    private function _checkServerError($file)
    {
        switch ($_FILES[$file]['error'])
        {
            case UPLOAD_ERR_INI_SIZE:
                return false;
                
        }            
        
        return true;
    }
    
    
    /**
     * Возвращает путь к файлу назначения
     * 
     * @return string
     */
    public function getDestinationFileName()
    {
        return is_null($this->_destination_file) ? '' : $this->_destination_file;
    }
    
    
    /**
     * Подготовка файла к загрузке
     * 
     * @param string $file                  Название поля загружаемого файла (индекс в массиве $_FILES)
     * @param string $path                  Путь загрузки файла
     * @param boolean $save_origin_name     Сохранить оригинальное название файла (если файл существует - будет переписан)
     * @return boolean|string               Путь к файлу назначения либо false в случае если файл не загрузился на сервер
     */
    public function prepareUpload($file, $path, $save_origin_name = false)
    {
        if (!isset($_FILES[$file]) || empty($_FILES[$file]["tmp_name"])) {
            return false;
        }

        if (!$this->_checkServerError($file)) {
            return false;
        }

        $this->_file_size = $_FILES[$file]["size"];
        
        $this->_source_file = $_FILES[$file]["tmp_name"];
        
        $file_name = $save_origin_name ? $_FILES[$file]['name'] : $this->_generateUniqueHash();
        
        $this->_destination_file = $path . $file_name . "." . self::getFileExt($_FILES[$file]['name']);        
        
        return $this;
    }
    
    
    /**
     * Загрузка файла в указанный каталог
     * 
     * @return boolean
     */
    public function upload()
    {
        if (is_null($this->_source_file) || is_null($this->_destination_file)) {
            return false;
        }
        
        self::makePath(WWW_DIR . $this->_destination_file);
        return move_uploaded_file($this->_source_file, WWW_DIR . $this->_destination_file);        
    }
    
    
    /**
     * Получение размера файла в байтах
     * 
     * @return integer
     */
    public function getFileSize()
    {
        return $this->_file_size;
    }
    
    
    /**
     * Удаляет созданный файл
     * 
     * @return boolean
     */
    public function deleteDestinationFile()
    {
        if (file_exists(WWW_DIR . $this->_destination_file)) {
            return unlink(WWW_DIR . $this->_destination_file);
        }
        return false;
    }
    
    
    
    
}
