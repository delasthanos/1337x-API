<?php 
/*
	NOTE: FIX THIS
	git authno and email was not set until recently.
	Fix old commits from root author to delasthanos
	
	App is still under refactoring.
	Breakpoint on search.
	Search results are saved into db now.
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
///////////////////////////////
// Parse command line arguments
if (CLI):
$allowedArgs=['search'=>false, 'save-html-torrents'=>false, 'parse-torrent'=>false];
//if (!array_key_exists($argv[1], $allowedArgs)) commandLineHelp();
if ( count($argv)===2 ){
	switch($argv[1]):
		case "search":
			$allowedArgs['search']=true;
			break;
		case "save-html-torrents":
			$allowedArgs['save-html-torrents']=true;
			break;
		case "parse-torrent":
			$allowedArgs['parse-torrent']=true;
			break;
		default:
		commandLineHelp();	
	endswitch;
} else { commandLineHelp(); }
endif; //CLI
// Parse command line arguments
///////////////////////////////

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
		$saveTorrents->createJSONFromDB($title);
		$saveTorrents->saveTorrents();
		unset($saveTorrents);

	endforeach; // Foreach titles

endif; //search
endif; //CLI
if (CLI)print ("\nDone\n");
?>
