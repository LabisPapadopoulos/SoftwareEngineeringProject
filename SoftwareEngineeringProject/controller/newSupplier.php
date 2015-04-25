<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	
	try_access(array("manager"));
	
	$title = "Προσθήκη προμηθευτή";
	
	/* Do we have POST data? If so, create a new supplier */
	if(checkArr($_GET, "action", "new")) {

		try{
			ModelSuppliers::insertNewSupplier($_POST['fullname'], $_POST['vat'], $_POST['location'], $_POST['phone_number'], $_POST['email']);
			$successMsg = "Επιτυχής εισαγωγή νέου προμηθευτή!";
		}catch(SupplierAlreadyExists $ex){
			$failureMsg = "Η δημιουργία νέου προμηθευτή απέτυχε!Ο προμηθευτής είναι ήδη κατοχυρομένος στο σύστημα.";
		}catch(QueryError $ex){
			$failureMsg = "Η δημιουργία νέου προμηθευτή απέτυχε!";	
		}
	}

	require_once('view/header.php');
	require('view/showMessage.php');
	require_once('view/newSupplier.php');
	require_once('view/footer.php')
?>