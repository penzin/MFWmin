<?php

namespace FW;

/**
 * Работа с ответом приложения
 * 
 * @author Roman V. Penzin <penzin.r.v@gmail.com>
 */
class Response
{
    /**
     * Код ответа: все ОК
     */
    const HTTP_OK = 200;
    
    
    /**
     * Код ответа: успешно создано
     */
    const HTTP_CREATED = 201;
    
    
    /**
     * Код ответа: нет данных по запросу
     */
    const HTTP_NO_CONTENT = 204;
    
    
    /**
     * Код ответа: не корректный запрос
     */
    const HTTP_BAD_REQUEST = 400;
    
    
    /**
     * Код ответа: не прошел аутенфикацию
     */
    const HTTP_UNAUTHORIZED = 401;
    
    
    /**
     * Код ответа: не прошел авторизацию (не хватает прав)
     */
    const HTTP_FORBIDDEN = 403;
    
    
    /**
     * Код ответа: документ не найден
     */
    const HTTP_NOT_FOUND = 404;
    
    
    /**
     * Код ответа: проблемы на сервере
     */
    const HTTP_SERVER_ERROR = 500;
    
    
    /**
     * Код ответа: запрошенный метод неприменим в данной конфигурации
     */
    const HTTP_NOT_IMPLEMENTED = 501;
    
    
    /**
     * XML Доумент
     */
    const CONTENT_TYPE_XML = 'text/xml';
    
    
    /**
     * Простой текст
     */
    const CONTENT_TYPE_PLAIN_TEXT = 'text/plain';
    
    
    /**
     * HTML документ
     */
    const CONTENT_TYPE_HTML = 'text/html';
    
    
    /**
     * CSV файл
     */
    const CONTENT_TYPE_CSV = 'text/csv';
    
    
    /**
     * JSON документ
     */
    const CONTENT_TYPE_JSON = 'application/json';
    
    
    /**
     * Поток бинарных данных
     */
    const CONTENT_TYPE_BINARY = 'application/octet-stream';
    
    
    /**
     * PDF документ
     */
    const CONTENT_TYPE_PDF = 'application/pdf';
    
    
    /**
     * ZIP архив
     */
    const CONTENT_TYPE_ZIP = 'application/zip';
    
    
    /**
     * GZIP архив
     */
    const CONTENT_TYPE_GZIP = 'application/gzip';
    
    
    /**
     * RAR архив
     */
    const CONTENT_TYPE_RAR = 'application/x-rar-compressed';
    
    
    /**
     * TAR архив
     */
    const CONTENT_TYPE_TAR = 'application/x-tar';    
    
    /**
     * Звук в формате WAV
     */
    const CONTENT_TYPE_WAV = 'audio/vnd.wave';
    
    
    /**
     * Звук в формате WMA
     */
    const CONTENT_TYPE_WMA = 'audio/x-ms-wma';
    
    
    /**
     * Звук в формате OGG
     */
    const CONTENT_TYPE_OGG = 'audio/ogg';
    
    
    /**
     * Звук в формате MP3
     */
    const CONTENT_TYPE_MP3 = 'audio/mpeg';
    
    
    /**
     * Изображение в формате GIF
     */
    const CONTENT_TYPE_GIF = 'image/gif';
    
    
    /**
     * Изображение в формате JPG
     */
    const CONTENT_TYPE_JPG = 'image/jpeg';
    
    
    /**
     * Изображение в формате PNG
     */
    const CONTENT_TYPE_PNG = 'image/png';
    
    
    /**
     * Изображение в формате SVG
     */
    const CONTENT_TYPE_SWG = 'image/svg+xml';
    
    
    /**
     * Изображение в формате TIFF
     */
    const CONTENT_TYPE_TIFF = 'image/tiff';
    
    
    /**
     * Изображение в формате ICO
     */
    const CONTENT_TYPE_ICO = 'image/vnd.microsoft.icon';
    
    
    /**
     * Email
     */
    const CONTENT_TYPE_EMAIL = 'message/rfc822';
    
    
    /**
     * Данные с формы (в т.ч. файлы)
     */
    const CONTENT_TYPE_POST_MULTIPART_FORM_DATA = 'multipart/form-data';
    
    
    /**
     * Данные с формы
     */
    const CONTENT_TYPE_POST_FORM = 'application/x-www-form-urlencoded';
    
    
    /**
     * Видео в формате MPEG
     */
    const CONTENT_TYPE_MPEG = 'video/mpeg';
    
    
    /**
     * Видео в формате MP4
     */
    const CONTENT_TYPE_MP4 = 'video/mp4';
    
    
    /**
     * Видео в формате MWMV
     */
    const CONTENT_TYPE_WMV = 'video/x-ms-wmv';
    
    
    /**
     * Видео в формате FLV
     */
    const CONTENT_TYPE_FLV = 'video/x-flv';
    
    
    /**
     * Видео в формате 3GP
     */
    const CONTENT_TYPE_3GP = 'video/3gpp';
    
    
    /**
     * MS EXCEL файл
     */
    const CONTENT_TYPE_EXCEL = 'application/vnd.ms-excel';
    
    
    /**
     * MS Word Файл
     */
    const CONTENT_TYPE_KML = 'application/vnd.google-earth.kml+xml';
    
    
    /**
     * MS Power Point файл
     */
    const CONTENT_TYPE_POWER_POINT = 'application/vnd.ms-powerpoint';
    
    
    /**
     * Флэшка (SWF)
     */
    const CONTENT_TYPE_FLASH = 'application/x-shockwave-flash';   
    
    
    /**
     * @var string Тип данных 
     */
    private $_content_type = self::CONTENT_TYPE_HTML;
    
    
    /**
     * @var integer Время жизни КЭША в секундах
     */
    private $_cache_seconds = 0;
    
    
    /**
     * @var string Кодировка 
     */
    private $_charset = 'utf-8';
    
    
    /**
     * @var string Имя файла, в виде которого будет выдан документ
     */
    private $_attachment_filename = '';
    
    
    /**
     * @var string Контроль доступа Allow Origin
     */
    private $_allow_origin = '';
    
    
    /**
     *
     * @var string Политика отображения сайта в iframe
     */
    private $_XFO = '';
    
    
    /**
     * @var string Поведение браузера при обнаружении им XSS
     */
    private $_XXSSP = '';
    
    
    /**     
     * @var string Защита от сниффинга на старых браузерах
     */
    private $_XCTO = '';
    
    /**
     * Выполнить прямой редирект по адресу
     * 
     * @return void
     */
    public function redirect($url)
    {
        header("Location: $url");
        exit();
    }
    
    
    /**
     * Вернуть JSON
     * 
     * @param array $data   Возвращаемые данные
     * 
     * @return \FW\Response
     */
    public function toJSON($data)
    {
        echo json_encode($data);
        
        return $this;
    }
    
    
    /**
     * Установить код ответа
     * 
     * @param ineger $code
     * 
     * @return \FW\Response
     */
    public function setCode($code)
    {
        http_response_code($code);
        
        return $this;
    }
    
    
    /**
     * Установить тип содержимого
     * 
     * @param string $content_type
     * 
     * @return \FW\Response
     */
    public function setContentType($content_type)
    {
        $this->_content_type = $content_type;
        
        return $this;
    }
    
    
    /**
     * Установить кэширование
     * 
     * @param integer $seconds  Количество секунд жизни кэша
     * 
     * @return \FW\Response
     */
    public function setCache($seconds)
    {
        $this->_cache_seconds = $seconds;
        
        return $this;
    }
    
    
    /**
     * Проверка факта отправки заголовков
     * 
     * @return boolean
     */
    public function isHeadersSent()
    {
        return headers_sent();
    }
    
    
    /**
     * Установить кодировку заголовка
     * 
     * @param string $charset   Кодировка
     * 
     * @return \FW\Response
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
        
        return $this;
    }
    
    
    /**
     * Установить имя файла, которое будет загружаться
     * 
     * @param string $name Имя файла
     * 
     * @return \FW\Response
     */
    public function setAttachmentFilename($name)
    {
        $this->_attachment_filename = $name;
        
        return $this;
    }
    
    
    /**
     * Установка контроля доступа allow origin
     * 
     * @param string $mask  Маска
     * 
     * @return \FW\Response
     */
    public function setAllowOrigin($mask)
    {
        $this->_allow_origin = $mask;
        
        return $this;
    }
    
    
    /**
     * Установка заголовков
     * 
     * @return boolean
     */
    public function applyHeaders()
    {
        //установка типа и вида данных
        if (!empty($this->_content_type)) {
            
            //если скачивание файла
            if (!empty($this->_attachment_filename)) {
                header("Content-Type: {$this->_content_type}");
                header("Content-Disposition: attachment; filename=\"{$this->_attachment_filename}\"");
            }
            else {
                //если указана кодировка
                if (!empty($this->_charset)) {
                    header("Content-Type: {$this->_content_type}; charset={$this->_charset}");
                }
                else {
                    header("Content-Type: {$this->_content_type}");
                }
            }
        }
        
        //кэширование
        if ($this->_cache_seconds == 0) {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
        }
        else {
            header(
                    "Expires: " 
                  . gmdate("D, d M Y H:i:s", time() + $this->_cache_seconds) 
                  . " GMT");
        }
        
        //контроль доступа allow rigin
        if (!empty($this->_allow_origin)) {
             header("Access-Control-Allow-Origin: {$this->_allow_origin}");  
        }
        
        //контроль отображения в iframe
        if (!empty($this->_XFO)) {
            header("X-Frame-Options: {$this->_XFO}");
        }
        
        //XSS Browser protection
        if (!empty($this->_XXSSP)) {
            header("X-XSS-Protection: {$this->_XXSSP}");
        }        
        
        //Защита от снифинга на старых браузерах
        if (!empty($this->_XCTO)) {
            header("X-Content-Type-Options: {$this->_XCTO}");
        }           
    }
    
    
    /**
     * Возвращает список установленных заголовков
     * 
     * @return array
     */
    public function getAppliedHeaders()
    {
        return headers_list();
    }
    
    
    /**
     * Запрет отображения сайта в iframe на других ресурсах
     * 
     * @return \FW\Response
     */
    public function setXframeOptionsSame()
    {
        $this->_XFO = 'SAMEORIGIN';
        
        return $this;
    }
    
    
    /**
     * Запрет отображения страницы в случае, елси браузер обнаружит попытку XSS
     * 
     * @return \FW\Response
     */
    public function setXSSBrowserBlock()
    {
        $this->_XXSSP = '1; mode=block';
        
        return $this;
    }    
    
    
    /**
     * Защита от снифиинга на браузерах у динозавров
     * 
     * @return \FW\Response
     */
    public function setXcontentTypeNoSniff()
    {
        $this->_XCTO = 'nosniff';
        
        return $this;
    }      
}

