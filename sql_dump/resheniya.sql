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
CREATE DATABASE IF NOT EXISTS `flk_egrn` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `flk_egrn`;

-- Дамп структуры для таблица flk_egrn.resheniya
CREATE TABLE IF NOT EXISTS `resheniya` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Решения по исправления ошибок ФЛК';

-- Дамп данных таблицы flk_egrn.resheniya: ~5 rows (приблизительно)
DELETE FROM `resheniya`;
/*!40000 ALTER TABLE `resheniya` DISABLE KEYS */;
INSERT INTO `resheniya` (`id`, `name`) VALUES
	(0, 'Не обработано'),
	(1, 'В работе'),
	(2, 'Исправлена'),
	(3, 'Невозможно исправить'),
	(4, 'Не обнаружена');
/*!40000 ALTER TABLE `resheniya` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
