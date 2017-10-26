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
	`seeds` SMALLINT UNSIGNED NOT NULL NOT NULL,
	`leeches` SMALLINT UNSIGNED NOT NULL NOT NULL,
  `category` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `downloaded` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,

  PRIMARY KEY `imdb_1337x_id_match` (`imdb`,`1337x_id`),  
  FOREIGN KEY (`summary_id`) REFERENCES search_summary(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*
UNIQUE INDEX `imdb_1337x_id_match` (`imdb`,`1337x_id`),
*/

/*
      "details": {
       ok "Category": "Movies",
       ok "Type": "HD",
       ok "Language": "English",
       ok "Total-size": "96.6 GB",
       ok "Uploaded-By": " ZMachine95",
       ok "Downloads": "565",
       ok "Last-checked": "21 hours ago",
       ok "Date-uploaded": "2 years ago",
       ok "Seeders": "3",
       ok "Leechers": "2"
      },
*/
/* Note: some torrents may exist in results more than one. Check match with combination key imdb_1337x_id_match in search_results table */
/*

	Foreign key for 1337x_id not working
171026 10:58:48 Error in foreign key constraint of table 1337x/1337xtorrents:
FOREIGN KEY (`1337x_id`) REFERENCES search_results(`1337x_id`)  ) ENGINE=InnoDB DEFAULT CHARSET=utf8:
Cannot find an index in the referenced table where the
referenced columns appear as the first columns, or column types
in the table and the referenced table do not match for constraint.
Note that the internal storage type of ENUM and SET changed in
tables created with >= InnoDB-4.1.12, and such columns in old tables
cannot be referenced by such columns in new tables.
See http://dev.mysql.com/doc/refman/5.5/en/innodb-foreign-key-constraints.html
for correct foreign key definition.

FOREIGN KEY (`1337x_id`) REFERENCES search_results(`1337x_id`)

*/
CREATE TABLE `1337xtorrents` (

	`1337x_id` int(11) UNSIGNED NOT NULL,
	`category` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
	`titlename` MEDIUMTEXT NOT NULL,
	`hash` varchar(255) NOT NULL,
	`format_type` varchar(256),
	`language` varchar(256),
	`size` varchar(16),
	`seeds` SMALLINT UNSIGNED NOT NULL NOT NULL,
	`leeches` SMALLINT UNSIGNED NOT NULL NOT NULL,
	`uploader` varchar(256),
	`uploaded` varchar(256),
	`checked` varchar(256),
	`downloads` SMALLINT UNSIGNED NOT NULL NOT NULL,
	`links` MEDIUMTEXT NOT NULL,
	`images` MEDIUMTEXT NOT NULL,
	`imdbmatch` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,

	PRIMARY KEY (`1337x_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;
