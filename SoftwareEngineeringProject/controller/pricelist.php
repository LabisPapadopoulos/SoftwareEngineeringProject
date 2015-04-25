<?php
if(!defined("INDEX_CONTROLLER"))
	die("invalid request!");
define("MAIN_CONTROLLER", true);

try_access(array("admin"));

$title = "Τιμοκατάλογος";

require_once('view/header.php');
$show_list = true;

if($action = checkArr($_GET, "action")){
	switch($action){
		case 'add_item':
					$edit = false;
					require_once('view/add-editItem.php');
					$show_list = false;
					break;
		case 'add_done':
					try{
						ModelProducts::add_item($_POST['name'], $_POST['description'], $_POST['metric'], $_POST['market_value'], $_POST['sell_value'], $_POST['quantity'], $_POST['supplier'], $_POST['available_quantity'], $_POST['limit']);
						$successMsg = "Επιτυχής προσθήκη αντικειμένου!";
					}catch(ProductAlreadyExists $ex){
						$failureMsg = "Αποτυχία Δημιουργίας νέου προϊόντος. Το προϊόν ήδη υπάρχει!";
					}catch(QueryError $ex){
						$failureMsg = "Αποτυχία Δημιουργίας νέου προϊόντος. Το προϊόν ήδη υπάρχει!";
					}
					
					require('view/showMessage.php');
					break;
		case 'edit':
					$edit = true;
					try{
						$product = ModelProducts::get_product_by_id($_GET['id']);
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					require_once('view/add-editItem.php');
					$show_list = false;
					break;
		case 'edit_done':
					try{
						ModelProducts::editProduct($_POST['ID'], $_POST['description'], $_POST['metric'], $_POST['quantity'], 
											       $_POST['available_quantity'], $_POST['supplier'], $_POST['limit']);
					}
					catch(SuppliersDoesNotExists $ex){
						$product = ModelProducts::get_product_by_id($_POST['ID']);
						$edit = true;
						$failureMsg = 'Δεν υπάρχει προμηθευτής με το όνομα '.$_POST['supplier']; 
						require('view/showMessage.php');
						require_once('view/add-editItem.php');
						exit();
					}
					catch(QueryError $ex){
						require('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					
					try{
						ModelProducts::editPricelist($_POST['ID'], $_POST['market_value'], $_POST['sell_value']);
					}
					catch(QueryError $ex){
						require('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					
					$successMsg = "Επιτυχής επεξεργασία αντικειμένου!";
					require('view/showMessage.php');
					break;
	}
}

if($show_list){
	try{
		$pricelist = ModelProducts::viewPricelist();
	}catch(Exception $ex){
		require_once('view/500.php');
	}
	$head3 = "Τιμοκατάλογος";
	$add_new_url = "index.php?page=pricelist&amp;action=add_item";
	$edit_page_url = "index.php?page=pricelist&action=edit&id=";
	require_once('view/search.php');
	require_once('view/pricelist.php');
}
require_once('view/footer.php');
?>