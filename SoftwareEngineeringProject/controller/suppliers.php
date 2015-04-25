<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	
	try_access(array("manager"));
	
	$title = "Προμηθευτές";

	if(checkArr($_GET, "action", "edit") && checkArr($_GET, "id")){
		
		try{
			$supplier = ModelSuppliers::get_supplier_by_id($_GET['id']);
		}catch(Exception $ex){
			require_once('view/500.php');
		}
		
		require_once("view/header.php");
		require_once('controller/newSupplier.php');
		require_once("view/footer.php");
		exit();
	
	}elseif(checkArr($_GET, "action", "submit_edit") && checkArr($_GET, "id")){
		//submit the edit data
	}
	/* An id has been specified, so show details about a single supplier */
	if( $supplierID = checkArr($_GET, "id")) {
		try{
			$supplier = ModelSuppliers::get_supplier_by_id($supplierID);
			$pendingOrders = SupplyOrders::get_orders_by_id($supplierID, "incompleted");
			$table = ModelProducts::get_ProductsBySupplierId($supplierID);
		}catch(QueryError $ex){
			require_once('view/500.php');
		}catch(Exception $ex){
			require_once('view/500.php');
		}
		
		$head3 = "Προϊόντα προμηθευτή";
		require_once("view/header.php");
		require_once("view/singleSupplier.php");
		require_once('view/search.php');
		require_once("view/productTable.php");
	}elseif(checkArr($_GET, "delete")){
		try{
			ModelSuppliers::deleteSupplier($_GET['delete']);
		}catch(QueryError $ex){
			require_once('view/500.php');
		}catch(Exception $ex){
			require_once('view/500.php');
		}
		
		$redirectUrl = "index.php?page=suppliers";
		require_once("view/header.php");
		require_once('view/redirect.php');
	
	/* No id has been specified - display the full list of suppliers */
	}else {
		try{
			$persons = ModelSuppliers::get_suppliers();
		}catch(QueryError $ex){
			require_once('view/500.php');
		}catch(Exception $ex){
			require_once('view/500.php');
		}
	
		$add_new_url = "index.php?page=newSupplier";
		$head3 = "Λίστα προμηθευτών";
		$edit_page_url = "index.php?page=suppliers&id=";
		require_once("view/header.php");
		require_once('view/search.php');
		require_once("view/suppliers-customers.php");
	}

	require_once("view/footer.php");

?>