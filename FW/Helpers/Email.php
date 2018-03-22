<?php

namespace FW\Helpers;

/**
 * Хелпер для работы с отправки писем
 * 
 * @author fedyak <fedyak.82@gmail.com>
 */
final class Email 
{

    /**
     * Успешно отправлено
     */
    const STATUS_SUCCESS = 1;
    
    
    /**
     * Ошибка отправки
     */
    const STATUS_FAIL = 2;
    
    
    /**
     * Ожидает отправки
     */
    const STATUS_NOT_SEND = 3;


    /**
     * Имя отправителя
     * 
     * @var string
     */
    private $name_from;


    /**
     * Почта отправителя
     * 
     * @var string
     */
    private $email_from;

    
    /**
     * Кодировка
     * 
     * @var string
     */
    private $data_charset = "UTF-8";

    
    /**
     * Кодировка
     * 
     * @var string
     */    
    private $email_charset = "UTF-8";

    
    /**
     * Тема письма
     * 
     * @var string
     */
    private $subject;

    
    /**
     * Тело письма
     * 
     * @var string
     */
    private $body;

    
    /**
     * Вложения
     * 
     * @var array 
     */
    private $attaches = [];

    
    /**
     * Список получателей
     *  
     * @var array
     */
    private $email_list = [];

    
    /**
     * Статистика по отправке (всего, отправлено, не отправлено)
     * 
     * @var array
     */
    private $send_stat = [];

    
    /**
     * Декодирование в нужную кодировку
     * 
     * @param string $str
     * @param string $data_charset
     * @param string $send_charset
     * @return string
     */
    private function _mimeHeaderEncode($str, $data_charset, $send_charset) 
    {
        if($data_charset != $send_charset) {
            $str = iconv($data_charset, $send_charset, $str);
        }

        return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
    }


    /**
     * Отправка письма
     * 
     * @return boolean
     */
    private function sendMailWithAttaches()
    {
        $EOL = "\r\n";

        $boundary = "--".md5(uniqid(time()));

        $subject = $this->_mimeHeaderEncode($this->subject, $this->data_charset, $this->email_charset);
        $from =  $this->_mimeHeaderEncode($this->name_from, $this->data_charset, $this->email_charset)
                           .' <' . $this->email_from . '>';

        if ($this->data_charset != $this->email_charset) {
            $this->body = iconv($this->data_charset, $this->email_charset, $this->body);
        }

        $headers  = "MIME-Version: 1.0;$EOL";
        $headers .= "From: {$from}$EOL";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";

        $multipart  = "--$boundary$EOL";
        $multipart .= "Content-Type: text/html; charset={$this->email_charset}$EOL";
        $multipart .= "Content-Transfer-Encoding: base64$EOL";
        $multipart .= $EOL;
        $multipart .= chunk_split(base64_encode($this->body));

        if (count($this->attaches) > 0)
        {
            foreach($this->attaches as $path => $name)
            {
                if (file_exists($path) && is_file($path)) 
                {
                    $fp = fopen($path, "rb");
                    $file = fread($fp, filesize($path));
                    fclose($fp);

                    $multipart .=  "$EOL--$boundary$EOL";
                    $multipart .= "Content-Type: application/octet-stream; name=\"$name\"$EOL";
                    $multipart .= "Content-Transfer-Encoding: base64$EOL";
                    $multipart .= "Content-Disposition: attachment; filename=\"$name\"$EOL";
                    $multipart .= $EOL;
                    $multipart .= chunk_split(base64_encode($file));
               }
            }
        }

        $multipart .= "$EOL--$boundary--$EOL";

        if (count($this->email_list) > 0)
        {
            $send_all_success = true;
            $fail_count = $success_count = 0;

            for($i = 0; $i < count($this->email_list); $i++)
            {
                $name_to = $this->email_list[$i]['name'];
                $mail_to = $this->email_list[$i]['email'];

                //если не валидная почта - пропускаем итерацию
                if ( ! $this->_isEmail($mail_to) ){
                    $this->email_list[$i]['status'] = self::STATUS_FAIL; 
                    $send_all_success = false;
                    $fail_count++;
                    continue;
                }

                $to  = $this->_mimeHeaderEncode($name_to, $this->data_charset, $this->email_charset);
                $to .= " <" . $mail_to . ">";          

                $status = mail($to, $subject, $multipart, $headers); 

                if ($status === true) {
                    $this->email_list[$i]['status'] = self::STATUS_SUCCESS; 
                    $success_count ++;
                }
                else {
                    $this->email_list[$i]['status'] = self::STATUS_FAIL; 
                    $send_all_success = false;
                    $fail_count ++;
                }
            }

            $this->send_stat['fail'] = $fail_count; 
            $this->send_stat['success'] = $success_count; 

            return $send_all_success;
        }      

        return false;    
    }


    /**
     * Проверяет корректность электронной почты
     * 
     * @param string $email
     * @return boolean
     */
    private static function _isEmail($email)
    {
        if ( ! preg_match("/^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$/i", $email)) {
            return false;
        }

        return true;
    }     
    
    
    /**
     * Добавляет адресата в список рассылки
     * 
     * @param string $email
     * @param string $name
     * @return \FW\Helpers\Email
     */
    public function to($email, $name)
    {
        $this->email_list[] = array(
            "email"  => $email, 
            "name"   => $name, 
            "status" => self::STATUS_NOT_SEND
        );

        return $this;
    }


    /**
     * Данные отправителя
     * 
     * @param string $name_from
     * @param string $email_from
     * @return \FW\Helpers\Email
     */
    public function from($name_from, $email_from)
    {
        $this->name_from = $name_from;
        $this->email_from = $email_from;
        return $this;
    }


    /**
     * Тема письма
     * 
     * @param string $subject
     * @return \FW\Helpers\Email
     */
    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }  


    /**
     * Тело письма
     * 
     * @param string $body
     * @return \FW\Helpers\Email
     */
    public function body($body)
    {
        $this->body = $body;
        return $this;
    }  


    /**
     * Добавить вложение
     * 
     * @param string $name              Название файла в письме
     * @param string $path              Полный путь к файлу
     * @return \FW\Helpers\Email
     */
    public function addAttach($name, $path)
    {
        $this->attaches[$path] = $name;
        return $this;
    }


    /**
     * Инициализация
     */
    public function __construct() 
    {
        
    }


    /**
     * Отправить почту
     * 
     * @return boolean
     */
    public function sendMail()
    {
        $this->send_stat['total'] = count($this->email_list);
        return $this->sendMailWithAttaches();
    }


    /**
     * Получение списка получателей
     * 
     * @return array
     */
    public function getEmailList()
    {
        return $this->email_list;
    }


    /**
     * Получение статистики по отправке
     * 
     * @return array
     */
    public function getSendStats()
    {
        return $this->send_stat;
    }


    /**
     * Формирование HTML лога
     * 
     * @return boolean|string
     */
    public function getHtmlLog()
    {
        if (count($this->email_list) == 0) {
            return false;
        }

        $log  = "<h2>Лог отправки почты ".date("d.m.Y H:i:s")."</h2>";
        $log .= "Всего писем в списке: <strong>".$this->send_stat['total']."</strong><br>";
        $log .= "Успешно отправлены: <strong style='color:#009E00'>".$this->send_stat['success']."</strong><br>";
        $log .= "Ошибки отправки: <strong style='color:#f00'>".$this->send_stat['fail']."</strong><br><br>";

        $log .= "<table>";
        foreach ($this->email_list as $i)
        {
            if ($i['status'] == self::STATUS_SUCCESS)  { $status = "<span style='color:#009E00'>Успешно</span>"; }
            if ($i['status'] == self::STATUS_FAIL)     { $status = "<span style='color:#f00'>Ошибка</span>"; }
            if ($i['status'] == self::STATUS_NOT_SEND) { $status = "<span style='color:#00f'>Не отправлено</span>"; }

            $log .= "<tr><td style='padding-right:30px'>";
            $log .= $i['email'];
            $log .= "</td><td>".$status."</td>";
            $log .= "</td></tr>";
        }
        $log .= "</table>";

        return $log;
    }


 
  

}