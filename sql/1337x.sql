/*CREATE DATABASE 1337x CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;*/

CREATE TABLE `search_summary` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `imdb` varchar(255) NOT NULL DEFAULT '0',
  `totalPages` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `activePages` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `totalTorrents` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `activeTorrents` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `last_checked` datetime,
  `category` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`),
  UNIQUE INDEX (`imdb`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `search_results` (
  `summary_id` int(11) UNSIGNED NOT NULL,
  `imdb` varchar(255) NOT NULL DEFAULT '0',
  `1337x_id` int(11) UNSIGNED NOT NULL,
  `link` MEDIUMTEXT NOT NULL,
  `seeds` MEDIUMTEXT NOT NULL,
  `leeches` MEDIUMTEXT NOT NULL,
  `category` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `downloaded` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,

  UNIQUE INDEX `imdb_1337x_id_match` (`imdb`,`1337x_id`),
  FOREIGN KEY (`summary_id`) REFERENCES search_summary(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

