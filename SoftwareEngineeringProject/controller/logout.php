<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	define("MAIN_CONTROLLER", true);

	session_unset();
	session_destroy();

	try{
		Database::disconnect();
	}catch(Exception $ex){
		require_once('view/500.php');
	}
	
	/* Redirect to index */
	$redirectUrl = "index.php";
	require_once('view/redirect.php');

?>