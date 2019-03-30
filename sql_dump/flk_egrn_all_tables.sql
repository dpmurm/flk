-- --------------------------------------------------------
-- Хост:                         10.51.118.210
-- Версия сервера:               5.5.50-MariaDB - mariadb.org binary distribution
-- Операционная система:         Win64
-- HeidiSQL Версия:              10.1.0.5464
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных flk_egrn
CREATE DATABASE IF NOT EXISTS `flk_egrn` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `flk_egrn`;

-- Дамп структуры для таблица flk_egrn.kad_rayon
CREATE TABLE IF NOT EXISTS `kad_rayon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(5) NOT NULL DEFAULT '0' COMMENT 'Номер кадастрового района (00, 01, 02, 03 и т.д.)',
  `name` varchar(100) NOT NULL DEFAULT '0' COMMENT 'Наименование кадастрового района',
  `otdel_name` varchar(100) NOT NULL DEFAULT '0' COMMENT 'Наименование отдела по территориальной принадлежности',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='Информация о территориальной принадлежности кадастровых районов отделам';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.protokol_export
CREATE TABLE IF NOT EXISTS `protokol_export` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Year` year(4) DEFAULT NULL COMMENT 'Год',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT 'Порядковый номер с начала года',
  `period_start` date DEFAULT NULL,
  `period_stop` date DEFAULT NULL,
  `date` date DEFAULT NULL COMMENT 'Дата отправки протокола',
  `date_update` date DEFAULT NULL COMMENT 'Дата переформирования протокола',
  `visible` int(1) DEFAULT NULL COMMENT 'Отражать или нет в списке',
  `type` varchar(1) DEFAULT NULL COMMENT 'Тип протокола: П- периодическая (в основном декадная), Г- годовая, К- корректирующая (выгрузка по списку непрошедших ФЛК после исправления), Т- тестовая (предварительная выгрузка для исправления ФЛК перед основной).',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Year_period_start_period_stop_date_type` (`Year`,`period_start`,`period_stop`,`date`,`type`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='Список протоколов для передачи в ФНС';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.protokol_file
CREATE TABLE IF NOT EXISTS `protokol_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vid_object` varchar(50) DEFAULT NULL COMMENT 'ЗУ, ОКС, ПИК',
  `file_name_excel` varchar(100) NOT NULL DEFAULT '0',
  `file_name_xml` varchar(100) DEFAULT '0',
  `protokol_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_name_excel_file_name_xml` (`file_name_excel`,`file_name_xml`),
  KEY `id` (`id`),
  KEY `FK_protokol_file_protokol_export` (`protokol_id`),
  CONSTRAINT `FK_protokol_file_protokol_export` FOREIGN KEY (`protokol_id`) REFERENCES `protokol_export` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8 COMMENT='Соответствие протокола и его файлов';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.protokol_file_fns
CREATE TABLE IF NOT EXISTS `protokol_file_fns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `insert_date` date NOT NULL,
  `idfile_fns_xml` varchar(100) NOT NULL COMMENT 'Название XML файла протокола 2 уровня',
  `file_urr_xml` varchar(100) NOT NULL COMMENT 'Название XML файла протокола 1 уровня, который отрабатывался в ФНС',
  `protokol_id` varchar(32) NOT NULL DEFAULT '0' COMMENT 'Ссылка на id PROTOKOL_EXPORT исходящего протокола',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idfile_fns_xml` (`idfile_fns_xml`),
  KEY `protokol_id` (`protokol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.record_list
CREATE TABLE IF NOT EXISTS `record_list` (
  `number_in_file` int(11) NOT NULL,
  `cad_obj_num` varchar(100) NOT NULL,
  `type_object` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `guid_doc` varchar(100) NOT NULL,
  `vid_record_for_export` int(11) NOT NULL,
  `error_text` varchar(300) NOT NULL,
  `error_path_xml` varchar(300) NOT NULL,
  `atribut_name` varchar(100) NOT NULL,
  `atribut_value` varchar(100) NOT NULL,
  `error_type` varchar(100) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `guid_doc` (`guid_doc`)
) ENGINE=InnoDB AUTO_INCREMENT=275442 DEFAULT CHARSET=utf8 COMMENT='Записи протокола';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.record_list_fns
CREATE TABLE IF NOT EXISTS `record_list_fns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error_poz` varchar(1000) NOT NULL COMMENT 'Позиция в XML файле',
  `error_value` varchar(300) NOT NULL,
  `error_code` varchar(100) NOT NULL COMMENT 'Код ошибки по системе ФНС',
  `error_text` varchar(300) NOT NULL,
  `error_id` varchar(100) NOT NULL,
  `protokol_file_fns_id` varchar(32) NOT NULL DEFAULT '0' COMMENT 'Ссылка на id таблицы PROTOKOL_FILE_FNS',
  PRIMARY KEY (`id`),
  KEY `protokol_uid` (`protokol_file_fns_id`),
  KEY `error_id` (`error_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3906 DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.record_notes
CREATE TABLE IF NOT EXISTS `record_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_list_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Ссылка на запись протокола',
  `decision_type` int(11) NOT NULL DEFAULT '0' COMMENT 'Вид решения',
  `reg_no` varchar(100) DEFAULT '0' COMMENT 'Номер заявки на техошибку',
  `text` varchar(1000) DEFAULT '0',
  `insert_date` date DEFAULT NULL,
  `update_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `record_list_id` (`record_list_id`),
  KEY `id` (`id`),
  CONSTRAINT `FK_record_notes_record_list` FOREIGN KEY (`record_list_id`) REFERENCES `record_list` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12759 DEFAULT CHARSET=utf8 COMMENT='Информация об исправлениях';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.record_notes_fns
CREATE TABLE IF NOT EXISTS `record_notes_fns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_list_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Ссылка на запись протокола',
  `decision_type` int(11) NOT NULL DEFAULT '0' COMMENT 'Вид решения',
  `reg_no` varchar(100) DEFAULT '0' COMMENT 'Номер заявки на техошибку',
  `text` varchar(1000) DEFAULT '0',
  `insert_date` date DEFAULT NULL,
  `update_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `record_list_id` (`record_list_id`),
  CONSTRAINT `record_notes_fns_ibfk_1` FOREIGN KEY (`record_list_id`) REFERENCES `record_list_fns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='Информация об исправлениях';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.resheniya
CREATE TABLE IF NOT EXISTS `resheniya` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Решения по исправления ошибок ФЛК';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.vid_dok
CREATE TABLE IF NOT EXISTS `vid_dok` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` int(11) DEFAULT NULL,
  `desc` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Вид сведений';

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
