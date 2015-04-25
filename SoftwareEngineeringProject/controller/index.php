<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	define("MAIN_CONTROLLER", true);
	
	$title = "Αρχική σελίδα";
	
	require_once('view/header.php');
	require_once('view/index.php');
	require_once('view/footer.php')
?>