<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	define("MAIN_CONTROLLER", true);
	
	$action = checkArr($_GET, "action");
	
	switch($action){
		case 'suggest':
			try_access(array("manager"));
			$role = "κουμανταδόρε";
			$breadcrumb = "Πρόταση Προμήθειας";
			$title = "Πρόταση Προμήθειας";
			
			try{
				$products = modelProducts::suggestProduct();
			}catch(QueryError $ex){
				require_once('view/500.php');
			}
			
			require_once('view/header.php');
			$head3 = "Πρόταση Προμήθειας";
			require_once('view/search.php');
			require_once('view/suggestProducts.php');
			break;
		default:
			try_access(array("storekeeper","seller","manager"));
			$role = "αποθηκάριος";
			$breadcrumb = "Αποθέματα";
			$title = "Αποθέματα";
			try{
				$table = ModelProducts::get_activeProducts();
			}catch(QueryError $ex){
				require_once('view/500.php');
			}
			require_once('view/header.php');
			$head3 = "Αποθέματα Προιόντων";
			require_once('view/search.php');
			require_once('view/productTable.php');	
	}
	require_once('view/footer.php');
?>