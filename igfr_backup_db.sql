-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: localhost    Database: igfr_production
-- ------------------------------------------------------
-- Server version	8.0.36-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auth_role`
--

DROP TABLE IF EXISTS `auth_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `internal` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_role`
--

LOCK TABLES `auth_role` WRITE;
/*!40000 ALTER TABLE `auth_role` DISABLE KEYS */;
INSERT INTO `auth_role` VALUES (1,'Admin',1);
/*!40000 ALTER TABLE `auth_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `county`
--

DROP TABLE IF EXISTS `county`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `county` (
  `CountyId` int NOT NULL,
  `CountyName` varchar(50) DEFAULT NULL,
  `CountyCode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`CountyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `county`
--

LOCK TABLES `county` WRITE;
/*!40000 ALTER TABLE `county` DISABLE KEYS */;
INSERT INTO `county` VALUES (1,'MOMBASA','NULL\r'),(2,'KWALE','NULL\r'),(3,'KILIFI','NULL\r'),(4,'TANA RIVER','NULL\r'),(5,'LAMU','NULL\r'),(6,'TAITA TAVETA','NULL\r'),(7,'GARISSA','NULL\r'),(8,'WAJIR','NULL\r'),(9,'MANDERA','NULL\r'),(10,'MARSABIT','NULL\r'),(11,'ISIOLO','NULL\r'),(12,'MERU','NULL\r'),(13,'THARAKA NITHI','NULL\r'),(14,'EMBU','NULL\r'),(15,'KITUI','NULL\r'),(16,'MACHAKOS','NULL\r'),(17,'MAKUENI','NULL\r'),(18,'NYANDARUA','NULL\r'),(19,'NYERI','NULL\r'),(20,'KIRINYAGA','NULL\r'),(21,'MURANGA','NULL\r'),(22,'KIAMBU','NULL\r'),(23,'TURKANA','NULL\r'),(24,'WEST POKOT','NULL\r'),(25,'SAMBURU','NULL\r'),(26,'TRANS-NZOIA','NULL\r'),(27,'UASIN GISHU','NULL\r'),(28,'ELGEYO MARAKWET','NULL\r'),(29,'NANDI','NULL\r'),(30,'BARINGO','NULL\r'),(31,'LAIKIPIA','NULL\r'),(32,'NAKURU','NULL\r'),(33,'NAROK','NULL\r'),(34,'KAJIADO','NULL\r'),(35,'KERICHO','NULL\r'),(36,'BOMET','NULL\r'),(37,'KAKAMEGA','NULL\r'),(38,'VIHIGA','NULL\r'),(39,'BUNGOMA','NULL\r'),(40,'BUSIA','NULL\r'),(41,'SIAYA','NULL\r'),(42,'KISUMU','NULL\r'),(43,'HOMABAY','NULL\r'),(44,'MIGORI','NULL\r'),(45,'KISII','NULL\r'),(46,'NYAMIRA','NULL\r'),(47,'NAIROBI','NULL\r');
/*!40000 ALTER TABLE `county` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_library`
--

DROP TABLE IF EXISTS `document_library`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_library` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` bigint unsigned NOT NULL,
  `financial_year` bigint unsigned NOT NULL,
  `document_upload_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `upload_date` datetime NOT NULL,
  `uploaded_by` int NOT NULL,
  `publish_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unpublished',
  `published_date` datetime DEFAULT NULL,
  `published_by` int DEFAULT NULL,
  `document_date` timestamp NULL DEFAULT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_libraries_document_name_index` (`document_name`),
  KEY `document_libraries_document_type_index` (`document_type`),
  KEY `document_libraries_document_upload_path_index` (`document_upload_path`),
  KEY `financial_year` (`financial_year`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `document_library_ibfk_1` FOREIGN KEY (`financial_year`) REFERENCES `financial_year` (`id`),
  CONSTRAINT `document_library_ibfk_2` FOREIGN KEY (`document_type`) REFERENCES `document_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_library`
--

LOCK TABLES `document_library` WRITE;
/*!40000 ALTER TABLE `document_library` DISABLE KEYS */;
INSERT INTO `document_library` VALUES (13,'Public statement',17,6,'uploads/documents/doc-0.12198000 1712391599.doc','','2024-04-06 00:00:00',10,'Unpublished',NULL,NULL,NULL,NULL,NULL,NULL),(14,'Division of Revenue Act 2013',13,7,'uploads/documents/doc-0.31375500 1712392626.pdf','Division of Revenue IGFR','2024-04-06 00:00:00',8,'Unpublished',NULL,NULL,'2013-07-02 00:00:00',NULL,NULL,NULL),(15,'Division of Revenue Act 2018',13,12,'uploads/documents/doc-0.75659300 1712393211.pdf','Division of  Revenue IGFR','2024-04-06 00:00:00',8,'Unpublished',NULL,NULL,'2018-07-02 00:00:00',NULL,NULL,NULL),(16,'Division of Revenue Act 2023',13,17,'uploads/documents/doc-0.54622800 1712394671.pdf','Division of Revenue IGFR','2024-04-06 00:00:00',8,'Unpublished',NULL,NULL,'2023-07-01 00:00:00',NULL,NULL,NULL),(17,'Division of Revenue Act 2017',13,11,'uploads/documents/doc-0.71383900 1712393582.pdf','Division of Revenue IGFR','2024-04-06 00:00:00',9,'Unpublished',NULL,NULL,'2018-07-01 00:00:00',NULL,NULL,NULL),(18,'Division of Revenue Act 2021',13,15,'uploads/documents/doc-0.99780200 1712394184.pdf','Division of Revenue IFRD','2024-04-06 00:00:00',10,'Unpublished',NULL,NULL,'2021-07-01 00:00:00',NULL,NULL,NULL),(19,'Division of Revenue Act  2019',13,13,'uploads/documents/doc-0.12024000 1712393814.pdf','','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2019-07-01 00:00:00',NULL,NULL,NULL),(20,'Division of Revenue Act 2020',13,14,'uploads/documents/doc-0.36747600 1712393848.pdf','Division of  Revenue IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2020-07-01 00:00:00',NULL,NULL,NULL),(21,'2023 BROP',14,6,'uploads/documents/doc-0.29368500 1712393892.pdf','safsdfd','2024-04-06 00:00:00',1,'Unpublished',NULL,NULL,'2024-04-05 00:00:00',NULL,NULL,NULL),(22,'Division of Revenue Act 2022',13,16,'uploads/documents/doc-0.01279600 1712393922.pdf','Division of Revenue IGFR','2024-04-06 00:00:00',9,'Unpublished',NULL,NULL,'2023-07-01 00:00:00',NULL,NULL,NULL),(23,'Division of Revenue Act  2014',13,8,'uploads/documents/doc-0.20863300 1712394091.pdf','','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2014-07-01 00:00:00',NULL,NULL,NULL),(24,'Division of Revenue Act 2016',13,10,'uploads/documents/doc-0.27454200 1712394106.pdf','Division of Revenue Act IGFR','2024-04-06 00:00:00',10,'Unpublished',NULL,NULL,'2016-07-01 00:00:00',NULL,NULL,NULL),(25,'County Allocation of Revenue Act 2014',14,8,'uploads/documents/doc-0.25227500 1712395869.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2014-07-01 00:00:00',NULL,NULL,NULL),(26,'County Allocation of Revenue Act 2013',14,7,'uploads/documents/doc-0.90203300 1712395878.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',8,'Unpublished',NULL,NULL,'2013-07-01 00:00:00',NULL,NULL,NULL),(27,'County Allocation of Revenue Act 2017',14,11,'uploads/documents/doc-0.48893400 1712396576.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',9,'Unpublished',NULL,NULL,'2017-07-01 00:00:00',NULL,NULL,NULL),(28,'County Allocation of Revenue Act 2016',14,10,'uploads/documents/doc-0.38674300 1712395901.pdf','County Allocation of Revenue Act IGFR','2024-04-06 00:00:00',10,'Unpublished',NULL,NULL,'2016-07-01 00:00:00',NULL,NULL,NULL),(29,'County Allocation of Revenue Act 2018',14,12,'uploads/documents/doc-0.83343400 1712395999.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',8,'Unpublished',NULL,NULL,'2018-07-01 00:00:00',NULL,NULL,NULL),(30,'County Allocation of Revenue Act 2019',14,13,'uploads/documents/doc-0.97418700 1712396050.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2019-07-01 00:00:00',NULL,NULL,NULL),(31,'County Allocation of Revenue Act 2021',14,15,'uploads/documents/doc-0.82918800 1712396109.pdf','County Allocation of Revenue Act IGFR','2024-04-06 00:00:00',10,'Unpublished',NULL,NULL,'2021-07-01 00:00:00',NULL,NULL,NULL),(32,'County Allocation of Revenue Act 2023',14,17,'uploads/documents/doc-0.97611800 1712396111.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',8,'Unpublished',NULL,NULL,'2023-07-03 00:00:00',NULL,NULL,NULL),(33,'County Allocation of Revenue Act 2022',14,16,'uploads/documents/doc-0.33145500 1712396628.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',9,'Unpublished',NULL,NULL,'2022-07-01 00:00:00',NULL,NULL,NULL),(34,'County Allocation of Revenue Act 2015',14,9,'uploads/documents/doc-0.96083400 1712396219.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2015-07-01 00:00:00',NULL,NULL,NULL),(35,'County Allocation of Revenue Act 2020',14,14,'uploads/documents/doc-0.83153600 1712396331.pdf','County Allocation of Revenue IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2020-07-01 00:00:00',NULL,NULL,NULL),(36,'County Government Additional Allocations Act 2022',15,14,'uploads/documents/doc-0.10145900 1712396614.pdf','County Government Additional Allocations','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2022-04-01 00:00:00',NULL,NULL,NULL),(37,'County Government Additional Allocations Act 2022',15,16,'uploads/documents/doc-0.93036100 1712396836.pdf','County Government Additional Allocations IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2022-12-12 00:00:00',NULL,NULL,NULL),(38,'County Government Additional Allocations Act 2024',15,17,'uploads/documents/doc-0.32678900 1712397037.pdf','County Government Additional Allocations IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2024-03-04 00:00:00',NULL,NULL,NULL),(39,'Public Statement',17,15,'uploads/documents/doc-0.77580200 1712397293.doc','Resources to County Governments from 2013 to 2022','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2022-09-12 00:00:00',NULL,NULL,NULL),(40,'2ND Homabay Investment Conference',18,17,'uploads/documents/doc-0.02085300 1712398699.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2024-02-21 00:00:00',NULL,NULL,NULL),(41,'Amendment of Sections 129, 131 and 133 of PFMA',18,17,'uploads/documents/doc-0.33313100 1712399117.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2024-02-14 00:00:00',NULL,NULL,NULL),(42,'Treasury Circular',18,13,'uploads/documents/doc-0.83450100 1712399227.pdf','Circular IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2019-10-22 00:00:00',NULL,NULL,NULL),(43,'Approval of Equilization Fund County Project Proposal- Nandi County',18,17,'uploads/documents/doc-0.47850100 1712399311.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2024-03-04 00:00:00',NULL,NULL,NULL),(44,'Treasury Circular',18,13,'uploads/documents/doc-0.74559300 1712399324.pdf','Circular IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2019-11-19 00:00:00',NULL,NULL,NULL),(45,'Treasury Circular',18,11,'uploads/documents/doc-0.89293900 1712399400.pdf','Circular IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2017-08-29 00:00:00',NULL,NULL,NULL),(46,'Treasury Circular',18,13,'uploads/documents/doc-0.97546900 1712399489.pdf','Circular IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2019-12-05 00:00:00',NULL,NULL,NULL),(47,'Treasury Circular',18,13,'uploads/documents/doc-0.08181500 1712399571.pdf','Circular IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2019-12-23 00:00:00',NULL,NULL,NULL),(48,'Treasury Circular',18,14,'uploads/documents/doc-0.98159200 1712399643.pdf','Circular IGFR','2024-04-06 00:00:00',11,'Unpublished',NULL,NULL,'2021-03-15 00:00:00',NULL,NULL,NULL),(49,'Circular On Financing Framework on Conditional Grants No. 6 of 2020',18,13,'uploads/documents/doc-0.59822500 1712403592.pdf','Circular IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2020-04-07 00:00:00',NULL,NULL,NULL),(50,'Construction of Tharaka Nithi County Assembly Chambers ',18,17,'uploads/documents/doc-0.17178200 1712403950.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2023-07-27 00:00:00',NULL,NULL,NULL),(51,'Court Case Meru- Mr. Michel Dechaufour',18,17,'uploads/documents/doc-0.45299800 1712404082.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2024-01-17 00:00:00',NULL,NULL,NULL),(52,'Declassification of Igamba-Ng\'ombe County Tharaka Nithi',18,17,'uploads/documents/doc-0.20530400 1712404467.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2023-06-09 00:00:00',NULL,NULL,NULL),(53,'Equilization Fund project proposal- West Pokot',18,17,'uploads/documents/doc-0.42471500 1712405497.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2024-03-04 00:00:00',NULL,NULL,NULL),(54,'Equilization Fund Appropriation Act 2023',18,17,'uploads/documents/doc-0.27505500 1712406096.pdf','Equalization Fund Appropriations ACT 2023 IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2023-06-30 00:00:00',NULL,NULL,NULL),(55,'Equilization Fund project proposal- Turkana County',18,17,'uploads/documents/doc-0.21009000 1712408181.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2024-02-08 00:00:00',NULL,NULL,NULL),(56,'Equilization Fund project proposal- Busia County',18,17,'uploads/documents/doc-0.69851300 1712408340.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2024-02-05 00:00:00',NULL,NULL,NULL),(57,'Equilization Fund project proposal- Homabay County',18,17,'uploads/documents/doc-0.54952300 1712408499.pdf','Letter IGFR','2024-04-06 00:00:00',13,'Unpublished',NULL,NULL,'2024-02-12 00:00:00',NULL,NULL,NULL);
/*!40000 ALTER TABLE `document_library` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_type`
--

DROP TABLE IF EXISTS `document_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `applicable_to` enum('National Govt','County') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'National Govt',
  PRIMARY KEY (`id`),
  KEY `budget_document_types_document_type_index` (`document_type`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_type`
--

LOCK TABLES `document_type` WRITE;
/*!40000 ALTER TABLE `document_type` DISABLE KEYS */;
INSERT INTO `document_type` VALUES (13,'Division of Revenue Act',0,NULL,NULL,NULL,1,'National Govt'),(14,'County Allocation of Revenue Act',0,NULL,NULL,NULL,1,'National Govt'),(15,'County Government Additional Allocation Act',0,NULL,NULL,NULL,10,'National Govt'),(16,'Disbursement Schedules',0,NULL,NULL,NULL,9,'National Govt'),(17,'Public Statement',0,NULL,NULL,NULL,8,'National Govt'),(18,'Circulars and Advisories',0,NULL,NULL,NULL,11,'National Govt');
/*!40000 ALTER TABLE `document_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `external_entity`
--

DROP TABLE IF EXISTS `external_entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `external_entity` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entity_name` varchar(150) NOT NULL,
  `type` enum('Government MDA','Non-Governmental') DEFAULT NULL,
  `parent_mda` int unsigned DEFAULT NULL,
  `po_box` varchar(400) DEFAULT NULL,
  `physical_address` varchar(400) DEFAULT NULL,
  `added_by` int DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `external_entity`
--

LOCK TABLES `external_entity` WRITE;
/*!40000 ALTER TABLE `external_entity` DISABLE KEYS */;
INSERT INTO `external_entity` VALUES (1,'Commission on Revenue Allocation','Government MDA',NULL,'00100','GPO',1,'2021-02-24 00:00:00'),(2,'Ministry of Devolution','Government MDA',NULL,'233i','GPO',1,'2024-04-05 11:58:50'),(3,'Parliament','Non-Governmental',NULL,'23030','safaricom house, Westlands',1,'2024-04-05 12:04:22'),(4,'Office of Controller of Budget','Government MDA',NULL,'35616 00100, ','Nairobi',10,'2024-04-06 08:10:03'),(5,'Intergovernmental Relations Technical Committee','Government MDA',NULL,'','',8,'2024-04-06 08:10:32'),(6,'County Governments','Government MDA',NULL,'','',8,'2024-04-06 08:12:52'),(7,'Council of Governors','Government MDA',NULL,'','',8,'2024-04-06 08:13:34'),(8,'Senate','Government MDA',NULL,'','',11,'2024-04-06 08:15:12'),(9,'Budget Supplies Department','Government MDA',NULL,'','',8,'2024-04-06 08:15:36'),(10,'Intergovernmental Budget Economic Council','Government MDA',NULL,'0009988','Annex',13,'2024-04-06 08:16:33');
/*!40000 ALTER TABLE `external_entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_year`
--

DROP TABLE IF EXISTS `financial_year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `financial_year` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `financial_year` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_year` (`financial_year`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_year`
--

LOCK TABLES `financial_year` WRITE;
/*!40000 ALTER TABLE `financial_year` DISABLE KEYS */;
INSERT INTO `financial_year` VALUES (6,'2024-04-04','2024-04-04','2028/29',0,NULL,NULL,NULL),(7,'2013-07-01','2014-06-30','2013/14',1,NULL,NULL,NULL),(8,'2014-07-01','2015-06-30','2014/15',1,NULL,NULL,NULL),(9,'2015-07-01','2016-06-30','2015/16',1,NULL,NULL,NULL),(10,'2016-07-01','2017-06-30','2016/17',0,NULL,NULL,NULL),(11,'2017-07-01','2018-06-30','2017/18',1,NULL,NULL,NULL),(12,'2018-07-01','2019-06-30','2018/19',1,NULL,NULL,NULL),(13,'2019-07-01','2020-06-30','2019/20',1,NULL,NULL,NULL),(14,'2020-07-01','2021-06-30','2020/21',1,NULL,NULL,NULL),(15,'2021-07-01','2022-06-30','2021/22',1,NULL,NULL,NULL),(16,'2022-07-01','2023-06-30','2022/23',1,NULL,NULL,NULL),(17,'2023-07-01','2024-06-30','2023/24',1,NULL,NULL,NULL),(18,'2024-07-01','2025-06-30','2024/25',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `financial_year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letter`
--

DROP TABLE IF EXISTS `letter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `letter` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `from_county` int DEFAULT NULL,
  `entity_id` int unsigned DEFAULT NULL,
  `letter` varchar(200) DEFAULT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'LetterWorkflow/new',
  `added_by` int NOT NULL,
  `date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `from_county` (`from_county`),
  KEY `added_by` (`added_by`),
  KEY `entity_id` (`entity_id`),
  CONSTRAINT `letter_ibfk_1` FOREIGN KEY (`from_county`) REFERENCES `county` (`CountyId`),
  CONSTRAINT `letter_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `user` (`id`),
  CONSTRAINT `letter_ibfk_3` FOREIGN KEY (`entity_id`) REFERENCES `external_entity` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letter`
--

LOCK TABLES `letter` WRITE;
/*!40000 ALTER TABLE `letter` DISABLE KEYS */;
/*!40000 ALTER TABLE `letter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letter_action`
--

DROP TABLE IF EXISTS `letter_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `letter_action` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `letter_id` int unsigned NOT NULL,
  `action_name` enum('Assign','Mark Complete') NOT NULL,
  `assigned_to` int DEFAULT NULL,
  `comment` varchar(250) NOT NULL,
  `action_by` int NOT NULL,
  `file_upload` varchar(200) DEFAULT NULL,
  `date_actioned` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `action_by` (`action_by`),
  KEY `assigned_to` (`assigned_to`),
  KEY `letter_id` (`letter_id`),
  CONSTRAINT `letter_action_ibfk_1` FOREIGN KEY (`action_by`) REFERENCES `user` (`id`),
  CONSTRAINT `letter_action_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`id`),
  CONSTRAINT `letter_action_ibfk_3` FOREIGN KEY (`letter_id`) REFERENCES `letter` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letter_action`
--

LOCK TABLES `letter_action` WRITE;
/*!40000 ALTER TABLE `letter_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `letter_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `hash` varchar(120) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset`
--

LOCK TABLES `password_reset` WRITE;
/*!40000 ALTER TABLE `password_reset` DISABLE KEYS */;
INSERT INTO `password_reset` VALUES (1,5,0,'hPzajoxFYc8spe29Mtr8ONgYR5kiq$GCvAodXBpbnsFK0MJ7U35NmOVE1S6ZHZ!2Lj6BumDK@za$SQlVXQPChygfDAitWULx034J','2024-04-04 15:24:01','2024-04-04 15:24:01'),(2,6,0,'Skx2b7eqAgQduwEUn91YjNJohfi1CHZB5GQKlFZq5YBX473chyrwIaWK!WU!IVx@0DeF$OaymupNSs2LlroPRgEnzc9MDTt0TsC8','2024-04-04 15:26:32','2024-04-04 15:26:32'),(3,9,0,'6n7whFc23XUIGeV5f!nB4i@szo10KSON!L0oKZJbrrl$wG7WsDAMt8ELJijTt2gUPWDz9NljcdSTd5AYMQ8pHPqmaRx$OX3Bay9m','2024-04-06 07:42:26','2024-04-06 07:42:26'),(4,10,0,'a5mB0uEE@UCPNfz9o3UYDrlbaIeKAq6bLy1jH5KOFd7XqdZGx7ycnMQQCD6$Lo8h4IJv!Fz@xS3vVRwpthJ1k!pgSsM4B$HeTlOw','2024-04-06 07:43:20','2024-04-06 07:43:20'),(5,11,0,'$dDB6k5hvHm73j!$T5q@Z4Xpz1jreGKT8AW4inAfysRwVkJ9QsbC8V2vp2euCfEYoH0M@3mOhcyKY1lgL6UNbtExcGdPFFiwQWJn','2024-04-06 07:44:23','2024-04-06 07:44:23'),(6,12,0,'v$05oioF8xgjZ2L5CUetYwf1yQuclA962Ty!ab@XRHb7M1SDGFhrLXOPkpks0ArHYG8Km64EVq7WgupUTxeKEnPtvmhzDdJ$ji!I','2024-04-06 07:45:58','2024-04-06 07:45:58'),(7,13,0,'My1sWr4LqBhF0mLR8TIHzsPjBgRdxHSD$YC19dlXE!AkTw3tGwfN@UY5Q@kZiUK!vaMCecme$36IzafuK2EhpNJ8ycWnu5ngV9i6','2024-04-06 07:55:37','2024-04-06 07:55:37');
/*!40000 ALTER TABLE `password_reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_audit_trail`
--

DROP TABLE IF EXISTS `tbl_audit_trail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_audit_trail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `old_value` text,
  `new_value` text,
  `action` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `field` varchar(255) NOT NULL,
  `stamp` datetime NOT NULL,
  `comments` varchar(500) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `model_id` int NOT NULL,
  `change_no` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_audit_trail_action` (`action`),
  KEY `idx_audit_trail_model` (`model`),
  KEY `idx_audit_trail_field` (`field`),
  KEY `idx_audit_trail_user_id` (`user_id`),
  KEY `idx_audit_trail_model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_audit_trail`
--

LOCK TABLES `tbl_audit_trail` WRITE;
/*!40000 ALTER TABLE `tbl_audit_trail` DISABLE KEYS */;
INSERT INTO `tbl_audit_trail` VALUES (1,'kenneth mqguae','kenneth ','CHANGE','app\\models\\User','user_names','2024-04-03 14:54:30',NULL,'1',2,NULL),(4,'peter wachira <wachirapk@gmail.com>','wachirapk@gmail.com','CHANGE','app\\models\\User','email','2024-04-05 12:49:56',NULL,'1',8,NULL);
/*!40000 ALTER TABLE `tbl_audit_trail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL,
  `user_names` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `status` enum('1','0','2') DEFAULT '0',
  `recent_passwords` varchar(500) DEFAULT NULL,
  `authKey` varchar(100) DEFAULT NULL,
  `accessToken` varchar(100) DEFAULT NULL,
  `last_password_change_date` datetime NOT NULL DEFAULT '2020-09-30 00:00:00',
  `last_login_date` datetime DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'kenmagua@gmail.com','Kenneth','$2y$10$eRk8p2ApH0.VqTNTmZFYp.U6BkTmMemL7DB7mGcGoXsIToL6FJaGS','1','$2y$10$eRk8p2ApH0.VqTNTmZFYp.U6BkTmMemL7DB7mGcGoXsIToL6FJaGS',NULL,NULL,'2020-09-30 00:00:00','2021-05-13 10:45:00','2020-02-11 08:56:00','2021-05-13 10:45:00'),(2,'kmagua@outlook.com','kenneth Magua','$2y$10$bUj5HPf8LgFxlO6s8LeNmu0j0hWLrrsjFAtK10VEVl7M/OZu73Ns6','1','$2y$10$bUj5HPf8LgFxlO6s8LeNmu0j0hWLrrsjFAtK10VEVl7M/OZu73Ns6',NULL,NULL,'2020-09-30 00:00:00',NULL,'2020-02-11 16:38:00','2024-04-03 14:55:41'),(7,'jacobmuimi@gmail.com','Jacob Muimi','$2y$10$ylZ5mlJ9owwyryMoWqtGzu8rLpY2ozSBjzNbxrGqNCEsUkTPjs3..','1','$2y$10$ylZ5mlJ9owwyryMoWqtGzu8rLpY2ozSBjzNbxrGqNCEsUkTPjs3..',NULL,NULL,'2024-04-05 12:39:49',NULL,'2024-04-05 12:39:49','2024-04-05 12:39:49'),(8,'wachirapk@gmail.com','Peter Wachira','$2y$10$0M1gG1Xv10JK4G5eqLKiRusrkPowirC9qFNbiU2jHoKiJ0tQhhZlq','1','$2y$10$0M1gG1Xv10JK4G5eqLKiRusrkPowirC9qFNbiU2jHoKiJ0tQhhZlq',NULL,NULL,'2024-04-05 12:49:11',NULL,'2024-04-05 12:49:11','2024-04-05 12:49:56'),(9,'mnnguli1974@gmail.com','Mary Nguli','$2y$10$h.kH2UpcYwcai5p8BnYxGOr30OnFDNxDj2eoc2i6G6c1F8MTIv1R2','1','$2y$10$h.kH2UpcYwcai5p8BnYxGOr30OnFDNxDj2eoc2i6G6c1F8MTIv1R2',NULL,NULL,'2024-04-06 07:42:26',NULL,'2024-04-06 07:42:26','2024-04-06 07:42:26'),(10,'toohenry80@gmail.com','Henry Too','$2y$10$91t73CYE/ycLHg/TGYojru6Z6olxTPW5ET5hfbpGuiUfjKG7Nl9/6','1','$2y$10$91t73CYE/ycLHg/TGYojru6Z6olxTPW5ET5hfbpGuiUfjKG7Nl9/6',NULL,NULL,'2024-04-06 07:43:20',NULL,'2024-04-06 07:43:20','2024-04-06 07:43:20'),(11,'jalody93@gmail.com','Jalody Cherotich','$2y$10$4VPTTlA/6lowHVagY9NJgePU4f.mCTnTKLnSUN9azC1MCSeh9Wyj2','1','$2y$10$4VPTTlA/6lowHVagY9NJgePU4f.mCTnTKLnSUN9azC1MCSeh9Wyj2',NULL,NULL,'2024-04-06 07:44:23',NULL,'2024-04-06 07:44:23','2024-04-06 07:44:23'),(12,' feroliz2017@gmail.com','Elizabeth Nzioka','$2y$10$w.UnXtFlBHnng5YcEHMlReADwiBPX2lxx7HoX52Q6xNQsBSs12otS','1','$2y$10$w.UnXtFlBHnng5YcEHMlReADwiBPX2lxx7HoX52Q6xNQsBSs12otS',NULL,NULL,'2024-04-06 07:45:58',NULL,'2024-04-06 07:45:58','2024-04-06 07:45:58'),(13,'ngangalucywanjiru@gmail.com','Lucy Nganga','$2y$10$Z6iK4iyMbz1RMLk70YhXHO6LU8Is0MExIvxDnJ2YzhZG08SAjnsrC','0','$2y$10$Z6iK4iyMbz1RMLk70YhXHO6LU8Is0MExIvxDnJ2YzhZG08SAjnsrC',NULL,NULL,'2024-04-06 07:55:37',NULL,'2024-04-06 07:55:37','2024-04-06 07:55:37');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_login_trial`
--

DROP TABLE IF EXISTS `user_login_trial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_login_trial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `number_of_trials` int DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`user_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `user_login_trial_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_login_trial`
--

LOCK TABLES `user_login_trial` WRITE;
/*!40000 ALTER TABLE `user_login_trial` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_login_trial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `auth_role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,1,1,1);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-04-06 13:18:26
