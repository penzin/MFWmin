<?php

namespace Modules\Application\Model;

use FW\Model;

/**
 * Модель для работы (дефолтная)
 * 
 */
class application extends Model
{ 
    
    /**
     * Возвращает имя приложения
     * 
     * @return string
     */
    public function getAppName()
    {
        return "Тестовое приложение MFW";
    }
}