<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	
	$title = "Επεξεργασία προφίλ";

	/* Do we have POST data? If so, update the fields */
	if(checkArr($_GET, "action", "submit")) {

		if( $_POST['password'] != $_POST['confirm'] ){
			$failureMsg = "Οι κωδικοί δεν συμπίπτουν";
		} else {
			try{
				ModelUsers::editProfile( $User->username, $_POST['password'], $_POST['fullname'], $_POST['vat'],
			  								$_POST['phone_number'], $_POST['email'] );
				$successMsg = "Οι αλλαγές σας αποθηκεύτηκαν.";
			}catch(QueryError $ex){
				require_once('view/500.php');
			}catch(Exception $ex){
				$failureMsg = $ex->getMessage();
			}
		}


		/* Update session information with new data */
		if( $_POST['email'] != '' )
			$User->email = $_POST['email'];
		
		if( $_POST['fullname'] != '' )
			$User->fullname = $_POST['fullname'];

		if( $_POST['vat'] != '' )
			$User->vat = $_POST['vat'];

		if( $_POST['phone_number'] != '' )
			$User->phone_number = $_POST['phone_number'];

		$_SESSION["user"] = serialize($User);
	}

	require_once('view/header.php');
	require_once('view/profile.php');
	require_once('view/footer.php')
?>