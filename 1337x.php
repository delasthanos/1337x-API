<?php 
/*
	NOTE: FIX THIS
	git auth and email was not set until recently.
	Fix old commits from root author to delasthanos

	-Results from crawling are saved into HTML files and in database, so that you can run each step on a different server.(Search, Download torrents, Import)

	-You have to always get a titles object from the database to proceed. Search by keyword is not accepred as an argument.To search for as single title add a mysql query with LIKE "% movie name %" insdide ImdbList class.

	-Use two main factory classes:
	
	  --Search1337x class to search for an array of titles.
	  --SaveTorrents1337x class to parse search pages, download torrent pages, parse them and import them to db.
	
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (php_sapi_name()!=='cli'){define("CLI", false);}
else if (php_sapi_name()==='cli'){define("CLI", true);}
// Include classes and functions
require_once("config.php");
require_once("src/dbhandler.class.php");
require_once("src/ansi.color.class.php"); $color = new Color();
require_once("src/functions.php");
spl_autoload_register(function ($class_name) {
    require_once 'src/'.$class_name.'.class.php';
});

if (CLI) system('clear');

// Parse command line arguments
if (CLI):
$allowedArgs=['search'=>false];
//if (!array_key_exists($argv[1], $allowedArgs)) commandLineHelp();
if ( count($argv)===2 ){
	switch($argv[1]):
		case "search":
			$allowedArgs['search']=true;
			break;
		default:
		commandLineHelp();	
	endswitch;
} else { commandLineHelp(); }
endif; //CLI

/*
 *  Search results foreach movie from 1337x
 *  Save HTML results pages
 */
if (CLI):
if ($allowedArgs['search']):

	// Search1337x | Find how many results pages exist for each title and save them to its own folder, named with imdb code
	// Maybe reset the variables inside the class Search1337x rather than destroying it with unset(). Check for memory usage.
	// Main Loop | Might take a while according to results from ImdbList class | 

	printColor (n."Searching ... ".n, "white+bold");
	printColor (CATEGORY.n, "white+bold");
	
	$ImdbList = new ImdbList();

	if (CATEGORY=="Movies"): $titles = $ImdbList->getMoviesList();
	elseif (CATEGORY=="TV"): $titles = $ImdbList->getTvshowsList();
	endif;

	// Iterate Movies or TvShows names to search and collect information
	foreach ( $titles as $title ):

		// Search has moved inside Search1337x factory class.
		$search= new Search1337x();
		$search->search($title);
		unset($search);
		
		// Parse search, collect torrents , download html pages and save to db has moved inside SaveTorrents1337x factory class
		$saveTorrents=new SaveTorrents1337x();
		$saveTorrents->downloadTorrents($title);
		// Refactoring: save torrents out of imdb folder		
		//$saveTorrents->createJSON($title);
		$saveTorrents->collectAndParseTorrents($title);
		$saveTorrents->saveTorrents();
		unset($saveTorrents);

	endforeach; // Foreach titles

endif; //search
endif; //CLI
if (CLI)print ("\nDone\n");
?>
