-- --------------------------------------------------------
-- Хост:                         10.51.118.210
-- Версия сервера:               5.5.50-MariaDB - mariadb.org binary distribution
-- Операционная система:         Win64
-- HeidiSQL Версия:              9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных flk_egrn
DROP DATABASE IF EXISTS `flk_egrn`;
CREATE DATABASE IF NOT EXISTS `flk_egrn` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `flk_egrn`;

-- Дамп структуры для таблица flk_egrn.kad_rayon
DROP TABLE IF EXISTS `kad_rayon`;
CREATE TABLE IF NOT EXISTS `kad_rayon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(5) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '0',
  `otdel_name` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='Информация о территориальной принадлежности кадастровых районов отделам';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.protokol_export
DROP TABLE IF EXISTS `protokol_export`;
CREATE TABLE IF NOT EXISTS `protokol_export` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Year` year(4) DEFAULT NULL,
  `number` int(11) NOT NULL DEFAULT '0',
  `period_start` date DEFAULT NULL,
  `period_stop` date DEFAULT NULL,
  `date` date DEFAULT NULL,
  `date_update` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='Список протоколов для передачи в ФНС';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.protokol_file
DROP TABLE IF EXISTS `protokol_file`;
CREATE TABLE IF NOT EXISTS `protokol_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vid_object` varchar(50) DEFAULT NULL COMMENT 'ЗУ, ОКС, ПИК',
  `file_name_excel` varchar(100) NOT NULL DEFAULT '0',
  `file_name_xml` varchar(100) DEFAULT '0',
  `protokol_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `FK_protokol_file_protokol_export` (`protokol_id`),
  CONSTRAINT `FK_protokol_file_protokol_export` FOREIGN KEY (`protokol_id`) REFERENCES `protokol_export` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COMMENT='Соответствие протокола и его файлов';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.record_list
DROP TABLE IF EXISTS `record_list`;
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
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61125 DEFAULT CHARSET=utf8 COMMENT='Записи протокола';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.record_notes
DROP TABLE IF EXISTS `record_notes`;
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
) ENGINE=InnoDB AUTO_INCREMENT=4433 DEFAULT CHARSET=utf8 COMMENT='Информация об исправлениях';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.resheniya
DROP TABLE IF EXISTS `resheniya`;
CREATE TABLE IF NOT EXISTS `resheniya` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Решения по исправления ошибок ФЛК';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица flk_egrn.vid_dok
DROP TABLE IF EXISTS `vid_dok`;
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
