/*CREATE DATABASE 1337x CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;*/

CREATE TABLE `search_results` (
  `imdb` int(11) UNSIGNED NOT NULL,
  `totalPages` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `activePages` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `totalTorrents` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `activeTorrents` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `last_checked` datetime,

  PRIMARY KEY (`imdb`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `search_results_torrents` (
  `imdb` int(11) UNSIGNED NOT NULL,
  `1337x_id` int(11) UNSIGNED NOT NULL,
  `link` MEDIUMTEXT NOT NULL,
  `seeds` MEDIUMTEXT NOT NULL,
  `leeches` MEDIUMTEXT NOT NULL,

  FOREIGN KEY (`imdb`) REFERENCES search_results(imdb),
  UNIQUE INDEX `imdb_1337x_id_match` (`imdb`,`1337x_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

