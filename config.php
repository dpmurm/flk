<?php
date_default_timezone_set("Europe/Moscow");
//IP адреса с которых доступны ссылки на загрузку протоколов
$arr_ip_allow = array(
    '127.0.0.1',
    '10.51.114.211',
    '10.51.114.73',
    '10.51.118.217',
);

//$doc_root = $_SERVER["DOCUMENT_ROOT"]."/";
$ipaddr = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['HTTP_HOST'];
$site = basename(__DIR__);

/* ===========================================
Настройки сессий
=========================================== */

// имя сессии
$session_name = 'SFLK';
session_name($session_name);

// таймаут хранения сессии
$session_timeout = 28800;
ini_set('session.gc_maxlifetime', $session_timeout);

// Пути сессий и cooke
// ini_set('session.cookie_path', $site);
//ini_set('session.save_path', $doc_root.$site.'/sessions');
ini_set('session.save_path', 'sessions');

// Вероятность запуска GC при каждом запуске скрипта = session.gc_probability/session.gc_divisor
//ini_set('session.gc_probability', 1);
//ini_set('session.gc_divisor', 100);

/* Подключение к серверу MySQL */
$link = mysqli_connect(
    'localhost',  /* Хост, к которому мы подключаемся */
    'flk_user',       /* Имя пользователя */
    'test_parol',   /* Используемый пароль */
    'flk_egrn');     /* База данных для запросов по умолчанию */

if (!$link) {
    printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error());
    exit;
}
mysqli_set_charset($link, "utf8");

?>
