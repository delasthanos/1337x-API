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
-- Table structure for table `1337xtorrents`
--

DROP TABLE IF EXISTS `1337xtorrents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `1337xtorrents` (
  `1337x_id` int(11) unsigned NOT NULL,
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `titlename` mediumtext NOT NULL,
  `hash` varchar(255) NOT NULL,
  `format_type` varchar(256) DEFAULT NULL,
  `language` varchar(256) DEFAULT NULL,
  `size` varchar(16) DEFAULT NULL,
  `seeds` smallint(5) unsigned NOT NULL,
  `leeches` smallint(5) unsigned NOT NULL,
  `uploader` varchar(256) DEFAULT NULL,
  `uploaded` varchar(256) DEFAULT NULL,
  `checked` varchar(256) DEFAULT NULL,
  `downloads` smallint(5) unsigned NOT NULL,
  `links` mediumtext NOT NULL,
  `images` mediumtext NOT NULL,
  `imdbmatch` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`1337x_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `1337xtorrents`
--

LOCK TABLES `1337xtorrents` WRITE;
/*!40000 ALTER TABLE `1337xtorrents` DISABLE KEYS */;
/*!40000 ALTER TABLE `1337xtorrents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `search_results`
--

DROP TABLE IF EXISTS `search_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `search_results` (
  `summary_id` int(11) unsigned NOT NULL,
  `imdb` varchar(255) NOT NULL DEFAULT '0',
  `1337x_id` int(11) unsigned NOT NULL,
  `link` mediumtext NOT NULL,
  `seeds` smallint(5) unsigned NOT NULL,
  `leeches` smallint(5) unsigned NOT NULL,
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `downloaded` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`imdb`,`1337x_id`),
  KEY `summary_id` (`summary_id`),
  CONSTRAINT `search_results_ibfk_1` FOREIGN KEY (`summary_id`) REFERENCES `search_summary` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search_results`
--

LOCK TABLES `search_results` WRITE;
/*!40000 ALTER TABLE `search_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `search_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `search_summary`
--

DROP TABLE IF EXISTS `search_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `search_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `imdb` varchar(255) NOT NULL DEFAULT '0',
  `totalPages` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `activePages` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `totalTorrents` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `activeTorrents` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_checked` datetime DEFAULT NULL,
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `imdb` (`imdb`)
) ENGINE=InnoDB AUTO_INCREMENT=12936 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search_summary`
--

LOCK TABLES `search_summary` WRITE;
/*!40000 ALTER TABLE `search_summary` DISABLE KEYS */;
/*!40000 ALTER TABLE `search_summary` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
