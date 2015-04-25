<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	define("MAIN_CONTROLLER", true);
	
	try_access(array("manager", "storekeeper"));

	list($start, $end) = getStartEndDates();
	$action = checkArr($_GET, "action");
	
	if( checkArr($_GET, "succ", 1) )
		$successMsg = "Η προμήθεια καταχωρήθηκε με επιτυχία";
	elseif( checkArr($_GET, "succ", 2) )
		$successMsg = "Η προμήθεια ενημερώθηκε με επιτυχία";
	elseif(checkArr($_GET, "succ", 3))
		$successMsg = "Επιτυχής διαγραφή παραγγελίας!";
	
	switch($action){
		case 'new': 
					try_access(array("manager"));
					
					/* If there is no ID, just show a list of all suppliers with a button to add an order */
					if( !isset($_GET['id']) || !is_numeric($_GET['id']) ) {
						$head3 = "Νέα προμήθεια";
						try{
							$suppliers = ModelSuppliers::get_suppliers('active');
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$edit_page_url = "index.php?page=suppliersOrders&action=new&id=";
						require_once('view/header.php');
						require('view/showMessage.php');
						require_once('view/search.php');
						require_once('view/newSupplyOrderList.php');
					}
					/* A supplier has been selected */
					else {	
						$supplierID = $_GET['id'];
						try{
							$supplier = ModelSuppliers::get_supplier_by_id($supplierID);
							$products = ModelProducts::get_ProductsBySupplierId($supplierID);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$form_action = "index.php?page=suppliersOrders&amp;action=submit&amp;id=".$supplier->id;
						$form_title = "Νέα Παραγγελία";
						$form_button = "Δημιουργία";
						

						require_once('view/header.php');
						require('view/showMessage.php');
						require_once('view/newSchedulerOrder.php');
					}
					break;
		case 'history':
					try_access(array("manager"));
					
					$head3 = "Ιστορικό προμηθειών";
					try{
						$orders = SupplyOrders::get_orders(null, $start, $end);
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					
					$edit_page_url = "index.php?page=suppliersOrders&action=view&id=";

					require_once('view/header.php');
					require('view/showMessage.php');
					require_once('view/search.php');
					require_once('view/suppliesHistory.php');
					//TODO maybe add a link to only pending supplies
					break;
		case 'view':
					try_access(array("manager", "storekeeper"));
					
					if( !isset($_GET['id'])|| !is_numeric($_GET['id']) )
						die("No id for supply order specified");

					$orderID = $_GET["id"];
					try{
						$products = SupplyOrders::viewOrderDetails($orderID);
						$order = SupplyOrders::viewOrder($orderID);
						$supplier = ModelSuppliers::get_supplier_by_id($order->supplier);
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}

					$supplierID = $order->supplier;
					
					require_once('view/header.php');
					require('view/showMessage.php');
					require_once('view/singleSupplyOrder.php');
					break;
		case 'edit':
					try_access(array("manager"));
					
					if(!checkArr($_GET, "id") || !checkArr($_GET, "order"))
						die("Invalid request");
					
					$supplierID = $_GET['id'];
					try{
						$supplier = ModelSuppliers::get_supplier_by_id($supplierID);
						$products = ModelProducts::get_ProductsBySupplierId($supplierID);
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					
					$form_action = "index.php?page=suppliersOrders&amp;action=submit_edit&amp;id=".$supplier->id."&amp;order=".$_GET['order'];
					$form_title = "Επεξεργασία Παραγγελία";
					$form_button = "Αποθήκευση";

					require_once('view/header.php');
					require('view/showMessage.php');
					require_once('view/newSchedulerOrder.php');
					break;
		case 'submit_edit':						
					try_access(array("manager"));
						
					/* Require that an ID has been given */
					if( !isset($_GET['id']) || !is_numeric($_GET['id']) )
						die("No supplier ID was given when attempting to place a new supply order");
					
					if( !isset($_GET['order']) || !is_numeric($_GET['order']) )
						die("No order ID was given when attempting to edit an order");
					
					$supplierID = $_GET['id'];
					$expectedDate = checkArr($_POST, "expectedDate");
					
					global $array_keys;
					$array_keys = array();
						
					$products_quantity = array_walk($_POST['products_quantity'], "checkEmptyQuantity");
					
					/* Remove all products with quantity <= 0 */
					foreach ($array_keys as $key){
						unset($_POST['products_quantity'][$key]);
						unset($_POST['products_id'][$key]);
					}	
					
					$_POST['products_id'] = array_values($_POST['products_id']);
					$_POST['products_quantity'] = array_values($_POST['products_quantity']);
										
					try{
						SupplyOrders::editOrder($supplierID, $_GET['order'], $expectedDate, $_POST['products_id'], $_POST['products_quantity']);
						$orderID = $_GET['order'];
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					
					$redirectUrl = "index.php?page=suppliersOrders&action=view&id=$orderID&succ=2";
					
					require_once('view/header.php');
					require('view/showMessage.php');
					require_once('view/redirect.php');
					
					break;
		case 'submit':
					try_access(array("manager"));
			
					/* Require that an ID has been given */
					if( !isset($_GET['id']) || !is_numeric($_GET['id']) ) 
						die("No supplier ID was given when attempting to place a new supply order");

					
					$supplierID = $_GET['id'];
					$today = date("Y-m-d");
					$expectedDate = checkArr($_POST, "expectedDate");
						
					global $array_keys;
					$array_keys = array();
					
					$products_quantity = array_walk($_POST['products_quantity'], "checkEmptyQuantity");
				
					/* Remove all products with quantity <= 0 */
					foreach ($array_keys as $key){
						unset($_POST['products_quantity'][$key]);
						unset($_POST['products_id'][$key]);
					}
					
					$_POST['products_id'] = array_values($_POST['products_id']);
					$_POST['products_quantity'] = array_values($_POST['products_quantity']);
					
					/* Empty order? Show the same page again */
					if( !checkArr($_POST, "products_id") ) {
						$failureMsg = "Δεν μπορείτε να καταχωρήσετε άδεια προμήθεια";

						try{
							$supplier = ModelSuppliers::get_supplier_by_id($supplierID);
							$products = ModelProducts::get_ProductsBySupplierId($supplierID);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}

						$form_action = "index.php?page=suppliersOrders&amp;action=submit&amp;id=".$supplier->id;
						$form_title = "Νέα Παραγγελία";
						$form_button = "Δημιουργία";

						require_once('view/header.php');
						require('view/showMessage.php');
						require_once('view/newSchedulerOrder.php');
					}
					/* Not empty - submit it */
					else {
						try{
							$orderID = SupplyOrders::create_new_order($supplierID, $today, $expectedDate, $_POST['products_id'], $_POST['products_quantity']);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$redirectUrl = "index.php?page=suppliersOrders&action=view&id=$orderID&succ=1";

						require_once('view/header.php');
						require('view/showMessage.php');
						require_once('view/redirect.php');
						exit(0);
					}

					break;
		case 'receive':
					try_access(array("storekeeper"));
					
					if(checkArr($_GET, "do", "done") && checkArr($_GET, "id") && checkArr($_POST, "items") && checkArr($_POST, "receipt")){
						try{
							SupplyOrders::receive_order($_GET['id'], date('Y-m-d'), $_POST['items'], $_POST['receipt'], $_POST['desired']);
							//TODO default receive page of storekeeper
							$orders = SupplyOrders::get_orders("active", $start, $end);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$successMsg = "Επιτυχής διαγραφή παραγγελίας!";
					}
					else {
						$orders = SupplyOrders::get_orders("active", $start, $end);
					}
					$head3 = "Ιστορικό προμηθειών";
					$edit_page_url = "index.php?page=suppliersOrders&action=view&id=";

					require_once('view/header.php');
					require('view/showMessage.php');
					require_once('view/search.php');
					require_once('view/suppliesHistory.php');
					break;
		case 'delete':
					try_access(array("manager"));
					
					//TODO Delete the order
					//try SupplyOrders::delete order
					if(!checkArr($_GET, "order"))
						die("invalid request!");
					
					try{
						SupplyOrders::deleteOrder($_GET['order']);
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
								
					$redirectUrl = "index.php?page=suppliersOrders&action=history&succ=3";
						
					require_once('view/header.php');
					require('view/showMessage.php');
					require_once('view/redirect.php');
					exit(0);
					break;
		default:
					require_once('view/404.php');
					break;
	}
	
	require_once('view/footer.php');
?>