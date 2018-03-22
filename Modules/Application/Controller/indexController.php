<?php

namespace Modules\Application\Controller;

use FW\View;


/**
 * Основной контроллер модуля Application
 */
class indexController extends \FW\Controller\Controller
{  
    /**
     *
     * @var Modules\Application\Model\application Модель
     */
    private $_application;
    
    
    /**
     * Конструктор приложения
     */
    public function __construct() 
    {
        //$this->_application = new \Modules\Application\Model\application();
        $this->_application = $this->getModel('application');
    }
    
    
    
    /**
     * Домашнаяя страница сайта
     * 
     * @return View
     */
    public function indexAction()
    {        
        $this->setBreadcrumbs([
            ['Главная', ''],
        ]);        
        
        return (new View([
            'header' => $this->_application->getAppName(),
        ]));
    }    
}