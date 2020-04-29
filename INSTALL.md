# flk
## Настройка

**Используемое свободное ПО**

Если ставите под виндой то профиль пользователя должен содержать ТОЛЬКО латинские буквы
- Веб-сервер Apache 2.4 (http://httpd.apache.org/download.cgi#apache24)
- PHP 5.6 (https://www.php.net/archive/2019.php#id2019-01-10-4)
  в настройках php.ini
  Maximum execution time - 3000 seconds,
  upload_max_filesize 20M
- База данных MariaDB 10.3 (https://mariadb.com/downloads/)
 кодировка базы utf8_general_ci
- Toad for Mysql (https://mariadb.com/kb/en/library/toad-for-mysql-80/)
или HeidiSQL (https://www.heidisql.com/download.php)
- PHP библиотека PHPExcel (папку PHPExcel и файл PHPExcel.php распаковываем в папку PHPExcel проекта, архив с библиотекой размещен в корне проекта)

**Заливаем дампы таблиц в базу:** 

- Дамп структуры базы в файле flk_egrn_all_tables.sql
- Заполняем пару таблиц resheniya.sql и vid_doc.sql
- Заполняем таблицу kad_rayon по своим районам со своими отделами по территориальной принадлежности и номер региона

**Настраиваем конфиг**

- открываем config.php и настраиваем, читая комментарии

**Стартовые страница:** 
- index.php         список протоколов ФЛК 1 уровня
- index_fns.php         список протоколов ФЛК 2 уровня

**Развитие проекта** 
Cледите за закладкой COMMITS (нормально отображается в браузере Chrome)!