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

-- Дамп структуры для таблица flk_egrn.kad_rayon
CREATE TABLE IF NOT EXISTS `kad_rayon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region` varchar(2) DEFAULT NULL COMMENT 'Кадастровый округ, Мурманск -51',
  `number` varchar(5) NOT NULL DEFAULT '0' COMMENT 'Номер кадастрового района (00, 01, 02, 03 и т.д.)',
  `name` varchar(100) NOT NULL DEFAULT '0' COMMENT 'Наименование кадастрового района',
  `otdel_name` varchar(100) NOT NULL DEFAULT '0' COMMENT 'Наименование отдела по территориальной принадлежности',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='Информация о территориальной принадлежности кадастровых районов отделам';

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
