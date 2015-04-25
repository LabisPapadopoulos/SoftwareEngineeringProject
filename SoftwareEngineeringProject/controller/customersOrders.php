<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	define("MAIN_CONTROLLER", true);
	
	$action = checkArr($_GET, "action");

	list($start, $end) = getStartEndDates();
	
	switch($action){
		case 'new':
					try_access(array("seller"));
			
					/* A customer ID has been specified */
					if(checkArr($_GET, "id")) {
						try{
							$customer = Customers::getCustomer($_GET['id']);
							$products = ModelProducts::get_activeProducts(null,true);
							$discounts = Discounts::getDiscounts($_GET['id']);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$form_action = "index.php?page=customersOrders&amp;action=submit&amp;id=".$customer->id;
						$form_title = "Νέα Παραγγελία";
						$form_button = "Καταχώρηση";
						
						require_once('view/header.php');
						require_once('view/newCustomerOrder.php');
					}
					/* No customer ID has been specified - just show a list of all customers */
					else {
						require_once('view/header.php');
						$head3 = "Νέα παραγγελία";
						
						require_once('view/search.php');
						try{
							$customers = Customers::showActiveCustomers();
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						require_once('view/newOrderCustomerList.php');
					}
					break;
		case 'submit':
					try_access(array("seller"));
					
					if(!checkArr($_GET, "id"))
						die("Invalid Request");
					
					global $array_keys;
					$array_keys = array();
						
					if(checkArr($_POST, "products_quantity"))
						$products_quantity = array_walk($_POST['products_quantity'], "checkEmptyQuantity");
					
					/* Remove all products with quantity <= 0 */
					foreach ($array_keys as $key){
						unset($_POST['products_quantity'][$key]);
						unset($_POST['products_id'][$key]);
					}
					
					if(!checkArr($_POST, "products_id")){
						$failureMsg = "Δεν μπορείτε να καταχωρήσετε άδεια παραγγελία";
						
						try{
							$customer = Customers::getCustomer($_GET['id']);
							$products = ModelProducts::get_activeProducts(null,true);
							$discounts = Discounts::getDiscounts($_GET['id']);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$form_action = "index.php?page=customersOrders&amp;action=submit&amp;id=".$customer->id;
						$form_title = "Νέα Παραγγελία";
						$form_button = "Καταχώρηση";
						
						require_once('view/header.php');
						require('view/showMessage.php');
						require_once('view/newCustomerOrder.php');
					}else{
						try{
							if(empty($_POST['comments'])){
								$_POST['comments'] = null;
							}
							CustomerOrders::create_new_order($User->id, $_GET['id'], date('Y-m-d'), 
								$_POST['expectedDate'], $_POST['products_id'], $_POST['products_quantity'], $_POST['comments']);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$redirectUrl = "index.php?page=customersOrders&action=history&id=". $User->id ."&succ=1";

						require_once('view/header.php');
						require_once('view/redirect.php');
						exit(0);
					}
					break;
		case 'edit':
					try_access(array("seller"));
					
					//check if this order is from this seller
					if($User->type == "seller"){
						try{
							$orders = CustomerOrders::get_orders($User->id);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
					
						if(!array_key_exists($_GET['order'], $orders)){
							$failureMsg = "Δεν έχετε δικαιώματα για αυτή τη σελίδα...";
							$redirectUrl = "index.php";
							require_once('view/redirect.php');
							die();
						}
					}
					
					if(!checkArr($_GET, "id") || !checkArr($_GET, "order"))
						die("Invalid request");
					
					try{
						$customer = Customers::getCustomer($_GET['id']);
						$products = ModelProducts::get_activeProducts(null,true);
						$discounts = Discounts::getDiscounts($_GET['id']);
						$comment = CustomerOrders::getComment($_GET['order']);
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					
					$form_action = "index.php?page=customersOrders&amp;action=submit_edit&amp;id=". $customer->id ."&amp;order=". $_GET['order'];
					$form_title = "Επεξεργασία Παραγγελίας";
					$form_button = "Αποθήκευση";
					
					require_once('view/header.php');
					require_once('view/newCustomerOrder.php');
					break;
		case 'submit_edit':
				
					try_access(array("seller"));
					
					//check if this order is from this seller
					if($User->type == "seller"){
						try{
							$orders = CustomerOrders::get_orders($User->id);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
					
						if(!array_key_exists($_GET['order'], $orders)){
							$failureMsg = "Δεν έχετε δικαιώματα για αυτή τη σελίδα...";
							$redirectUrl = "index.php";
							require_once('view/redirect.php');
							die();
						}
					}
					
					if(!checkArr($_GET, "id") || !checkArr($_GET, "order"))
						die("Invalid request");
					
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
						
					if(!checkArr($_POST, "products_id")){
						$failureMsg = "Δεν μπορείτε να καταχωρήσετε άδεια παραγγελία";
						
						require_once('view/header.php');
						require('view/showMessage.php');
					}else{
						try{
							if(empty($_POST['comments'])){
								$_POST['comments'] = ' ';
							}
							CustomerOrders::editOrders($_GET['order'], $_GET['id'], $_POST['expectedDate'], $_POST['products_id'], 
											$_POST['products_quantity'], $_POST['comments']);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
					
						$redirectUrl = "index.php?page=customersOrders&action=history&id=". $User->id ."&succ=2";
					
						require_once('view/header.php');
						require_once('view/redirect.php');
						exit(0);
					}
					break;
		case 'view':
					try_access(array("seller", "storekeeper"));
					
					if(!checkArr($_GET, "id"))
						die("invalid request");
					
					if($User->type == "seller"){
						try{
							$orders = CustomerOrders::get_orders($User->id);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
					
						if(!array_key_exists($_GET['id'], $orders)){
							$failureMsg = "Δεν έχετε δικαιώματα για αυτή τη σελίδα...";
							$redirectUrl = "index.php";
							require_once('view/redirect.php');
							die();
						}
					}
					
					try{
						$order = CustomerOrders::get_order_by_id($_GET['id']);
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					

					require_once('view/header.php');
					require_once('view/singleCustomerOrder.php');
					break;
		case 'history':
					try_access(array("seller"));
					
					if(checkArr($_GET, "succ", 1))
						$successMsg = "Επιτυχής προσθήκη παραγγελίας!";
					elseif(checkArr($_GET, "succ", 2))
						$successMsg = "Επιτυχής ενημέρωση παραγγελίας!";
					elseif(checkArr($_GET, "succ", 3))
						$successMsg = "Επιτυχής διαγραφή παραγγελίας!";
					
					/* A seller ID has been specified - show a list of all orders taken by that seller */
					if(checkArr($_GET, "id")) {
						$sellerID = $_GET['id'];

						if($User->id != $sellerID && $User->type != "admin" )
							die("Δεν έχετε αρκετά δικαιώματα");

						try{
							$orders = CustomerOrders::get_orders($_GET['id'], null, $start, $end);

							/* No orders for this seller - default to all sellers */
							if($User->type == "admin" && $orders == null) {
								unset($sellerID);
								unset($_GET["id"]);

								$orders = CustomerOrders::get_orders(null, null, $start, $end);
								$warningMsg = "Ο πωλητής που επιλέξατε δεν καταχώρησε παραγγελίες στο συγκεκριμένο χρονικό διάστημα. Αντί για αυτό, εμφανίζονται οι παραγγελίες όλων των πωλητών";
							}

						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$sellers = CustomerOrders::getUsers($start, $end);
						$head3 = "Ιστορικό Παραγγελιών";
						
						$edit_page_url = "index.php?page=customersOrders&action=view&id=";

						require_once('view/header.php');
						require('view/showMessage.php');
						require_once('view/search.php');
						require_once('view/customerOrderHistory.php');
					}
					/* No seller ID has been specified - just show all orders */
					else {
						//only for admin
						try_access(array("admin"));
						
						$sellers = CustomerOrders::getUsers($start, $end);
						$head3 = "Ιστορικό Παραγγελιών";
						
						try{
							$orders = CustomerOrders::get_orders(null, null, $start, $end);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$edit_page_url = "index.php?page=customersOrders&action=view&id=";
						require_once('view/header.php');
						require('view/showMessage.php');
						require_once('view/search.php');
						require_once('view/customerOrderHistory.php');
					}
					break;
		case 'send':
					try_access(array("storekeeper"));
					
					if(checkArr($_GET, "id") && checkArr($_GET, "do", "done")){
						try{
							CustomerOrders::send_order($_GET['id'], date('Y-m-d'), $_POST['items'], $_POST['receipt']);						
							CustomerOrders::setComment($_GET['id'], $_POST['comments']);	
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						$successMsg = "Επιτυχής αποστολή παραγγελίας!";
					}
					$head3 = "Ιστορικό παραγγελιών όλων των πωλητών";
					require_once('view/header.php');
					require('view/showMessage.php');
					require_once('view/search.php');
					
					try{
						$orders = CustomerOrders::get_orders(null, null, $start, $end);
					}catch(QueryError $ex){
						require_once('view/500.php');
					}catch(Exception $ex){
						require_once('view/500.php');
					}
					
					$edit_page_url = "index.php?page=customersOrders&action=view&id=";
					require_once('view/customerOrderHistory.php');
					break;
		case 'delete':
					try_access(array("seller"));
					
					if(!checkArr($_GET, "order"))
						die("invalid request!");
					
					if($User->type == "seller"){
						try{
							$orders = CustomerOrders::get_orders($User->id);
						}catch(QueryError $ex){
							require_once('view/500.php');
						}catch(Exception $ex){
							require_once('view/500.php');
						}
						
						if(!array_key_exists($_GET['order'], $orders)){
							$failureMsg = "Δεν έχετε δικαιώματα για αυτή τη σελίδα...";
							$redirectUrl = "index.php";
							require_once('view/redirect.php');
							die();
						}
					}
					
					
					try{ 
						CustomerOrders::deleteOrder($_GET['order']);
					}	
					catch(Exception $ex){
						require_once('view/500.php');
					}
					
					$redirectUrl = "index.php?page=customersOrders&action=history". (checkArr($_GET, "id") ? "&id=".$_GET['id'] : "") ."&succ=3";
						
					require_once('view/header.php');
					require_once('view/redirect.php');
					exit(0);
					break;
		default:
					require_once('view/header.php');
					require_once('view/404.php');
					break;
	}
	
	require_once('view/footer.php');
?>