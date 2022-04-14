-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: localhost    Database: inforex_clear
-- ------------------------------------------------------
-- Server version	5.7.24-0ubuntu0.16.04.1

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
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activities` (
  `activity_page_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `ip_id` int(10) unsigned NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `corpus_id` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `activity_type_id` int(11) NOT NULL,
  `execution_time` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`activity_page_id`),
  KEY `ip_id` (`ip_id`),
  KEY `user_id` (`user_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `report_id` (`report_id`),
  KEY `activity_type_id` (`activity_type_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `activities_view_users`
--

DROP TABLE IF EXISTS `activities_view_users`;
/*!50001 DROP VIEW IF EXISTS `activities_view_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `activities_view_users` AS SELECT 
 1 AS `screename`,
 1 AS `COUNT(*)`,
 1 AS `last_datetime`,
 1 AS `activity_page_id`,
 1 AS `datetime`,
 1 AS `ip_id`,
 1 AS `user_id`,
 1 AS `corpus_id`,
 1 AS `report_id`,
 1 AS `activity_type_id`,
 1 AS `execution_time`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `activity_types`
--

DROP TABLE IF EXISTS `activity_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_types` (
  `activity_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  KEY `activity_type_id` (`activity_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_types`
--

LOCK TABLES `activity_types` WRITE;
/*!40000 ALTER TABLE `activity_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annotation_sets`
--

DROP TABLE IF EXISTS `annotation_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation_sets` (
  `annotation_set_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nested` tinyint(1) NOT NULL DEFAULT '0',
  `public` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`annotation_set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation_sets`
--

LOCK TABLES `annotation_sets` WRITE;
/*!40000 ALTER TABLE `annotation_sets` DISABLE KEYS */;
INSERT INTO `annotation_sets` VALUES (1,'Names','Jednostki identyfikacyjne',0,1,1);
/*!40000 ALTER TABLE `annotation_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annotation_sets_corpora`
--

DROP TABLE IF EXISTS `annotation_sets_corpora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation_sets_corpora` (
  `annotation_set_id` int(11) NOT NULL,
  `corpus_id` int(11) NOT NULL,
  PRIMARY KEY (`annotation_set_id`,`corpus_id`),
  KEY `annotation_set_id` (`annotation_set_id`),
  KEY `corpus_id` (`corpus_id`),
  CONSTRAINT `annotation_sets_corpora_ibfk_1` FOREIGN KEY (`annotation_set_id`) REFERENCES `annotation_sets` (`annotation_set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `annotation_sets_corpora_ibfk_2` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation_sets_corpora`
--

LOCK TABLES `annotation_sets_corpora` WRITE;
/*!40000 ALTER TABLE `annotation_sets_corpora` DISABLE KEYS */;
/*!40000 ALTER TABLE `annotation_sets_corpora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annotation_subsets`
--

DROP TABLE IF EXISTS `annotation_subsets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation_subsets` (
  `annotation_subset_id` int(11) NOT NULL AUTO_INCREMENT,
  `annotation_set_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`annotation_subset_id`),
  KEY `annotation_set_id` (`annotation_set_id`),
  CONSTRAINT `annotation_subsets_ibfk_1` FOREIGN KEY (`annotation_set_id`) REFERENCES `annotation_sets` (`annotation_set_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Nazwy podkategorii, na które dzielone są anotacje.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation_subsets`
--

LOCK TABLES `annotation_subsets` WRITE;
/*!40000 ALTER TABLE `annotation_subsets` DISABLE KEYS */;
INSERT INTO `annotation_subsets` VALUES (1,1,'living','Nazwy istot żywych'),(2,1,'location','Nazwy lokalizacji, obiektów geopolitycznych  i geograficznych'),(3,1,'event','Nazwy wydarzeń'),(4,1,'facility','Nazwy budowli stworzonych przez człowieka.'),(5,1,'product','Nazwy szeroko rozumianych produktów (pod względem marketingowym).'),(7,1,'organization','Nazwy instytucji, organizacji, grup ludzi.'),(8,1,'other','Nazwy własne niezaklasyfikowane do pozostałych grup.'),(44,1,'MUC',''),(52,1,'adjective','Przymiotniki pochodzące od nazw własnych'),(53,1,'numex','Nazwy powiązane z wartościami numerycznymi.');
/*!40000 ALTER TABLE `annotation_subsets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annotation_types`
--

DROP TABLE IF EXISTS `annotation_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation_types` (
  `annotation_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` int(11) NOT NULL,
  `annotation_subset_id` int(11) DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `short_description` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cross_sentence` tinyint(1) NOT NULL DEFAULT '0',
  `shortlist` int(11) DEFAULT '0',
  PRIMARY KEY (`annotation_type_id`),
  KEY `group_id` (`group_id`),
  KEY `annotation_subset_id` (`annotation_subset_id`),
  KEY `name` (`name`),
  CONSTRAINT `annotation_types_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `annotation_sets` (`annotation_set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `annotation_types_ibfk_2` FOREIGN KEY (`annotation_subset_id`) REFERENCES `annotation_subsets` (`annotation_subset_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=367 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation_types`
--

LOCK TABLES `annotation_types` WRITE;
/*!40000 ALTER TABLE `annotation_types` DISABLE KEYS */;
INSERT INTO `annotation_types` VALUES (1,'nam_loc_gpe_admin1','Nazwa obszaru wynikającego z podziału terytorialnego pierwszego poziomu, np. województwa w Polsce, landy w Niemczech, stany w USA (pisane małą lub dużą literą).',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#CEDEF4;',0,0),(2,'nam_loc_gpe_admin2','Nazwa terytorium wynikające z podziału administracyjnego, drugiego poziomu, np. powiaty w Polsce.',1,2,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#DDCEFF;',0,0),(3,'nam_loc_gpe_admin3','Nazwa terytorium wynikającego z podziału administracyjnego trzeciego poziomu, np. gminy w Polsce, township w USA.',1,2,1,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(5,'nam_liv_animal','Nazwy własne zwierząt.',1,1,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid black; background:#CACAFF;',0,0),(6,'nam_loc_astronomical','Nazwy obiektów kosmicznych.',1,2,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background: Gold;',0,0),(7,'nam_pro_award','Nazwy nagród, tytułów i orderów.',1,5,0,'','',0,0),(8,'nam_org_group_band','Nazwa zespołu muzycznego, drużyny, grupy lub innej grupy ludzi.',1,7,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px double green; background:#FFDFDF;',0,0),(9,'nam_loc_hydronym_bay','Nazwa zatoki (część zbiornika wodnego). Jeżeli nazwa zatoki zawiera słowo \'zatoka\' błędnie napisane małą literą, to i tak powinna być oznaczona całość.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(10,'nam_pro_brand','Nazwa produktów seryjnych (samochodów, produktów spożywczych, chemicznych, itp.).',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#CACAFF;',0,0),(11,'nam_loc_land_cape','Nazwa przylądka (fragment lądu). Jeżeli nazwa przylądka zawiera słowo \'przylądek\' błędnie napisany małą literą, to mimo to oznaczamy całość.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(25,'nam_loc_gpe_city','Nazwa własna miasta.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#CACAFF;',0,0),(27,'nam_org_company','Nazwy firm, zakładów, przedsiębiorstw, organizacji prowadzących działalność gospodarczą, mające formę spółki lub jednoosobowej działalności gospodarczej.',1,7,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#D6F8DE;',0,0),(28,'nam_loc_land_continent','Nazwa kontynentu.',1,2,1,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#D6F8DE;',0,0),(29,'nam_loc_gpe_conurbation','Nazwa aglomeracji.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:;',0,0),(30,'nam_loc_gpe_country','Nazwa państwa.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#EEDCC8;',0,0),(31,'nam_loc_country_region','Nazwa regionu będącego częścią kraju, który nie można zaklasyfikować jako podział administracyjny (province_nam, powiat_nam, gmina_nam), historyczny (historical_region_nam) ani inny zdefiniowany.',1,2,1,NULL,NULL,0,0),(32,'nam_oth_currency','Nazwy walut, zarówno słowne, skrócone i symboliczne.',1,8,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(33,'nam_loc_gpe_district','Nazwa dzielnicy miasta.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#C0E7F3;',0,0),(34,'nam_pro_title_document','Nazwa dokumentu urzędowego, ustawy, rozporządzenia.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#EEDCC8;',0,0),(38,'nam_eve_human','Nazwy wydarzeń sportowyh, muzycznych, wystaw, pokazów, koncertów, projektów, itp.',1,3,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#FFFAA9;',0,0),(40,'nam_fac_goe','Nazwa lokalu, takiego jak kino, restauracja, sklep, itp.',1,4,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#F5CAFF;',0,0),(60,'nam_loc_historical_region','Nazwa terenu historycznego.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:;',0,0),(62,'nam_org_institution','Nazwy instytucji rozumianych jako elementy, organy organizacji.',1,7,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#DDCEFF;',0,0),(63,'nam_loc_land_island','Nazwa wyspy.',1,2,1,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(64,'nam_loc_hydronym_lagoon','Nazwa zalewu (zbiornik wodny).',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(65,'nam_loc_hydronym_lake','Nazwa jeziora, stawu, oczka wodnego lub innego naturalnego zbiornika wodnego.',1,2,1,NULL,NULL,0,0),(66,'nam_oth_license','Nazwa licencji lub sposobu dystrybucji oprogramowania.',1,8,0,NULL,NULL,0,0),(117,'nam_pro_media','Nazwy stacji telewizyjnych i radiowych.',1,5,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:Violet;',0,0),(118,'nam_loc_land_mountain','Nazwa łańcuchów górskich, przełęczy i szczytów.',1,2,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#FFFAA9;',0,0),(119,'nam_oth','Nazwy własne niezaklasyfikowane do pozostałych grup (w przypadku braku bardziej szczegółowego typu anotacji w obrębie nam_oth).',1,8,0,'','background: lightgreen; border: 1px dashed red; border-bottom: 2px solid red;',0,0),(120,'nam_org_nation','Nazwy narodów i plemion (w tym nazwy przedstawicieli).',1,7,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid blue; background:#EEDCC8;',0,0),(125,'nam_loc_hydronym_ocean','Nazwa oceanu. Jeżeli nazwa oceanu zawiera słowo \'ocean\' błędnie napisane małą literą, to mimo to oznaczamy całość.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(128,'nam_org_organization','Nazwy organizacji społecznych, politycznych, militarnych i ekonomicznych, nazwy fundacji, zrzeszeń itp.',1,7,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background: GreenYellow;',0,0),(129,'nam_org_organization_sub','Nazwa wyróżnionego elementu organizacji wynikającego z pewnego podziału tej organizacji, np. podział państw w UE ze względu na datę przystąpienia UE-15, EU-25.',1,7,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; ',0,0),(130,'nam_fac_park','Nazwa parków miejskich, krajobrazowych i przyrodniczych.',1,4,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#F5CAFF;',0,0),(131,'nam_loc_land_peninsula','Nazwa półwyspu. Jeżeli nazwa półwyspu zawiera słowo \'półwysep\', a jest błędnie napisany małą literą, to i tak należy anotować całość.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(132,'nam_pro_media_periodic','Nazwa gazety, czasopisma, magazynu.',1,5,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#CEDEF4;',0,0),(134,'nam_liv_person_add','Dodatkowa nazwa osobowa, taka jak: przydomek, przezwisko, pseudonim, ksywka, alias, epitet, nazwa zastępcza.',1,1,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid blue; background:#FFDFDF;',0,0),(135,'nam_adj_person','Przymiotniki pochodzące od nazw osobowych.',1,52,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid blue; background:white;',0,0),(136,'nam_liv_person_first','Imiona, nazwy powszechnie uznawane za imiona. Pierwsze i drugie imię znakowane są osobno.',1,1,1,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid blue; background:#CACAFF;',0,0),(137,'nam_org_group','Nieoficjalna grupa osób, np. grupy na portalach społecznościowych.',1,7,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid blue; background:#BDFFEA;',0,0),(138,'nam_liv_person_last','Nazwiska. Nazwiska założone z dwóch członów (nazwisko panieńskie) znakowane są jako dwie nazwy.',1,1,1,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid blue; background:#D6F8DE;',0,0),(139,'nam_liv_person','Pełna nazwa osobowa składająca się z imion, nazwisk, inicjałów i słów funkcyjnych.',1,1,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid blue; background: #FFFAA9;',0,0),(140,'nam_liv_plant','Nazwy własne roślin.',1,1,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid black; background:#FFDFDF;',0,0),(141,'nam_org_political_party','Nazwa partii politycznej.',1,7,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#BDFFEA;',0,0),(146,'nam_loc_land_region','Nazwy regionów mniejsze niż kontynenty, ale większe niż państwa.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#BDFFEA;',0,0),(147,'nam_loc_hydronym_river','Nazwa rzeki.',1,2,1,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(149,'nam_fac_road','Nazwa ulicy, drogi, autostrady itp.',1,4,0,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#FFFFC8;',0,0),(150,'nam_loc_land_sandspit','Nazwa mierzei. Jeżeli nazwa mierzei zawiera słowo \'mierzeja\', a jest napisana błędnie małą literą, to mimo to oznaczamy całość.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(151,'nam_loc_hydronym_sea','Nazwa morza.',1,2,1,NULL,'border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:white;',0,0),(152,'nam_pro_software','Nazwy programów i gier komputerowych.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; ',0,0),(156,'nam_fac_square','Nazwa placu lub rynku.',1,4,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#ECD9D9;',0,0),(157,'nam_loc_gpe_subdivision','Nazwa osiedla.',1,2,0,'','padding: 0 1px; border-bottom: 2px solid purple; ',0,0),(158,'nam_fac_system','Nazwy konkretnych instancji fizycznych systemów posiadających własną infrastrukturę i realizujących określone cele (np. systemy informatyczne, ostrzegawcze, itp.). ',1,4,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:white;',0,0),(159,'nam_oth_tech','Nazwy technologii oraz języków programowania.',1,8,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#EE8262;',0,0),(166,'nam_pro_title','Nazwa książki, płyty, obrazu, piosenki i innego utworu artystycznego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(167,'nam_loc','Nazwy lokalizacji, obiektów geopolitycznych i geograficznych (w przypadku braku bardziej szczegółowego typu anotacji).',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#C0E7F3;',0,0),(169,'nam_pro_title_treaty','Nazwa porozumienia zawartego między dwoma stronami (np. krajami).',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; ',0,0),(171,'nam_pro_vehicle','Nazwa unikalnego pojazdu lub innego obiektu stworzonego przez człowieka zdolnego do przemieszczania się (nazwy statków, samolotów, satelit, itp.). Nazwy marek, serii pojazdów powinny być oznaczane jako brand_nam.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; ',0,0),(172,'nam_pro_media_web','Nazwy portali i stron internetowych.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#C0E7F3;',0,0),(262,'nam_oth_www','Nazwy domen i adresów stron WWW.',1,8,0,NULL,NULL,0,0),(268,'loc','Location',1,44,0,'loc','background: none repeat scroll 0 0 #CC6600; color: #FFFFFF; border-width: 0',0,0),(269,'org','Organization',1,44,0,'org','background: none repeat scroll 0 0 #CC0066; color: #FFFFFF; border-width: 0',0,0),(270,'per','Person',1,44,0,'per','background: none repeat scroll 0 0 #660066; color: #FFFFFF; border-width: 0',0,0),(271,'oth','Other',1,44,0,'oth','background: none repeat scroll 0 0 #990000; color: #FFFFFF; border-width: 0',0,0),(306,'nam_pro_media_tv','Nazwa stacji telewizyjnej.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:Violet;',0,0),(307,'nam_pro_media_radio','Nazwa stacji radiowej.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:Violet;',0,0),(308,'nam_eve_human_holiday','Nazwa święta lub uroczystości obchodzonej cyklicznie.',1,3,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#FFFAA9;',0,0),(309,'nam_eve_human_sport','Nazwa wydarzenia sportowego.',1,3,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#FFFAA9;',0,0),(310,'nam_eve_human_cultural','Nazwa wydarzenia kulturalnego, festynu, koncertu, itp.',1,3,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#FFFAA9;',0,0),(311,'nam_pro_title_album','Nazwa albumu muzycznego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(312,'nam_pro_title_boardgame','Nazwa gry planszowej.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(313,'nam_pro_title_book','Nazwa książki.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(315,'nam_pro_title_article','Nazwa artykułu prasowego lub naukowego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(316,'nam_pro_title_painting','Nazwa obrazu.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(317,'nam_pro_title_song','Nazwa utworu muzycznego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(318,'nam_pro_title_tv','Nazwa programu telewizyjnego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(319,'nam_pro_title_radio','Nazwa programu radiowego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#ECD9D9;',0,0),(321,'nam_pro_model_car','Nazwa modelu samochodu lub innego pojazdu.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#CACAFF;',0,0),(322,'nam_pro_model_phone','Nazwa modelu telefonu, tabletu, smartphona.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#CACAFF;',0,0),(323,'nam_pro_model_plane','Nazwa samolotu, modelu samolotu lub innego pojazdu latającego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#CACAFF;',0,0),(324,'nam_pro_model_ship','Nazwa statku, modelu statu lub innego pojazdu pływającego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#CACAFF;',0,0),(325,'nam_oth_data_format','Nazwa formatu danych.',1,8,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#EE8262;',0,0),(326,'nam_pro_software_os','Nazwa systemu operacyjnego.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; ',0,0),(327,'nam_pro_software_game','Nazwa gry komputerowej, na konsole itp.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; ',0,0),(328,'nam_org_group_team','Nazwa zespołu sportowego.',1,7,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px double green; background:#FFDFDF;',0,0),(330,'nam_loc_land_peak','Nazwa szczytu górskiego.',1,2,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid purple; background:#FFFAA9;',0,0),(331,'nam_oth_mail','Adres e-mail.',1,8,0,'','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(332,'nam_oth_ip','Adres IP.',1,8,0,'','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(333,'nam_num_house2','Numer domu.',1,53,0,'nnh2','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(334,'nam_num_flat2','Numer mieszkania.',1,53,0,'nnf2','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(335,'nam_num_phone2','Numer telefonu.',1,53,0,'nnp2','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(336,'nam_num_postal_code2','Kod pocztowy.',1,53,0,'nnpc2','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(337,'nam_oth_address_street','Pełny adres ulicy z numerem domu i mieszkania.',1,8,0,'','padding: 0 1px; border-bottom: 2px solid purple; ',0,0),(338,'nam_adj_country','Przymiotnik określający pochodzenie.',1,52,0,'','',0,0),(339,'nam_adj_city','Przymiotnik utworzony od nazwy miasta.',1,52,0,'','',0,0),(340,'nam_oth_stock_index','Nazwa indeksu giełdowego.',1,8,0,'','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(341,'nam_org_institution_full','Pełna nazwa instytucji łącznie z nazwą organizacji, do której należy.',1,7,0,'','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(342,'nam_oth_position','Nazwa urzędu sprawowanego przez osobę.',1,8,0,'','padding: 0 1px; border-bottom: 2px solid green; ',0,0),(343,'nam_eve_human_aniversary','Nazwa rocznicy wydarzenia.',1,3,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#FFFAA9;',0,0),(344,'nam_loc_land_desert','Nazwa pustyni.',1,2,0,'','border-bottom: 2px solid purple; ',0,0),(345,'nam_fac_goe_stop','Nazwa przystanku autobusowego, tramwajowego i kolejowego.',1,4,0,'','padding: 0 1px; border-bottom: 2px solid purple; ',0,0),(346,'nam_fac_crossroad','Nazwa skrzyżowania lub ronda.',1,4,0,'','padding: 0 1px; border-bottom: 2px solid purple; ',0,0),(347,'nam_fac_bridge','Nazwa mostu.',1,4,0,'','padding: 0 1px; border-bottom: 2px solid purple; ',0,0),(348,'nam_pro_software_version','Numer wersji oprogramowania.',1,5,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; ',0,0),(349,'nam_eve_natural_phenomenom','Nazwy klęsk żywiołowych, w tym huraganów.',1,3,0,'','border: 1px dashed #555; padding: 0 1px; border-bottom: 2px solid green; background:#FFFAA9;',0,0),(350,'nam_fac_goe_market','Giełda papierów wartościowych.',1,4,0,'','padding: 0 1px; border-bottom: 2px solid purple; ',0,0),(351,'nam_loc_gpe','Nazwy jednostek podziału administracyjnego kraju (w przypadku braku bardziej szczegółowego typu anotacji).',1,2,0,'','',0,0),(352,'nam_loc_gpe_admin','Nazwy jednostek podziału administracyjnego kraju bez określonego poziomu podziału.',1,2,0,'','',0,0),(353,'nam_loc_hydronym','Nazwa naturalnego obiektu wodnego nie przypisana do żadnej podkategorii.',1,2,0,'','',0,0),(354,'nam_loc_land','Nazwy ziemnych obiektów geograficznych.',1,2,0,'','',0,0),(355,'nam_loc_land_protected_area','Nazwa obszaru chronionego, parku krajobrazowego, rezerwatu, obszaru wytyczonego przez człowieka.',1,2,0,'','',0,0),(356,'nam_liv_character','Nazwy indywidualne postaci fikcyjnych, tj. wymyślonych przez autora i posiadających cechy ludzkie, np. ludzie, reprezentanci wymyślonych ras, zwierzęta i przedmioty spersonifikowane.',1,1,0,'','',0,0),(357,'nam_liv_god','Nazwa boga, bóstwa.',1,1,0,'','',0,0),(359,'nam_liv_habitant','Nazwa mieszkańca kraju, miasta, kontynentu pochodząca od nazwy miejsca zamieszkania.',1,1,0,'','',0,0),(360,'nam_adj','Przymiotnik utworzony od nazwy własnej (w przypadku braku bardziej szczegółowego typu anotacji).',1,52,0,'','',0,0),(361,'nam_eve','Nazwy wydarzeń (w przypadku braku bardziej szczegółowego typu anotacji).',1,3,0,'','',0,0),(362,'nam_fac','Nazwa budowli (w przypadku braku bardziej szczegółowego typu anotacji).',1,4,0,'','',0,0),(363,'nam_liv','Nazwa istoty żywej (w przypadku braku bardziej szczegółowego typu anotacji).',1,1,0,'','',0,0),(364,'nam_num2','Nazwa powiązana z wartością numeryczną (w przypadku braku bardziej szczegółowego typu anotacji).',1,53,0,'nn2','',0,0),(365,'nam_org','Nazwy instytucji, organizacji, grup ludzi etc. (w przypadku braku bardziej szczegółowego typu anotacji).',1,7,0,'','',0,0),(366,'nam_pro','Nazwa produktu (w przypadku braku bardziej szczegółowego typu anotacji).',1,5,0,'','',0,0);
/*!40000 ALTER TABLE `annotation_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annotation_types_attributes`
--

DROP TABLE IF EXISTS `annotation_types_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation_types_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annotation_type_id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('radio','string') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `annotation_type_id` (`annotation_type_id`),
  CONSTRAINT `annotation_types_attributes_ibfk_1` FOREIGN KEY (`annotation_type_id`) REFERENCES `annotation_types` (`annotation_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation_types_attributes`
--

LOCK TABLES `annotation_types_attributes` WRITE;
/*!40000 ALTER TABLE `annotation_types_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `annotation_types_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annotation_types_attributes_enum`
--

DROP TABLE IF EXISTS `annotation_types_attributes_enum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation_types_attributes_enum` (
  `annotation_type_attribute_id` int(11) NOT NULL,
  `value` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`annotation_type_attribute_id`,`value`),
  KEY `value` (`value`),
  CONSTRAINT `annotation_types_attributes_enum_ibfk_1` FOREIGN KEY (`annotation_type_attribute_id`) REFERENCES `annotation_types_attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation_types_attributes_enum`
--

LOCK TABLES `annotation_types_attributes_enum` WRITE;
/*!40000 ALTER TABLE `annotation_types_attributes_enum` DISABLE KEYS */;
/*!40000 ALTER TABLE `annotation_types_attributes_enum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annotation_types_shared_attributes`
--

DROP TABLE IF EXISTS `annotation_types_shared_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation_types_shared_attributes` (
  `annotation_type_id` int(11) NOT NULL,
  `shared_attribute_id` int(11) NOT NULL,
  KEY `annotation_type_id` (`annotation_type_id`),
  KEY `shared_attribute_id` (`shared_attribute_id`),
  CONSTRAINT `annotation_types_shared_attributes_ibfk_1` FOREIGN KEY (`shared_attribute_id`) REFERENCES `shared_attributes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `annotation_types_shared_attributes_ibfk_2` FOREIGN KEY (`annotation_type_id`) REFERENCES `annotation_types` (`annotation_type_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation_types_shared_attributes`
--

LOCK TABLES `annotation_types_shared_attributes` WRITE;
/*!40000 ALTER TABLE `annotation_types_shared_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `annotation_types_shared_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annotation_types_shortlist`
--

DROP TABLE IF EXISTS `annotation_types_shortlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation_types_shortlist` (
  `annotation_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shortlist` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation_types_shortlist`
--

LOCK TABLES `annotation_types_shortlist` WRITE;
/*!40000 ALTER TABLE `annotation_types_shortlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `annotation_types_shortlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bases`
--

DROP TABLE IF EXISTS `bases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bases` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `text` (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bases`
--

LOCK TABLES `bases` WRITE;
/*!40000 ALTER TABLE `bases` DISABLE KEYS */;
/*!40000 ALTER TABLE `bases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ccl_viewer`
--

DROP TABLE IF EXISTS `ccl_viewer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ccl_viewer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` blob NOT NULL,
  `elements` blob,
  `ip` tinytext CHARACTER SET utf8 NOT NULL,
  `date` date NOT NULL,
  `key` binary(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ccl_viewer`
--

LOCK TABLES `ccl_viewer` WRITE;
/*!40000 ALTER TABLE `ccl_viewer` DISABLE KEYS */;
/*!40000 ALTER TABLE `ccl_viewer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corpora`
--

DROP TABLE IF EXISTS `corpora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corpora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `public` tinyint(4) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `ext` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `corpora_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corpora`
--

LOCK TABLES `corpora` WRITE;
/*!40000 ALTER TABLE `corpora` DISABLE KEYS */;
/*!40000 ALTER TABLE `corpora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corpora_flags`
--

DROP TABLE IF EXISTS `corpora_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corpora_flags` (
  `corpora_flag_id` int(11) NOT NULL AUTO_INCREMENT,
  `corpora_id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`corpora_flag_id`),
  KEY `corpora_id` (`corpora_id`),
  CONSTRAINT `corpora_flags_ibfk_1` FOREIGN KEY (`corpora_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corpora_flags`
--

LOCK TABLES `corpora_flags` WRITE;
/*!40000 ALTER TABLE `corpora_flags` DISABLE KEYS */;
/*!40000 ALTER TABLE `corpora_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corpora_relations`
--

DROP TABLE IF EXISTS `corpora_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corpora_relations` (
  `corpus_id` int(11) NOT NULL,
  `relation_set_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corpora_relations`
--

LOCK TABLES `corpora_relations` WRITE;
/*!40000 ALTER TABLE `corpora_relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `corpora_relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corpus_and_report_perspectives`
--

DROP TABLE IF EXISTS `corpus_and_report_perspectives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corpus_and_report_perspectives` (
  `perspective_id` varchar(32) CHARACTER SET utf8 NOT NULL,
  `corpus_id` int(11) NOT NULL,
  `access` enum('public','loggedin','role') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`perspective_id`,`corpus_id`),
  KEY `perspective_id` (`perspective_id`),
  KEY `corpus_id` (`corpus_id`),
  CONSTRAINT `corpus_and_report_perspectives_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `corpus_and_report_perspectives_ibfk_2` FOREIGN KEY (`perspective_id`) REFERENCES `report_perspectives` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corpus_and_report_perspectives`
--

LOCK TABLES `corpus_and_report_perspectives` WRITE;
/*!40000 ALTER TABLE `corpus_and_report_perspectives` DISABLE KEYS */;
/*!40000 ALTER TABLE `corpus_and_report_perspectives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corpus_event_groups`
--

DROP TABLE IF EXISTS `corpus_event_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corpus_event_groups` (
  `corpus_id` int(11) NOT NULL,
  `event_group_id` int(11) NOT NULL,
  PRIMARY KEY (`corpus_id`,`event_group_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `event_group_id` (`event_group_id`),
  CONSTRAINT `corpus_event_groups_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `corpus_event_groups_ibfk_2` FOREIGN KEY (`event_group_id`) REFERENCES `event_groups` (`event_group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corpus_event_groups`
--

LOCK TABLES `corpus_event_groups` WRITE;
/*!40000 ALTER TABLE `corpus_event_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `corpus_event_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corpus_perspective_roles`
--

DROP TABLE IF EXISTS `corpus_perspective_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corpus_perspective_roles` (
  `user_id` int(11) NOT NULL,
  `corpus_id` int(11) NOT NULL,
  `report_perspective_id` varchar(32) CHARACTER SET utf8 NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `report_perspective_id` (`report_perspective_id`),
  CONSTRAINT `corpus_perspective_roles_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `corpus_perspective_roles_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `corpus_perspective_roles_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `corpus_perspective_roles_ibfk_4` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `corpus_perspective_roles_ibfk_5` FOREIGN KEY (`report_perspective_id`) REFERENCES `report_perspectives` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corpus_perspective_roles`
--

LOCK TABLES `corpus_perspective_roles` WRITE;
/*!40000 ALTER TABLE `corpus_perspective_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `corpus_perspective_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corpus_roles`
--

DROP TABLE IF EXISTS `corpus_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corpus_roles` (
  `role` varchar(32) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_long` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lista ról użytkownika w dostępie do korpusu';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corpus_roles`
--

LOCK TABLES `corpus_roles` WRITE;
/*!40000 ALTER TABLE `corpus_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `corpus_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corpus_subcorpora`
--

DROP TABLE IF EXISTS `corpus_subcorpora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corpus_subcorpora` (
  `subcorpus_id` int(11) NOT NULL AUTO_INCREMENT,
  `corpus_id` int(11) NOT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`subcorpus_id`),
  KEY `corpus_id` (`corpus_id`),
  CONSTRAINT `corpus_subcorpora_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corpus_subcorpora`
--

LOCK TABLES `corpus_subcorpora` WRITE;
/*!40000 ALTER TABLE `corpus_subcorpora` DISABLE KEYS */;
/*!40000 ALTER TABLE `corpus_subcorpora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_groups`
--

DROP TABLE IF EXISTS `event_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_groups` (
  `event_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`event_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_groups`
--

LOCK TABLES `event_groups` WRITE;
/*!40000 ALTER TABLE `event_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_type_slots`
--

DROP TABLE IF EXISTS `event_type_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_type_slots` (
  `event_type_slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type_id` int(11) NOT NULL,
  PRIMARY KEY (`event_type_slot_id`),
  KEY `event_type_id` (`event_type_id`),
  CONSTRAINT `event_type_slots_ibfk_1` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`event_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_type_slots`
--

LOCK TABLES `event_type_slots` WRITE;
/*!40000 ALTER TABLE `event_type_slots` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_type_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_types`
--

DROP TABLE IF EXISTS `event_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_types` (
  `event_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_group_id` int(11) NOT NULL,
  PRIMARY KEY (`event_type_id`),
  KEY `event_group_id` (`event_group_id`),
  CONSTRAINT `event_types_ibfk_1` FOREIGN KEY (`event_group_id`) REFERENCES `event_groups` (`event_group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_types`
--

LOCK TABLES `event_types` WRITE;
/*!40000 ALTER TABLE `event_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `export_errors`
--

DROP TABLE IF EXISTS `export_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `export_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `export_id` int(11) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `export_errors`
--

LOCK TABLES `export_errors` WRITE;
/*!40000 ALTER TABLE `export_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `export_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exports`
--

DROP TABLE IF EXISTS `exports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exports` (
  `export_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `corpus_id` int(20) NOT NULL,
  `datetime_submit` datetime NOT NULL,
  `datetime_start` datetime DEFAULT NULL,
  `datetime_finish` datetime DEFAULT NULL,
  `status` enum('new','process','done','error') CHARACTER SET utf8 NOT NULL DEFAULT 'new',
  `description` text COLLATE utf8mb4_unicode_ci,
  `selectors` text COLLATE utf8mb4_unicode_ci,
  `extractors` text COLLATE utf8mb4_unicode_ci,
  `indices` text COLLATE utf8mb4_unicode_ci,
  `tagging` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tagger',
  `message` text COLLATE utf8mb4_unicode_ci,
  `progress` int(11) NOT NULL DEFAULT '0',
  `statistics` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`export_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `status` (`status`),
  CONSTRAINT `exports_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela z historią zadań eksportu korpusów';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exports`
--

LOCK TABLES `exports` WRITE;
/*!40000 ALTER TABLE `exports` DISABLE KEYS */;
/*!40000 ALTER TABLE `exports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flag_status_history`
--

DROP TABLE IF EXISTS `flag_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flag_status_history` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `flag_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `new_status` int(11) NOT NULL,
  `old_status` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`),
  KEY `flag_id` (`flag_id`),
  KEY `user_id` (`user_id`),
  KEY `new_status` (`new_status`),
  KEY `old_status` (`old_status`),
  CONSTRAINT `flag_status_history_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `flag_status_history_ibfk_2` FOREIGN KEY (`flag_id`) REFERENCES `corpora_flags` (`corpora_flag_id`),
  CONSTRAINT `flag_status_history_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `flag_status_history_ibfk_4` FOREIGN KEY (`new_status`) REFERENCES `flags` (`flag_id`),
  CONSTRAINT `flag_status_history_ibfk_5` FOREIGN KEY (`old_status`) REFERENCES `flags` (`flag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flag_status_history`
--

LOCK TABLES `flag_status_history` WRITE;
/*!40000 ALTER TABLE `flag_status_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `flag_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flags`
--

DROP TABLE IF EXISTS `flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flags` (
  `flag_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`flag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flags`
--

LOCK TABLES `flags` WRITE;
/*!40000 ALTER TABLE `flags` DISABLE KEYS */;
INSERT INTO `flags` VALUES (-1,'not ready','Document is not ready to process.'),(1,'ready to work','Document is ready to process'),(2,'under development','Document is under development'),(3,'ready','Document is ready'),(4,'verified','Document is verified'),(5,'to correct','Document needs correction');
/*!40000 ALTER TABLE `flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `corpus_id` int(11) NOT NULL,
  `original_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `corpus_id` (`corpus_id`),
  CONSTRAINT `images_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ips`
--

DROP TABLE IF EXISTS `ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ips` (
  `ip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(39) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ip_id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ips`
--

LOCK TABLES `ips` WRITE;
/*!40000 ALTER TABLE `ips` DISABLE KEYS */;
/*!40000 ALTER TABLE `ips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lang`
--

DROP TABLE IF EXISTS `lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lang` (
  `code` char(3) CHARACTER SET utf8 NOT NULL,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lang`
--

LOCK TABLES `lang` WRITE;
/*!40000 ALTER TABLE `lang` DISABLE KEYS */;
INSERT INTO `lang` VALUES ('aar','Afar'),('abk','Abkhazian'),('ace','Achinese'),('ach','Acoli'),('ada','Adangme'),('ady','Adyghe; Adygei'),('afa','Afro-Asiatic languages'),('afh','Afrihili'),('afr','Afrikaans'),('ain','Ainu'),('aka','Akan'),('akk','Akkadian'),('alb','Albanian'),('ale','Aleut'),('alg','Algonquian languages'),('alt','Southern Altai'),('amh','Amharic'),('ang','English, Old (ca.450-1100)'),('anp','Angika'),('apa','Apache languages'),('ara','Arabic'),('arc','Official Aramaic (700-300 BCE); Imperial Aramaic (700-300 BCE)'),('arg','Aragonese'),('arm','Armenian'),('arn','Mapudungun; Mapuche'),('arp','Arapaho'),('art','Artificial languages'),('arw','Arawak'),('asm','Assamese'),('ast','Asturian; Bable; Leonese; Asturleonese'),('ath','Athapascan languages'),('aus','Australian languages'),('ava','Avaric'),('ave','Avestan'),('awa','Awadhi'),('aym','Aymara'),('aze','Azerbaijani'),('bad','Banda languages'),('bai','Bamileke languages'),('bak','Bashkir'),('bal','Baluchi'),('bam','Bambara'),('ban','Balinese'),('baq','Basque'),('bas','Basa'),('bat','Baltic languages'),('bej','Beja; Bedawiyet'),('bel','Belarusian'),('bem','Bemba'),('ben','Bengali'),('ber','Berber languages'),('bho','Bhojpuri'),('bih','Bihari languages'),('bik','Bikol'),('bin','Bini; Edo'),('bis','Bislama'),('bla','Siksika'),('bnt','Bantu (Other)'),('bos','Bosnian'),('bra','Braj'),('bre','Breton'),('btk','Batak languages'),('bua','Buriat'),('bug','Buginese'),('bul','Bulgarian'),('bur','Burmese'),('byn','Blin; Bilin'),('cad','Caddo'),('cai','Central American Indian languages'),('car','Galibi Carib'),('cat','Catalan; Valencian'),('cau','Caucasian languages'),('ceb','Cebuano'),('cel','Celtic languages'),('cha','Chamorro'),('chb','Chibcha'),('che','Chechen'),('chg','Chagatai'),('chi','Chinese'),('chk','Chuukese'),('chm','Mari'),('chn','Chinook jargon'),('cho','Choctaw'),('chp','Chipewyan; Dene Suline'),('chr','Cherokee'),('chu','Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic'),('chv','Chuvash'),('chy','Cheyenne'),('cmc','Chamic languages'),('cop','Coptic'),('cor','Cornish'),('cos','Corsican'),('cpe','Creoles and pidgins, English based'),('cpf','Creoles and pidgins, French-based '),('cpp','Creoles and pidgins, Portuguese-based '),('cre','Cree'),('crh','Crimean Tatar; Crimean Turkish'),('crp','Creoles and pidgins '),('csb','Kashubian'),('cus','Cushitic languages'),('cze','Czech'),('dak','Dakota'),('dan','Danish'),('dar','Dargwa'),('day','Land Dayak languages'),('del','Delaware'),('den','Slave (Athapascan)'),('dgr','Dogrib'),('din','Dinka'),('div','Divehi; Dhivehi; Maldivian'),('doi','Dogri'),('dra','Dravidian languages'),('dsb','Lower Sorbian'),('dua','Duala'),('dum','Dutch, Middle (ca.1050-1350)'),('dut','Dutch; Flemish'),('dyu','Dyula'),('dzo','Dzongkha'),('efi','Efik'),('egy','Egyptian (Ancient)'),('eka','Ekajuk'),('elx','Elamite'),('eng','English'),('enm','English, Middle (1100-1500)'),('epo','Esperanto'),('est','Estonian'),('ewe','Ewe'),('ewo','Ewondo'),('fan','Fang'),('fao','Faroese'),('fat','Fanti'),('fij','Fijian'),('fil','Filipino; Pilipino'),('fin','Finnish'),('fiu','Finno-Ugrian languages'),('fon','Fon'),('fre','French'),('frm','French, Middle (ca.1400-1600)'),('fro','French, Old (842-ca.1400)'),('frr','Northern Frisian'),('frs','Eastern Frisian'),('fry','Western Frisian'),('ful','Fulah'),('fur','Friulian'),('gaa','Ga'),('gay','Gayo'),('gba','Gbaya'),('gem','Germanic languages'),('geo','Georgian'),('ger','German'),('gez','Geez'),('gil','Gilbertese'),('gla','Gaelic; Scottish Gaelic'),('gle','Irish'),('glg','Galician'),('glv','Manx'),('gmh','German, Middle High (ca.1050-1500)'),('goh','German, Old High (ca.750-1050)'),('gon','Gondi'),('gor','Gorontalo'),('got','Gothic'),('grb','Grebo'),('grc','Greek, Ancient (to 1453)'),('gre','Greek, Modern (1453-)'),('grn','Guarani'),('gsw','Swiss German; Alemannic; Alsatian'),('guj','Gujarati'),('gwi','Gwich\'in'),('hai','Haida'),('hat','Haitian; Haitian Creole'),('hau','Hausa'),('haw','Hawaiian'),('heb','Hebrew'),('her','Herero'),('hil','Hiligaynon'),('him','Himachali languages; Western Pahari languages'),('hin','Hindi'),('hit','Hittite'),('hmn','Hmong; Mong'),('hmo','Hiri Motu'),('hrv','Croatian'),('hsb','Upper Sorbian'),('hun','Hungarian'),('hup','Hupa'),('iba','Iban'),('ibo','Igbo'),('ice','Icelandic'),('ido','Ido'),('iii','Sichuan Yi; Nuosu'),('ijo','Ijo languages'),('iku','Inuktitut'),('ile','Interlingue; Occidental'),('ilo','Iloko'),('ina','Interlingua (International Auxiliary Language Association)'),('inc','Indic languages'),('ind','Indonesian'),('ine','Indo-European languages'),('inh','Ingush'),('ipk','Inupiaq'),('ira','Iranian languages'),('iro','Iroquoian languages'),('ita','Italian'),('jav','Javanese'),('jbo','Lojban'),('jpn','Japanese'),('jpr','Judeo-Persian'),('jrb','Judeo-Arabic'),('kaa','Kara-Kalpak'),('kab','Kabyle'),('kac','Kachin; Jingpho'),('kal','Kalaallisut; Greenlandic'),('kam','Kamba'),('kan','Kannada'),('kar','Karen languages'),('kas','Kashmiri'),('kau','Kanuri'),('kaw','Kawi'),('kaz','Kazakh'),('kbd','Kabardian'),('kha','Khasi'),('khi','Khoisan languages'),('khm','Central Khmer'),('kho','Khotanese; Sakan'),('kik','Kikuyu; Gikuyu'),('kin','Kinyarwanda'),('kir','Kirghiz; Kyrgyz'),('kmb','Kimbundu'),('kok','Konkani'),('kom','Komi'),('kon','Kongo'),('kor','Korean'),('kos','Kosraean'),('kpe','Kpelle'),('krc','Karachay-Balkar'),('krl','Karelian'),('kro','Kru languages'),('kru','Kurukh'),('kua','Kuanyama; Kwanyama'),('kum','Kumyk'),('kur','Kurdish'),('kut','Kutenai'),('lad','Ladino'),('lah','Lahnda'),('lam','Lamba'),('lao','Lao'),('lat','Latin'),('lav','Latvian'),('lez','Lezghian'),('lim','Limburgan; Limburger; Limburgish'),('lin','Lingala'),('lit','Lithuanian'),('lol','Mongo'),('loz','Lozi'),('ltz','Luxembourgish; Letzeburgesch'),('lua','Luba-Lulua'),('lub','Luba-Katanga'),('lug','Ganda'),('lui','Luiseno'),('lun','Lunda'),('luo','Luo (Kenya and Tanzania)'),('lus','Lushai'),('mac','Macedonian'),('mad','Madurese'),('mag','Magahi'),('mah','Marshallese'),('mai','Maithili'),('mak','Makasar'),('mal','Malayalam'),('man','Mandingo'),('mao','Maori'),('map','Austronesian languages'),('mar','Marathi'),('mas','Masai'),('may','Malay'),('mdf','Moksha'),('mdr','Mandar'),('men','Mende'),('mga','Irish, Middle (900-1200)'),('mic','Mi\'kmaq; Micmac'),('min','Minangkabau'),('mis','Uncoded languages'),('mkh','Mon-Khmer languages'),('mlg','Malagasy'),('mlt','Maltese'),('mnc','Manchu'),('mni','Manipuri'),('mno','Manobo languages'),('moh','Mohawk'),('mon','Mongolian'),('mos','Mossi'),('mul','Multiple languages'),('mun','Munda languages'),('mus','Creek'),('mwl','Mirandese'),('mwr','Marwari'),('myn','Mayan languages'),('myv','Erzya'),('nah','Nahuatl languages'),('nai','North American Indian languages'),('nap','Neapolitan'),('nau','Nauru'),('nav','Navajo; Navaho'),('nbl','Ndebele, South; South Ndebele'),('nde','Ndebele, North; North Ndebele'),('ndo','Ndonga'),('nds','Low German; Low Saxon; German, Low; Saxon, Low'),('nep','Nepali'),('new','Nepal Bhasa; Newari'),('nia','Nias'),('nic','Niger-Kordofanian languages'),('niu','Niuean'),('nno','Norwegian Nynorsk; Nynorsk, Norwegian'),('nob','Bokm'),('nog','Nogai'),('non','Norse, Old'),('nor','Norwegian'),('nqo','N\'Ko'),('nso','Pedi; Sepedi; Northern Sotho'),('nub','Nubian languages'),('nwc','Classical Newari; Old Newari; Classical Nepal Bhasa'),('nya','Chichewa; Chewa; Nyanja'),('nym','Nyamwezi'),('nyn','Nyankole'),('nyo','Nyoro'),('nzi','Nzima'),('oci','Occitan (post 1500); Proven'),('oji','Ojibwa'),('ori','Oriya'),('orm','Oromo'),('osa','Osage'),('oss','Ossetian; Ossetic'),('ota','Turkish, Ottoman (1500-1928)'),('oto','Otomian languages'),('paa','Papuan languages'),('pag','Pangasinan'),('pal','Pahlavi'),('pam','Pampanga; Kapampangan'),('pan','Panjabi; Punjabi'),('pap','Papiamento'),('pau','Palauan'),('peo','Persian, Old (ca.600-400 B.C.)'),('per','Persian'),('phi','Philippine languages'),('phn','Phoenician'),('pli','Pali'),('pol','Polish'),('pon','Pohnpeian'),('por','Portuguese'),('pra','Prakrit languages'),('pro','Proven'),('pus','Pushto; Pashto'),('que','Quechua'),('raj','Rajasthani'),('rap','Rapanui'),('rar','Rarotongan; Cook Islands Maori'),('roa','Romance languages'),('roh','Romansh'),('rom','Romany'),('rum','Romanian; Moldavian; Moldovan'),('run','Rundi'),('rup','Aromanian; Arumanian; Macedo-Romanian'),('rus','Russian'),('sad','Sandawe'),('sag','Sango'),('sah','Yakut'),('sai','South American Indian (Other)'),('sal','Salishan languages'),('sam','Samaritan Aramaic'),('san','Sanskrit'),('sas','Sasak'),('sat','Santali'),('scn','Sicilian'),('sco','Scots'),('sel','Selkup'),('sem','Semitic languages'),('sga','Irish, Old (to 900)'),('sgn','Sign Languages'),('shn','Shan'),('sid','Sidamo'),('sin','Sinhala; Sinhalese'),('sio','Siouan languages'),('sit','Sino-Tibetan languages'),('sla','Slavic languages'),('slo','Slovak'),('slv','Slovenian'),('sma','Southern Sami'),('sme','Northern Sami'),('smi','Sami languages'),('smj','Lule Sami'),('smn','Inari Sami'),('smo','Samoan'),('sms','Skolt Sami'),('sna','Shona'),('snd','Sindhi'),('snk','Soninke'),('sog','Sogdian'),('som','Somali'),('son','Songhai languages'),('sot','Sotho, Southern'),('spa','Spanish; Castilian'),('srd','Sardinian'),('srn','Sranan Tongo'),('srp','Serbian'),('srr','Serer'),('ssa','Nilo-Saharan languages'),('ssw','Swati'),('suk','Sukuma'),('sun','Sundanese'),('sus','Susu'),('sux','Sumerian'),('swa','Swahili'),('swe','Swedish'),('syc','Classical Syriac'),('syr','Syriac'),('tah','Tahitian'),('tai','Tai languages'),('tam','Tamil'),('tat','Tatar'),('tel','Telugu'),('tem','Timne'),('ter','Tereno'),('tet','Tetum'),('tgk','Tajik'),('tgl','Tagalog'),('tha','Thai'),('tib','Tibetan'),('tig','Tigre'),('tir','Tigrinya'),('tiv','Tiv'),('tkl','Tokelau'),('tlh','Klingon; tlhIngan-Hol'),('tli','Tlingit'),('tmh','Tamashek'),('tog','Tonga (Nyasa)'),('ton','Tonga (Tonga Islands)'),('tpi','Tok Pisin'),('tsi','Tsimshian'),('tsn','Tswana'),('tso','Tsonga'),('tuk','Turkmen'),('tum','Tumbuka'),('tup','Tupi languages'),('tur','Turkish'),('tut','Altaic languages'),('tvl','Tuvalu'),('twi','Twi'),('tyv','Tuvinian'),('udm','Udmurt'),('uga','Ugaritic'),('uig','Uighur; Uyghur'),('ukr','Ukrainian'),('umb','Umbundu'),('und','Undetermined'),('urd','Urdu'),('uzb','Uzbek'),('vai','Vai'),('ven','Venda'),('vie','Vietnamese'),('vol','Volap'),('vot','Votic'),('wak','Wakashan languages'),('wal','Walamo'),('war','Waray'),('was','Washo'),('wel','Welsh'),('wen','Sorbian languages'),('wln','Walloon'),('wol','Wolof'),('xal','Kalmyk; Oirat'),('xho','Xhosa'),('yao','Yao'),('yap','Yapese'),('yid','Yiddish'),('yor','Yoruba'),('ypk','Yupik languages'),('zap','Zapotec'),('zbl','Blissymbols; Blissymbolics; Bliss'),('zen','Zenaga'),('zha','Zhuang; Chuang'),('znd','Zande languages'),('zul','Zulu'),('zun','Zuni'),('zxx','No linguistic content; Not applicable'),('zza','Zaza; Dimili; Dimli; Kirdki; Kirmanjki; Zazaki');
/*!40000 ALTER TABLE `lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relation_sets`
--

DROP TABLE IF EXISTS `relation_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relation_sets` (
  `relation_set_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `public` int(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`relation_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relation_sets`
--

LOCK TABLES `relation_sets` WRITE;
/*!40000 ALTER TABLE `relation_sets` DISABLE KEYS */;
/*!40000 ALTER TABLE `relation_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relation_types`
--

DROP TABLE IF EXISTS `relation_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relation_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_set_id` int(11) DEFAULT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `annotation_set_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `annotation_set_id` (`annotation_set_id`),
  KEY `relation_set_id` (`relation_set_id`),
  CONSTRAINT `relation_types_ibfk_1` FOREIGN KEY (`annotation_set_id`) REFERENCES `annotation_sets` (`annotation_set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `relation_types_ibfk_2` FOREIGN KEY (`relation_set_id`) REFERENCES `relation_sets` (`relation_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relation_types`
--

LOCK TABLES `relation_types` WRITE;
/*!40000 ALTER TABLE `relation_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `relation_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relations`
--

DROP TABLE IF EXISTS `relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `relation_type_id` int(11) NOT NULL,
  `source_id` bigint(20) NOT NULL,
  `target_id` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `stage` enum('final','discarded','agreement') CHARACTER SET utf8 NOT NULL DEFAULT 'final',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`),
  KEY `source_id` (`source_id`),
  KEY `relation_type_id` (`relation_type_id`),
  KEY `stage` (`stage`),
  CONSTRAINT `relations_ibfk_12` FOREIGN KEY (`source_id`) REFERENCES `reports_annotations_optimized` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `relations_ibfk_13` FOREIGN KEY (`target_id`) REFERENCES `reports_annotations_optimized` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `relations_ibfk_6` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `relations_ibfk_9` FOREIGN KEY (`relation_type_id`) REFERENCES `relation_types` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relations`
--

LOCK TABLES `relations` WRITE;
/*!40000 ALTER TABLE `relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relations_groups`
--

DROP TABLE IF EXISTS `relations_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relations_groups` (
  `relation_type_id` int(11) NOT NULL,
  `part` enum('source','target') CHARACTER SET utf8 NOT NULL,
  `annotation_set_id` int(11) DEFAULT NULL,
  `annotation_subset_id` int(11) DEFAULT NULL,
  `annotation_type_id` int(11) DEFAULT NULL,
  KEY `relation_type_id` (`relation_type_id`),
  KEY `annotation_set_id` (`annotation_set_id`),
  KEY `annotation_subset_id` (`annotation_subset_id`),
  CONSTRAINT `relations_groups_ibfk_1` FOREIGN KEY (`relation_type_id`) REFERENCES `relation_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `relations_groups_ibfk_2` FOREIGN KEY (`annotation_set_id`) REFERENCES `annotation_sets` (`annotation_set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `relations_groups_ibfk_3` FOREIGN KEY (`annotation_subset_id`) REFERENCES `annotation_subsets` (`annotation_subset_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relations_groups`
--

LOCK TABLES `relations_groups` WRITE;
/*!40000 ALTER TABLE `relations_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `relations_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_perspectives`
--

DROP TABLE IF EXISTS `report_perspectives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_perspectives` (
  `id` varchar(32) CHARACTER SET utf8 NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_perspectives`
--

LOCK TABLES `report_perspectives` WRITE;
/*!40000 ALTER TABLE `report_perspectives` DISABLE KEYS */;
INSERT INTO `report_perspectives` VALUES ('agreement','Agreement','',10),('anaphora','Anaphora Viewer','Show anaphora relations.',51),('annotation_lemma','Annotation lemmas','Allows to edit lemmas for existing annotations.',100),('annotator','Annotator','Show and edit document annotations, relations and events.',40),('annotatorwsd','WSD','Show and edit WSD annotation senses.',85),('annotator_anaphora','Anaphora','Show and edit anaphora relations.',50),('autoextension','Bootstrapping','Verify automaticaly added annotations.',78),('cleanup','Content cleanup','Display document content along with it\'s sources.',22),('diffs','History of changes','Show history of document content changes.',75),('edit','Content','Show and edit document content.',20),('edittranslation','Edit translation','Edit document translation',320),('extendedmetadata','Extended metadata','Metadata including translations, images and document content.',325),('flag_history','Flag history','Show the history of flag changes.',320),('images','Images','Manage images attached to the document.',200),('importannotations','Import annotations','Import annotations from a .CCL file.',150),('metadata','Metadata','Show and edit document metadata.',10),('morphodisamb','Morphological Disambiguation','Show and edit user morphological disamgibuations',300),('morphodisambagreement','Morphological Disambiguation Agreement','Show and edit final morphological disambiguations',310),('preview','Preview','Show document content as formated and raw content.',0),('relation_agreement','Relation agreement','',11),('tokenization','Tokenization','Displays current document tokenization and allows to perform new tokenization by uploading a ccl file or by a web service.',100),('topic','Topic','Edit document topic categorization.',25),('transcription','Transcription','Show images and edit document content with a toolbox.',10),('viewer','Content Viewer','Display document content with hidden sensitive information.',0);
/*!40000 ALTER TABLE `report_perspectives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `corpora` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_ci,
  `source` text COLLATE utf8mb4_unicode_ci,
  `author` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `type` int(11) DEFAULT '1',
  `status` int(11) DEFAULT '1',
  `user_id` int(11) NOT NULL COMMENT 'Identyfikator użytkownika, który dodał dokument',
  `subcorpus_id` int(11) DEFAULT NULL,
  `tokenization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format_id` int(11) NOT NULL DEFAULT '1',
  `lang` char(3) CHARACTER SET utf8 DEFAULT NULL,
  `filename` text COLLATE utf8mb4_unicode_ci,
  `parent_report_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `type` (`type`),
  KEY `corpora` (`corpora`),
  KEY `date` (`date`),
  KEY `user_id` (`user_id`),
  KEY `subcorpus_id` (`subcorpus_id`),
  KEY `format_id` (`format_id`),
  KEY `format_id_2` (`format_id`),
  KEY `format_id_3` (`format_id`),
  KEY `lang` (`lang`),
  KEY `fk_parent_report_id` (`parent_report_id`),
  CONSTRAINT `fk_parent_report_id` FOREIGN KEY (`parent_report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `reports_ibfk_10` FOREIGN KEY (`format_id`) REFERENCES `reports_formats` (`id`),
  CONSTRAINT `reports_ibfk_15` FOREIGN KEY (`type`) REFERENCES `reports_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `reports_ibfk_16` FOREIGN KEY (`subcorpus_id`) REFERENCES `corpus_subcorpora` (`subcorpus_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `reports_ibfk_17` FOREIGN KEY (`lang`) REFERENCES `lang` (`code`),
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`type`) REFERENCES `reports_types` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`status`) REFERENCES `reports_statuses` (`id`),
  CONSTRAINT `reports_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `reports_ibfk_6` FOREIGN KEY (`corpora`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reports_ibfk_9` FOREIGN KEY (`format_id`) REFERENCES `reports_formats` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_and_images`
--

DROP TABLE IF EXISTS `reports_and_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_and_images` (
  `report_id` bigint(20) NOT NULL,
  `image_id` bigint(20) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`report_id`,`image_id`),
  KEY `report_id` (`report_id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `reports_and_images_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reports_and_images_ibfk_2` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_and_images`
--

LOCK TABLES `reports_and_images` WRITE;
/*!40000 ALTER TABLE `reports_and_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_and_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `reports_annotations`
--

DROP TABLE IF EXISTS `reports_annotations`;
/*!50001 DROP VIEW IF EXISTS `reports_annotations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `reports_annotations` AS SELECT 
 1 AS `id`,
 1 AS `report_id`,
 1 AS `type_id`,
 1 AS `type`,
 1 AS `from`,
 1 AS `to`,
 1 AS `text`,
 1 AS `user_id`,
 1 AS `creation_time`,
 1 AS `stage`,
 1 AS `source`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `reports_annotations_attributes`
--

DROP TABLE IF EXISTS `reports_annotations_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_annotations_attributes` (
  `annotation_id` bigint(20) NOT NULL,
  `annotation_attribute_id` int(11) NOT NULL,
  `value` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`annotation_id`,`annotation_attribute_id`,`user_id`),
  KEY `annotation_id` (`annotation_id`),
  KEY `annotation_attribute_id` (`annotation_attribute_id`),
  KEY `user_id` (`user_id`),
  KEY `value` (`value`),
  CONSTRAINT `reports_annotations_attributes_ibfk_1` FOREIGN KEY (`annotation_attribute_id`) REFERENCES `annotation_types_attributes` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_annotations_attributes_ibfk_2` FOREIGN KEY (`annotation_id`) REFERENCES `reports_annotations_optimized` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_annotations_attributes`
--

LOCK TABLES `reports_annotations_attributes` WRITE;
/*!40000 ALTER TABLE `reports_annotations_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_annotations_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_annotations_lemma`
--

DROP TABLE IF EXISTS `reports_annotations_lemma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_annotations_lemma` (
  `report_annotation_id` bigint(20) NOT NULL,
  `lemma` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `report_annotation_id` (`report_annotation_id`),
  CONSTRAINT `reports_annotations_lemma_ibfk_1` FOREIGN KEY (`report_annotation_id`) REFERENCES `reports_annotations_optimized` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_annotations_lemma`
--

LOCK TABLES `reports_annotations_lemma` WRITE;
/*!40000 ALTER TABLE `reports_annotations_lemma` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_annotations_lemma` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_annotations_optimized`
--

DROP TABLE IF EXISTS `reports_annotations_optimized`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_annotations_optimized` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data i czas dodania anotacji.',
  `stage` enum('new','final','discarded','agreement') CHARACTER SET utf8 NOT NULL DEFAULT 'final',
  `source` enum('user','bootstrapping','auto') CHARACTER SET utf8 NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`),
  KEY `user_id` (`user_id`),
  KEY `FK_reports_annotations_annotation_types` (`type_id`),
  KEY `stage` (`stage`),
  KEY `source` (`source`),
  CONSTRAINT `reports_annotations_optimized_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_annotations_optimized_ibfk_3` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reports_annotations_optimized_ibfk_4` FOREIGN KEY (`type_id`) REFERENCES `annotation_types` (`annotation_type_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_annotations_optimized`
--

LOCK TABLES `reports_annotations_optimized` WRITE;
/*!40000 ALTER TABLE `reports_annotations_optimized` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_annotations_optimized` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_annotations_shared_attributes`
--

DROP TABLE IF EXISTS `reports_annotations_shared_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_annotations_shared_attributes` (
  `annotation_id` bigint(20) NOT NULL,
  `shared_attribute_id` int(11) NOT NULL,
  `value` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `unique_annotations_shared_attributes_values` (`annotation_id`,`shared_attribute_id`),
  KEY `annotation_id` (`annotation_id`),
  KEY `shared_attribute_id` (`shared_attribute_id`),
  KEY `user_id` (`user_id`),
  KEY `value` (`value`),
  CONSTRAINT `reports_annotations_shared_attributes_ibfk_1` FOREIGN KEY (`annotation_id`) REFERENCES `reports_annotations_optimized` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `reports_annotations_shared_attributes_ibfk_2` FOREIGN KEY (`shared_attribute_id`) REFERENCES `shared_attributes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `reports_annotations_shared_attributes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `reports_annotations_shared_attributes_ibfk_4` FOREIGN KEY (`value`) REFERENCES `annotation_types_attributes_enum` (`value`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_annotations_shared_attributes`
--

LOCK TABLES `reports_annotations_shared_attributes` WRITE;
/*!40000 ALTER TABLE `reports_annotations_shared_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_annotations_shared_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_diffs`
--

DROP TABLE IF EXISTS `reports_diffs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_diffs` (
  `diff_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `datetime` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `diff` blob NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`diff_id`),
  KEY `user_id` (`user_id`),
  KEY `report_id` (`report_id`),
  CONSTRAINT `reports_diffs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_diffs_ibfk_2` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_diffs`
--

LOCK TABLES `reports_diffs` WRITE;
/*!40000 ALTER TABLE `reports_diffs` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_diffs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_events`
--

DROP TABLE IF EXISTS `reports_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_events` (
  `report_event_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `event_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  PRIMARY KEY (`report_event_id`),
  KEY `report_id` (`report_id`),
  KEY `event_type_id` (`event_type_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reports_events_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reports_events_ibfk_2` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`event_type_id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_events_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_events`
--

LOCK TABLES `reports_events` WRITE;
/*!40000 ALTER TABLE `reports_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_events_slots`
--

DROP TABLE IF EXISTS `reports_events_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_events_slots` (
  `report_event_slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_event_id` int(11) NOT NULL,
  `report_annotation_id` bigint(20) DEFAULT NULL,
  `event_type_slot_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `user_update_id` int(11) NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`report_event_slot_id`),
  KEY `report_event_id` (`report_event_id`),
  KEY `report_annotation_id` (`report_annotation_id`),
  KEY `event_type_slot_id` (`event_type_slot_id`),
  KEY `user_id` (`user_id`),
  KEY `user_update_id` (`user_update_id`),
  CONSTRAINT `reports_events_slots_ibfk_1` FOREIGN KEY (`report_annotation_id`) REFERENCES `reports_annotations_optimized` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_events_slots_ibfk_2` FOREIGN KEY (`event_type_slot_id`) REFERENCES `event_type_slots` (`event_type_slot_id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_events_slots_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_events_slots_ibfk_4` FOREIGN KEY (`user_update_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `reports_events_slots_ibfk_5` FOREIGN KEY (`report_event_id`) REFERENCES `reports_events` (`report_event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_events_slots`
--

LOCK TABLES `reports_events_slots` WRITE;
/*!40000 ALTER TABLE `reports_events_slots` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_events_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_flags`
--

DROP TABLE IF EXISTS `reports_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_flags` (
  `corpora_flag_id` int(11) NOT NULL,
  `report_id` bigint(20) NOT NULL,
  `flag_id` int(11) NOT NULL,
  UNIQUE KEY `corpora_flag_id_unique` (`corpora_flag_id`,`report_id`),
  KEY `fk_flag_id` (`flag_id`),
  KEY `report_id` (`report_id`),
  CONSTRAINT `fk_flag_id` FOREIGN KEY (`flag_id`) REFERENCES `flags` (`flag_id`),
  CONSTRAINT `reports_flags_ibfk_1` FOREIGN KEY (`corpora_flag_id`) REFERENCES `corpora_flags` (`corpora_flag_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reports_flags_ibfk_2` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_flags`
--

LOCK TABLES `reports_flags` WRITE;
/*!40000 ALTER TABLE `reports_flags` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_formats`
--

DROP TABLE IF EXISTS `reports_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `format` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_formats`
--

LOCK TABLES `reports_formats` WRITE;
/*!40000 ALTER TABLE `reports_formats` DISABLE KEYS */;
INSERT INTO `reports_formats` VALUES (1,'xml'),(2,'plain'),(3,'premorph');
/*!40000 ALTER TABLE `reports_formats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_limited_access`
--

DROP TABLE IF EXISTS `reports_limited_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_limited_access` (
  `report_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `report_id` (`report_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reports_limited_access_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reports_limited_access_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_limited_access`
--

LOCK TABLES `reports_limited_access` WRITE;
/*!40000 ALTER TABLE `reports_limited_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_limited_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_statuses`
--

DROP TABLE IF EXISTS `reports_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_statuses`
--

LOCK TABLES `reports_statuses` WRITE;
/*!40000 ALTER TABLE `reports_statuses` DISABLE KEYS */;
INSERT INTO `reports_statuses` VALUES (1,'Nieznany','Komunikat nie został jeszcze ręcznie sprawdzony',2),(2,'Przyjęty','Komunikat zawiera wartościową treść -- pełny opis zdarzenia.',1),(3,'Załącznik','Komunikat nie zawiera wartościowej treści. Treść wskazuje na załącznik lub załączniki, w którym opisane są zdarzenia.',4),(5,'Odrzucony','Komunikat zawiera nie istotine informacje.',3),(6,'Odłożony','',7),(7,'Do usunięcia','Dokument powinien zostać usunięty',8);
/*!40000 ALTER TABLE `reports_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_types`
--

DROP TABLE IF EXISTS `reports_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_types`
--

LOCK TABLES `reports_types` WRITE;
/*!40000 ALTER TABLE `reports_types` DISABLE KEYS */;
INSERT INTO `reports_types` VALUES (1,'!! Nieokreślony !!');
/*!40000 ALTER TABLE `reports_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_users_selection`
--

DROP TABLE IF EXISTS `reports_users_selection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_users_selection` (
  `user_id` int(11) NOT NULL,
  `report_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_users_selection`
--

LOCK TABLES `reports_users_selection` WRITE;
/*!40000 ALTER TABLE `reports_users_selection` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_users_selection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role` varchar(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Opis roli',
  PRIMARY KEY (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lista dostępnych ról w systemie';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES ('admin','Prawa administratora'),('create_corpus','Prawo do tworzenia nowych korpusów.'),('editor_schema_events','Edit schema events.'),('editor_schema_relations','Edit schema relations.');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shared_attributes`
--

DROP TABLE IF EXISTS `shared_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shared_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('enum','string') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shared_attributes`
--

LOCK TABLES `shared_attributes` WRITE;
/*!40000 ALTER TABLE `shared_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `shared_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shared_attributes_enum`
--

DROP TABLE IF EXISTS `shared_attributes_enum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shared_attributes_enum` (
  `shared_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `shared_attribute_id` (`shared_attribute_id`),
  CONSTRAINT `shared_attributes_enum_ibfk_1` FOREIGN KEY (`shared_attribute_id`) REFERENCES `shared_attributes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shared_attributes_enum`
--

LOCK TABLES `shared_attributes_enum` WRITE;
/*!40000 ALTER TABLE `shared_attributes_enum` DISABLE KEYS */;
/*!40000 ALTER TABLE `shared_attributes_enum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tagsets`
--

DROP TABLE IF EXISTS `tagsets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tagsets` (
  `tagset_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tagset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tagsets`
--

LOCK TABLES `tagsets` WRITE;
/*!40000 ALTER TABLE `tagsets` DISABLE KEYS */;
INSERT INTO `tagsets` VALUES (1,'nkjp'),(2,'English'),(3,'German');
/*!40000 ALTER TABLE `tagsets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data i czas utworzenia zadania.',
  `datetime_start` timestamp NULL DEFAULT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Identyfikator zadania.',
  `description` text COLLATE utf8mb4_unicode_ci,
  `parameters` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `corpus_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `max_steps` int(11) NOT NULL,
  `current_step` int(11) NOT NULL,
  `status` enum('new','process','done','error') CHARACTER SET utf8 NOT NULL DEFAULT 'new',
  `message` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`task_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks_reports`
--

DROP TABLE IF EXISTS `tasks_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks_reports` (
  `task_id` int(11) NOT NULL,
  `report_id` bigint(11) NOT NULL,
  `status` enum('new','process','done','error') CHARACTER SET utf8 NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `document_id` (`report_id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `tasks_reports_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_reports_ibfk_2` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks_reports`
--

LOCK TABLES `tasks_reports` WRITE;
/*!40000 ALTER TABLE `tasks_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `token_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `eos` tinyint(1) NOT NULL,
  PRIMARY KEY (`token_id`),
  KEY `report_id` (`report_id`),
  CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens_backup`
--

DROP TABLE IF EXISTS `tokens_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens_backup` (
  `token_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `eos` tinyint(1) NOT NULL,
  PRIMARY KEY (`token_id`),
  KEY `report_id` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens_backup`
--

LOCK TABLES `tokens_backup` WRITE;
/*!40000 ALTER TABLE `tokens_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `tokens_tags`
--

DROP TABLE IF EXISTS `tokens_tags`;
/*!50001 DROP VIEW IF EXISTS `tokens_tags`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `tokens_tags` AS SELECT 
 1 AS `token_tag_id`,
 1 AS `token_id`,
 1 AS `base`,
 1 AS `base_id`,
 1 AS `ctag`,
 1 AS `disamb`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tokens_tags_ctags`
--

DROP TABLE IF EXISTS `tokens_tags_ctags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens_tags_ctags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ctag` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ctag_tagset_UNIQUE` (`ctag`,`tagset_id`),
  KEY `ctag` (`ctag`),
  KEY `fk_tagset` (`tagset_id`),
  CONSTRAINT `tokens_tags_ctags_ibfk_1` FOREIGN KEY (`tagset_id`) REFERENCES `tagsets` (`tagset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens_tags_ctags`
--

LOCK TABLES `tokens_tags_ctags` WRITE;
/*!40000 ALTER TABLE `tokens_tags_ctags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens_tags_ctags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens_tags_optimized`
--

DROP TABLE IF EXISTS `tokens_tags_optimized`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens_tags_optimized` (
  `token_tag_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `token_id` bigint(20) NOT NULL,
  `base_id` bigint(20) NOT NULL,
  `disamb` tinyint(1) NOT NULL,
  `ctag_id` int(11) NOT NULL,
  `pos` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `stage` enum('tagger','agreement','final') CHARACTER SET utf8 NOT NULL DEFAULT 'tagger' COMMENT 'Describes stage of chosen tag, tagger- generated by tagger, agreement- set by user, final- set by final user with agreement check',
  PRIMARY KEY (`token_tag_id`),
  KEY `fk_token_id` (`token_id`),
  KEY `FK_tokens_tags_optimized_bases` (`base_id`),
  KEY `ctag_id` (`ctag_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_tokens_tags_optimized_bases` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_token_id` FOREIGN KEY (`token_id`) REFERENCES `tokens` (`token_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tokens_tags_optimized_ibfk_1` FOREIGN KEY (`ctag_id`) REFERENCES `tokens_tags_ctags` (`id`),
  CONSTRAINT `tokens_tags_optimized_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens_tags_optimized`
--

LOCK TABLES `tokens_tags_optimized` WRITE;
/*!40000 ALTER TABLE `tokens_tags_optimized` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens_tags_optimized` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activities`
--

DROP TABLE IF EXISTS `user_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_activities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `started` datetime NOT NULL,
  `ended` datetime NOT NULL,
  `counter` int(11) NOT NULL,
  `login` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`ended`),
  KEY `user_id_2` (`user_id`),
  KEY `ended` (`ended`),
  CONSTRAINT `user_activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activities`
--

LOCK TABLES `user_activities` WRITE;
/*!40000 ALTER TABLE `user_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `screename` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unknown',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clarin_login` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','Inforex Admin','unknown','21232f297a57a5a743894a0e4a801fc3',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_annotation_sets`
--

DROP TABLE IF EXISTS `users_annotation_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_annotation_sets` (
  `user_id` int(11) NOT NULL,
  `annotation_set_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_annotation_sets`
--

LOCK TABLES `users_annotation_sets` WRITE;
/*!40000 ALTER TABLE `users_annotation_sets` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_annotation_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_corpus_roles`
--

DROP TABLE IF EXISTS `users_corpus_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_corpus_roles` (
  `user_id` int(11) NOT NULL,
  `corpus_id` int(11) NOT NULL,
  `role` varchar(32) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`user_id`,`corpus_id`,`role`),
  KEY `user_id` (`user_id`),
  KEY `corpus_id` (`corpus_id`),
  KEY `role` (`role`),
  CONSTRAINT `users_corpus_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_corpus_roles_ibfk_2` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_corpus_roles_ibfk_3` FOREIGN KEY (`role`) REFERENCES `corpus_roles` (`role`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_corpus_roles`
--

LOCK TABLES `users_corpus_roles` WRITE;
/*!40000 ALTER TABLE `users_corpus_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_corpus_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_roles`
--

DROP TABLE IF EXISTS `users_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_roles` (
  `user_id` int(11) NOT NULL,
  `role` varchar(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`role`),
  KEY `user_id` (`user_id`),
  KEY `role` (`role`),
  CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role`) REFERENCES `roles` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_roles`
--

LOCK TABLES `users_roles` WRITE;
/*!40000 ALTER TABLE `users_roles` DISABLE KEYS */;
INSERT INTO `users_roles` VALUES (1,'admin');
/*!40000 ALTER TABLE `users_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wccl_rules`
--

DROP TABLE IF EXISTS `wccl_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wccl_rules` (
  `wccl_rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `corpus_id` int(11) NOT NULL,
  `rules` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`wccl_rule_id`),
  UNIQUE KEY `user_id` (`user_id`,`corpus_id`),
  KEY `user_id_2` (`user_id`),
  KEY `corpus_id` (`corpus_id`),
  CONSTRAINT `wccl_rules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wccl_rules_ibfk_2` FOREIGN KEY (`corpus_id`) REFERENCES `corpora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wccl_rules`
--

LOCK TABLES `wccl_rules` WRITE;
/*!40000 ALTER TABLE `wccl_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `wccl_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `activities_view_users`
--

/*!50001 DROP VIEW IF EXISTS `activities_view_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `activities_view_users` AS select `u`.`screename` AS `screename`,count(0) AS `COUNT(*)`,max(`a`.`datetime`) AS `last_datetime`,`a`.`activity_page_id` AS `activity_page_id`,`a`.`datetime` AS `datetime`,`a`.`ip_id` AS `ip_id`,`a`.`user_id` AS `user_id`,`a`.`corpus_id` AS `corpus_id`,`a`.`report_id` AS `report_id`,`a`.`activity_type_id` AS `activity_type_id`,`a`.`execution_time` AS `execution_time` from (`activities` `a` join `users` `u` on((`a`.`user_id` = `u`.`user_id`))) group by `a`.`user_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `reports_annotations`
--

/*!50001 DROP VIEW IF EXISTS `reports_annotations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `reports_annotations` AS select `ra`.`id` AS `id`,`ra`.`report_id` AS `report_id`,`ra`.`type_id` AS `type_id`,`at`.`name` AS `type`,`ra`.`from` AS `from`,`ra`.`to` AS `to`,`ra`.`text` AS `text`,`ra`.`user_id` AS `user_id`,`ra`.`creation_time` AS `creation_time`,`ra`.`stage` AS `stage`,`ra`.`source` AS `source` from (`reports_annotations_optimized` `ra` left join `annotation_types` `at` on((`at`.`annotation_type_id` = `ra`.`type_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `tokens_tags`
--

/*!50001 DROP VIEW IF EXISTS `tokens_tags`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `tokens_tags` AS select `tt`.`token_tag_id` AS `token_tag_id`,`tt`.`token_id` AS `token_id`,`b`.`text` AS `base`,`tt`.`base_id` AS `base_id`,`tokens_tags_ctags`.`ctag` AS `ctag`,`tt`.`disamb` AS `disamb` from ((`tokens_tags_optimized` `tt` left join `bases` `b` on((`b`.`id` = `tt`.`base_id`))) left join `tokens_tags_ctags` on((`tt`.`ctag_id` = `tokens_tags_ctags`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-11-17  0:31:20
