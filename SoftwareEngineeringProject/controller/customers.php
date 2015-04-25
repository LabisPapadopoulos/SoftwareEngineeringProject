<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");

	try_access(array("seller"));
	
	$title = "Πελάτες";

	$action = checkArr($_GET, "action");
	
	switch($action){
		/* Show page to fill-in details about a customer */
		case 'add':
			require_once('view/header.php');
			require_once('view/newCustomer.php');
			break;

		/* User is submitting POST data about a new customer */
		case 'submit':
			try{
				Customers::insertNewCustomer($_POST['fullname'], $_POST['vat'], $_POST['location'], $_POST['phone_number'], $_POST['email'], $User->id);
				$successMsg = "Επιτυχής εισαγωγή νέου χρήστη!";
			}catch(CustomerAlreadyExists $ex){
				$failureMsg = "Η δημιουργία νέου χρήστη απέτυχε!Ο χρήστης είναι ήδη κατοχυρομένος στο σύστημα.";
			}catch(QueryError $ex){
				$failureMsg = "Η δημιουργία νέου χρήστη απέτυχε!";	
			}
			catch(Exception $ex){
				$failureMsg = "Η δημιουργία νέου χρήστη απέτυχε!";	
			}
			
			require_once('view/header.php');
			require('view/showMessage.php');
			require_once('view/newCustomer.php');
			require_once('view/footer.php');
			break;

		/* No action has been specified, just view a customer */
		default:

			/* An id has been specified, so show details about a single customer */
			if( $customerID = checkArr($_GET, "id")) {

				if(checkArr($_GET, "succ"))
					$successMsg = "Οι αλλαγές αποθηκεύτηκαν";
		
				try{
					$customer = Customers::getCustomer($customerID);
					//TODO BUG, the function returns the orders for sellers id, while here we need for customer's id
					$pendingOrders = CustomerOrders::get_orders(null, "incompleted", null, null, $customerID);
					$discounts = Discounts::getDiscounts($customerID);
				}catch(NotValidUserID $ex){
					$ex = new NotValidUserID("Μη έγκυρος χρήστης");
					require_once('view/500.php');
				}catch(QueryError $ex){
					require_once('view/500.php');
				}catch(Exception $ex){
					require_once('view/500.php');
				}

				require_once('view/header.php');
				require_once("view/singleCustomer.php");
			}
			/* No id has been specified - display the full list of customers */
			else {
				try{
					$persons = Customers::showActiveCustomers();
				}catch(QueryError $ex){
					require_once('view/500.php');
				}catch(Exception $ex){
					require_once('view/500.php');
				}
				$add_new_url = "index.php?page=customers&amp;action=add";
				$head3 = "Λίστα πελατών";
				
				require_once('view/header.php');
				require_once('view/search.php');
				$edit_page_url = "index.php?page=customers&id=";
				require_once("view/suppliers-customers.php");
			}
	}
	require_once('view/footer.php');
?>