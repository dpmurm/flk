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

-- Дамп структуры для таблица flk_egrn.vid_dok
CREATE TABLE IF NOT EXISTS `vid_dok` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` int(11) DEFAULT NULL,
  `desc` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Вид сведений';

-- Дамп данных таблицы flk_egrn.vid_dok: ~11 rows (приблизительно)
DELETE FROM `vid_dok`;
/*!40000 ALTER TABLE `vid_dok` DISABLE KEYS */;
INSERT INTO `vid_dok` (`id`, `no`, `desc`) VALUES
	(1, 1, 'о возникновении (регистрации) права на объект недвижимого имущества не в связи с переходом права'),
	(2, 3, 'о прекращении права на объект недвижимого имущества без перехода к новому правообладателю'),
	(3, 4, 'о переходе права на объект недвижимого имущества'),
	(4, 5, 'об изменении характеристик объекта недвижимого имущества по причине исправления ошибки  '),
	(5, 6, 'об изменении характеристик объекта недвижимого имущества не в связи с исправлением ошибки'),
	(6, 7, 'об изменении кадастровой стоимости земельных участков вследствие проведенной государственной кадастровой оценки земель'),
	(7, 8, 'об изменении кадастровой стоимости объектов недвижимого имущества вследствие проведенной государственной кадастровой оценки объектов недвижимости'),
	(8, 9, 'годовые'),
	(9, 10, 'об ограничении права на объект недвижимого имущества'),
	(10, 11, 'о ранее возникших правах на земельный участок'),
	(11, 12, 'об отказе от права собственности, постоянного (бессрочного) пользования, пожизненного наследуемого владения на земельный участок либо об отказе от права собственности на земельную долю');
/*!40000 ALTER TABLE `vid_dok` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
