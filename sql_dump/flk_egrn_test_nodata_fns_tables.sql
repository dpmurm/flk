-- MySQL dump 10.13  Distrib 5.1.73, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: flk_egrn_test
-- ------------------------------------------------------
-- Server version	5.1.73-0ubuntu0.10.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `protokol_export_fns`
--

DROP TABLE IF EXISTS `protokol_export_fns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `protokol_export_fns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `insert_date` date NOT NULL,
  `idfile_fns_xml` varchar(100) NOT NULL,
  `file_urr_xml` varchar(100) NOT NULL,
  `protokol_uid` varchar(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `protokol_uid` (`protokol_uid`)-- ,
  -- CONSTRAINT `protokol_export_fns_ibfk_1` FOREIGN KEY (`protokol_uid`) REFERENCES `protokol_export` (`protokol_uid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `record_list_fns`
--

DROP TABLE IF EXISTS `record_list_fns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `record_list_fns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error_id` varchar(100) NOT NULL,
  `error_text` varchar(300) NOT NULL,
  `error_value` varchar(300) NOT NULL,
  `protokol_uid` varchar(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `protokol_uid` (`protokol_uid`),
  CONSTRAINT `record_list_fns_ibfk_1` FOREIGN KEY (`protokol_uid`) REFERENCES `protokol_export_fns` (`protokol_uid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3060 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `record_notes_fns`
--

DROP TABLE IF EXISTS `record_notes_fns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `record_notes_fns` (
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Информация об исправлениях';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-01-21 11:16:33
