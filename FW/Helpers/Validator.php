<?php

namespace FW\Helpers;

/**
 * Класс-валидатор 
 * 
 * @author fedyak <fedyak.82@gmail.com>
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 * 
 */
class Validator 
{
  
    /**
     * Проверяет корректность электронной почты
     * 
     * @param string $email
     * @return boolean
     */
    public static function isEmail($email)
    {
        if ( ! preg_match("/^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$/i", $email)) {
            return false;
        }

        return true;
    }
    
  
    /**
     * Обязательное поле
     */
    const REQUIRE_VALUE = 'Обязательное поле!';
    
    
    /**
     * Неверный тип
     */
    const WRONG_EMAIL = 'Некорректный адрес электронной почты!';    
    
    
    /**
     * Неверный тип
     */
    const WRONG_TYPE = 'Поле заполнено неверно!';
    
    
    /**
     * Неверный тип FLOAT
     */
    const WRONG_FLOAT = 'В поле указано некорректное вещественое число';
    
    
    /**
     * Входные и выходные данные
     * 
     * @var array
     */
    private $_data;
    
    
    /**
     * Модель данных
     * 
     * @var array
     */
    private $_model;
    
    

    /**
     * Фильтрует массив исходных параметров используя массив, описанный в контроллере
     */
    private function _validate()
    {
        if (!is_array($this->_data) || empty($this->_data)) {
            $this->_result = false;
            $this->_data = [];
            return $this;
        }
        
        $res = $invalid_vars = [];
        $this->_result = true;
        
        foreach ($this->_model as $key => $access) 
        {
            //если существует набор валидаторов
            if (is_array($access)) 
            {
                foreach ($access as $a) 
                {
                    switch ($a) 
                    {
                        //обязательное ненулевое поле
                        case 'r':
                            if (!ArrayUtils::akeAndNotEmpty($key, $this->_data)) {
                                $this->_result = false;
                                $invalid_vars[$key]['errors'][] = self::REQUIRE_VALUE;
                                $invalid_vars[$key]['label'] = $access;
                            }
                        break;
                        
                        //обязательное присуствие ключа (может быть ноль)
                        case 'exist':
                            if (!ArrayUtils::akeAndIsset($key, $this->_data)) {
                                $this->_result = false;
                                $invalid_vars[$key]['errors'][] = self::REQUIRE_VALUE;
                                $invalid_vars[$key]['label'] = $access;
                            }  
                        break;
                            
                        //email поле
                        case 'email':  
                            if (!self::isEmail($this->_data[$key])) {
                                $this->_result = false;
                                $invalid_vars[$key]['errors'][] = self::WRONG_EMAIL;
                                $invalid_vars[$key]['label'] = $access;
                            }
                        break;  
                        
                        //float
                        case 'float':
                            //если значение задано, анализируем его
                            if (ArrayUtils::akeAndNotEmpty($key, $this->_data)) {
                                $this->_data[$key] = str_replace(",", ".", $this->_data[$key]);

                                if ((float)$this->_data[$key] == 0 && $this->_data[$key] != '0' && $this->_data[$key] != '0.0') {
                                    $this->_result = false;
                                    $invalid_vars[$key]['errors'][] = self::WRONG_FLOAT;
                                    $invalid_vars[$key]['label'] = $access;
                                }
                            }
                            
                        break;                             
                    }                
                }                
            }

            if (array_key_exists($key, $this->_data)) {
                $res[$key] = $this->_data[$key];
            }
        }

        $this->_data = ($this->_result === true) ? $res : $invalid_vars;
        
        return $this;
    }      
  
    
    /**
     * 
     * 
     * @param type $data        Массив с входными данными
     * @param type $model       Модель данных
     */
    public function __construct($data, $model) 
    {
        $this->_data = $data;
        $this->_model = $model;
        $this->_validate();
    }
    
    
    /**
     * Прошли ли данные валидацию
     * 
     * @return boolean
     */
    public function isValid()
    {
        return $this->_result;
    }
    
    
    /**
     * Массив с данными
     * 1. Если данные прошли валидацию - массив содержит значения с полями, 
     *    соответсвтвующими модели
     * 2. Если данне не прошли валидацию - массив содержит поля, не прошедшие
     *    валицию и причину, по которой валидация не была пройдена
     * 
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }    
    
    
}


