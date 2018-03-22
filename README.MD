# MFWmin (Минимальное приложение на базе фреймворка MFW v1.)

Установка
-
1) Склонировать репозиторий на сервер с предустановленными php >= 5.6
```
git clone https://github.com/penzin/MFWmin.git
```

2) Установить Composer и выполнить инициализацию
[Установка]https://getcomposer.org/download/
```
composer install
```

3) Задать актуальные занные в конфиге:
файл /config/global.php или создать переопределяющий его /config/local.php


4) Настроить веб-сервер (apache/nginx/..) таким образом, чтобы корневая директория сайта была /www/
Если необходимо, отредактировать файл hosts.

Пример v-хоста для xampp:
```
#MFWmin
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/mfwmin/www"
    ServerName mfw
</VirtualHost>
```

Строка в Hosts:
```
127.0.0.1 mfw
```

После этого приложение будет доступно по адресу http://mfw/

