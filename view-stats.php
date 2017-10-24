<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="assets/view-stats.js"></script>
<link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<h2>Stats: 1337x.search_summary</h2>
<?php 

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require_once("config.php");
	require_once("src/dbhandler.class.php");
	require_once("src/functions.php");	
	
	spl_autoload_register(function ($class_name) {
		require_once 'src/'.$class_name.'.class.php';
	});

	$view=new ViewStatsHTML();
	
	if ( isset($_GET['imdb']) ){
		$imdb=strip_tags($_GET['imdb']);
		print ("Showing torrent results imdb: ".$imdb);
		$view->viewResults($imdb);		
	}
	else {

		$view->showStatsSummary();
		$view->viewStats();
	}
	
?>
</body>
</html>
