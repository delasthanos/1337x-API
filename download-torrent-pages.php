<?php 
	// Called by AJAX

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require_once("config.php");
	require_once("src/dbhandler.class.php");
	require_once("src/functions.php");
	require_once("src/ansi.color.class.php"); $color = new Color();
	
	spl_autoload_register(function ($class_name) {
		require_once 'src/'.$class_name.'.class.php';
	});

	if (isset($_POST['imdb'])){
		
		$imdb=strip_tags($_POST['imdb']);
		if (strlen($imdb)>20){
			$imdb=0;
		}
		$imdbSplit=explode("-",$imdb);
		$imdb=$imdbSplit[1];
		print('Downloaded torrent HTML pages for imdb='.$imdb.n);
	} else {
		print('Nothing found. Error.'.n);
		exit();
	}

	//$imdb="tt3659388";
	$folder=$imdb;

	printColor (n.n."\t[!]TEST save torrents files here".n.n,"yellow");

	$findTorrents=new ParseSearch1337x($folder);
	$findTorrents->collectSearchResultsHTML();
	$torrents = $findTorrents->collectTorrentsFromHTML(); // Final array with torrent info for each folder
	if ( is_array($torrents) ){print (n."\t[*]Found: ".count($torrents)." torrents." );}

	$activeTorrents=0;
	foreach ($torrents as $t ):
	
		if ( $t['seeds']>MIN_SEEDS ){++$activeTorrents;}
		
	endforeach;
	print (n."\t[*]Active: ".$activeTorrents." ( seeds>".MIN_SEEDS." )");
	// Print stats
	
	// Download Torrent Pages HTML
	$countDownloaded=0;
	foreach ($torrents as $t ):

		if ( $t['seeds']>MIN_SEEDS ):
			$downloadHTMLTorrent= new DownloadHTMLTorrentPage( $folder, $t ); //$folder==imdb, $t==torrent Array
			$downloadHTMLTorrent->downloadTorrentHTMLPage();
			unset($downloadHTMLTorrent);
		endif;
		//if (++$countDownloaded>2){ printColor(n."Break after 10 torrent pages. Still testing.".n,"red"); break; }

	endforeach; // Foreach torrent


?>
