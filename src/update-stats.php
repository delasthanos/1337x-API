<?php 

	// Called from ajax request at summary page

	require_once("../config.php");
	require_once("dbhandler.class.php");
	require_once("functions.php");	
	
	spl_autoload_register(function ($class_name) {
		require_once ''.$class_name.'.class.php';
	});

	$view=new ViewStatsHTML();
	$view->showTotalStats("AJAX");

?>
