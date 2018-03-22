<?php

namespace FW\Files;

use FW\Vendors\SimpleImage;
use FW\Files\File;

/**
 * Хэлпер для работы с изображениями
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
class Image extends File
{
 
   /**
     * Экземпляр класса SimpleImage
     * 
     * @var \FW\Vendors\SimpleImage 
     */
    private $_img = null;
    
    
    /**
     * Имя файла миниатюры
     * 
     * @var string
     */    
    protected $_destination_file_thumbnail = null;
    
    
    /**
     * Функция для работы с изображениями средстами библиотеки SimpleImage
     * 
     * @return \FW\Vendors\SimpleImage 
     */
    public function image()
    {
        if (is_null($this->_source_file) || is_null($this->_destination_file)) {
            return false;
        }
        
        if (is_null($this->_img)) {
            $this->_img = new SimpleImage($this->_source_file);
        }
        
        return $this->_img;
    }

    
    /**
     * Создает миниатюру
     * 
     * @param int $width            Ширина
     * @param int $height           Высота
     * @param string $focal         Может принимать значения:<br>
     *                              top, bottom, left, right, <br>
     *                              top left, top right, bottom left, bottom right,<br>
     *                              center
     * 
     * @param string $postfix       Постфикс (_th по умолчанию)
     * @param bool   $crop          Если True - использовать crop
     * 
     * @return boolean|string
     */
    public function createThumbnail($width, $height, $focal = 'center', $postfix = '_th', $crop = false)
    {
        if (is_null($this->_source_file) || is_null($this->_destination_file)) {
            return false;
        }        
        
        $img = new SimpleImage($this->_source_file);
        
        $this->_destination_file_thumbnail = $this->_createThumbnailName($postfix);
        
        if (!$crop) {
            $img->best_fit($width, $height);
        }
        else {
            $img->thumbnail($width, $height, $focal);
        }
        
        self::makePath(WWW_DIR . $this->_destination_file_thumbnail);
        
        try {
            $img->save(WWW_DIR . $this->_destination_file_thumbnail);
        } catch (\Exception $ex) {
            error_log(date('d.m.Y H:i:s') . ": " . $ex->getMessage() . " (" . $ex->getFile() . ":" . $ex->getLine() . ")");
            return false;
        }

        return $this->_destination_file_thumbnail;
    }
    
    
    /**
     * Удаление файла миниатюры
     * 
     * @return boolean
     */
    public function deleteThumbnailFile()
    {
        if (file_exists(WWW_DIR . $this->_destination_file_thumbnail)) {
            return unlink(WWW_DIR . $this->_destination_file_thumbnail);
        }
        return false;        
    }
    
    
    /**
     * Формирует имя файла для миниатюры
     * 
     * @param string $postfix
     * @return string
     */
    private function _createThumbnailName($postfix)
    {
        $file = self::getFilePathInfo($this->_destination_file);
        return $file['directory'] . '/' . $file['file_name'] . $postfix . '.' . $file['file_ext'];
    }
    
    
    /**
     * Загрузка файла в указанную папку
     * 
     * @return boolean
     */
    public function upload()
    {
        if (is_null($this->_source_file) || is_null($this->_destination_file)) {
            return false;
        }
        
        self::makePath(WWW_DIR . $this->_destination_file);
        
        try {
            return $this->_img->save(WWW_DIR . $this->_destination_file);
        } catch (\Exception $ex) {
            error_log(date('d.m.Y H:i:s') . ": " . $ex->getMessage() . " (" . $ex->getFile() . ":" . $ex->getLine() . ")");
            return false;
        }
    }
    
    
    
}