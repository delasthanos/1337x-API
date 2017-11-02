<?php 
	// Called by AJAX
exit("DEPRECATED");
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
	$title['imdb']=$imdb;
	
	$saveTorrents=new SaveTorrents1337x();
	$saveTorrents->downloadTorrents($title);
	$saveTorrents->createJSON($title);
	var_dump($title);
	$saveTorrents->saveTorrents();
?>
