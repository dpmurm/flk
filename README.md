# flk

**Заливаем дампы таблиц в базу:** 

Используемое свободное ПО:
- Веб-сервер Apache 2.6
- PHP 5.6
- База данных MariaDB 10.3 (https://mariadb.com/downloads/)
- Toad for Mysql (https://mariadb.com/kb/en/library/toad-for-mysql-80/)
или HeidiSQL (https://www.heidisql.com/download.php)

Дамп структуры базы в файле flk_egrn_all_tables.sql
Заполняем пару таблиц resheniya.sql и vid_doc.sql
Заполняем таблицу kad_rayon по своим районам со своими отделами по территориальной принадлежности

**Стартовая страница:** 
- index.php         список протоколов

**После загрузки структуры**

Создан Веб-интерфейс загрузки файлов протоколов в EXCEL формате в БД (спасибо Курган)
![](/pict/protokol_upload.jpg)


**Развитие проекта** 

Cледите за закладкой COMMITS (нормально отображается в браузере Chrome)

**Скриншоты работы системы**

Стартовая страница
![](/pict/index.jpg)

Статистика по протоколам
![](/pict/index_stat.jpg)

Процент прохождения сведений
![](/pict/procent.jpg)

Ход исправления ошибок
![](/pict/hod.jpg)

Статистика по видам ошибок
![](/pict/vid_osh.jpg)

Список записей протокола для исправления
![](/pict/list_records.jpg)

Запись об ошибке и форма внесения информации об исправлении
![](/pict/record_work.jpg)