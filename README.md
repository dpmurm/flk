# flk
##Настройка

**Используемое свободное ПО**
- Веб-сервер Apache 2.4 (http://httpd.apache.org/download.cgi#apache24)
- PHP 5.6 (https://www.php.net/archive/2019.php#id2019-01-10-4)
- База данных MariaDB 10.3 (https://mariadb.com/downloads/)
- Toad for Mysql (https://mariadb.com/kb/en/library/toad-for-mysql-80/)
или HeidiSQL (https://www.heidisql.com/download.php)
- PHP библиотека PHPExcel (папку PHPExcel и файл PHPExcel.php распаковываем в папку PHPExcel проекта, архив с библиотекой размещен в корне проекта)

**Заливаем дампы таблиц в базу:** 

- Дамп структуры базы в файле flk_egrn_all_tables.sql
- Заполняем пару таблиц resheniya.sql и vid_doc.sql
- Заполняем таблицу kad_rayon по своим районам со своими отделами по территориальной принадлежности

**Настраиваем конфиг**

- открываем config.php и настраиваем, читая комментарии

**Стартовая страница:** 
- index.php         список протоколов

**После загрузки структуры**

Загружаем файлы через Веб интерфейс.

- Веб-интерфейс загрузки файлов протоколов EXCEL формата в БД (спасибо Курган)
![](/pict/protokol_upload.jpg)
- Веб-интерфейс загрузки XML файлов протокола ФЛК 2 уровня в БД (спасибо Курган)
![](/pict/protokol_upload_fns.jpg)

**Развитие проекта** 

Cледите за закладкой COMMITS (нормально отображается в браузере Chrome)!

## Использование

**Скриншоты работы системы**

- Поиск по протоколам ФЛК 1 и 2 уровня одновременно
![](/pict/search.jpg)

- Стартовая страница для исправления протоколов ФЛК 1 уровня
![](/pict/index.jpg)
- Стартовая страница для исправления протоколов ФЛК 2 уровня
![](/pict/index_fns.jpg)

- Статистика по протоколам
![](/pict/index_stat.jpg)

- Процент прохождения сведений
![](/pict/procent.jpg)

- Ход исправления ошибок протокола ФЛК 1 уровня
![](/pict/hod.jpg)
- Ход исправления ошибок протокола ФЛК 2 уровня
![](/pict/hod_fns.jpg)

- Статистика по видам ошибок протокола ФЛК 1 уровня
![](/pict/vid_osh.jpg)
- Статистика по видам ошибок протокола ФЛК 2 уровня
![](/pict/vid_osh_fns.jpg)

- Список записей протокола ФЛК 1 уровня для исправления
![](/pict/list_records.jpg)
- Список записей протокола ФЛК 2 уровня для исправления
![](/pict/list_records_fns.jpg)

- Запись об ошибке ФЛК 1 уровня и форма внесения информации об исправлении
![](/pict/record_work.jpg)
- Запись об ошибке ФЛК 2 уровня и форма внесения информации об исправлении
![](/pict/record_work_fns.jpg)

