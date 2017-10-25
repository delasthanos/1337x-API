<?php 
// Global Paramaters
// example of 1337x https://1337x.to/category-search/Independence Day Resurgence/Movies/1/;
// NOTE: Maybe you should refactor Search1337.class to work for TV category too.
//define("CATEGORY","Movies"); // 1337x Category used below to set othr defines
define("CATEGORY","Movies"); // 1337x Category used below to set othr defines
define( "TORRENT_URL_PREFIX", "https://1337x.to");
define( "SEARCH_URL_START", "https://1337x.to/category-search/");
define( "HTML", true);
define( "JSON", false);

switch(CATEGORY):

	case ("Movies"):
		define( "SEARCH_URL_END", "/Movies/");  // Warning add a number at the end when performing search | Check class Search1337x for details
		define("HTML_SEARCH_FILES_PATH","Movies"); // Folder to save and parse HTML files for each movie. Without trailing slash.
		define("HTML_TORRENTS_FILES_PATH","MoviesTorrentsHTML"); // Folder to save and parse HTML torrent files.
		break;

	case ("TV"): 
		define( "SEARCH_URL_END", "/TV/");  // Warning add a number at the end when performing search | Check class Search1337x for details
		define("HTML_SEARCH_FILES_PATH","TV"); // Folder to save and parse HTML torrent files.
		define("HTML_TORRENTS_FILES_PATH","TVTorrentsHTML"); // Folder to save and parse HTML torrent files.		
		break;

endswitch;
define("WAIT_SECONDS", 3.5); // Wait between each search request for titles. Otherwise IP gets banned. Read NOTES for details.
define("WAIT_SECONDS_RESULTS", 2.5); // Wait between search pages. 1,2,3 ...
define("MIN_SEEDS", 1); // Number of seeds on 1337x to start ignoring torrents and results pages
define("MAX_RESULTS_PAGES", 15 ); // Max results pages to fetch. 1,2,3 ... for safety. Maybe increase this value for TvShows
define("n","\n"); // Folder to save and parse HTML files for each movie. Without trailing slash.

// Database configuration
define( "DB_HOST", "localhost");
define( "DB_USER", "imdb");
define( "DB_PASSWORD", "imdb");
define( "DB_NAME", "imdb");
?>
