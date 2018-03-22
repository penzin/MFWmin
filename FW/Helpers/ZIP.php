<?php

namespace FW\Helpers;


/**
 * Класс для генерации zip архива
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
class ZIP 
{  
    /**
     * Создание ZIP архива
     * 
     * @param array $files          Массив файлов [$path_to_file => 'name_of_file_in_archive.ext', ...]
     * @param string $destination   
     * @param boolean $overwrite    
     * @return boolean
     */
    public static function createZip($files = [], $destination = '', $overwrite = false) 
    {
        if(file_exists($destination) && !$overwrite) { 
            return false; 
        }

        if (file_exists($destination) && $overwrite) {
            unlink($destination);
        }
        
        $valid_files = [];
        if(is_array($files)) {
            foreach($files as $file => $label) {
                if(file_exists($file)) {
                    $valid_files[$file] = $label;
                }
            }
        }

        if(count($valid_files) > 0) {
            $zip = new \ZipArchive();
            if($zip->open($destination, \ZipArchive::CREATE) !== true) {
                return false;
            }

            foreach($valid_files as $file => $label) {
                if (file_exists($file)) {
                    $zip->addFile($file, $label);
                }
            }
            
            //debug
            //echo 'Файлов в архиве ', $zip->numFiles ,'. статус: ', $zip->status;

            $zip->close();
            return file_exists($destination);
        }
        
        return false;
    }


}