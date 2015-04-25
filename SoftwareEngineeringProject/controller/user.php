<?php
if(!defined("INDEX_CONTROLLER"))
	die("invalid request!");

	try_access(array("admin"));

	$action = checkArr($_GET, "action");
	
	switch($action){
		case 'edit':
				if(!($id = checkArr($_GET, "id")))
					die("invalid user id");
				
				try{
					$target_user = ModelUsers::get_user($id);
				}catch(QueryError $ex){
					require_once('view/500.php');
				}catch(Exception $ex){
					require_once('view/500.php');
				}
					
				$action_url = "index.php?page=users&action=do_edit&id=$id";

				require_once('view/header.php');
				require_once('view/user.php');
				break;
		case 'add':
				$action_url = "index.php?page=users&amp;action=do_add";
			
				try{
					$target_user = new ModelUsers();
				}catch(Exception $ex){
					require_once('view/500.php');
				}

				require_once('view/header.php');
				require_once('view/user.php');
				break;
		case 'do_edit':
				if(!($id = checkArr($_GET, "id")))
					die("invalid user id");
		
				if(!checkArr($_POST, "username"))
					die("empty username");
				
				if(!checkArr($_POST, "type"))
					die("empty type");
					
				try{
					$return = ModelUsers::modifyProfile($_POST['username'], $_POST['type'], checkArr($_POST, "deleted", 1) ? true : false);
				}catch(QueryError $ex){
					require_once('view/500.php');
				}catch(Exception $ex){
					require_once('view/500.php');
				}

				if($return == "fail" ) {
					$failureMsg = "Η επεξεργασία του χρήστη απέτυχε!";
				}
				else {
					$successMsg = "Οι αλλαγές καταχωρήθηκαν";
				}

				try{
					$users = ModelUsers::list_all();
				}catch(QueryError $ex){
					require_once('view/500.php');
				}catch(Exception $ex){
					require_once('view/500.php');
				}
				
				$edit_page_url = "index.php?page=users&action=edit&id=";

				require_once('view/header.php');
				require('view/showMessage.php');
				require_once('view/showAllUsers.php');

				break;
				
		case 'do_add':
			
				$pass = array();
				$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789-#_";
				$alphaLength = strlen($alphabet) - 1;
				for ($i = 0; $i < 10; $i++) {	//generate random password
					$n = rand(0, $alphaLength);
					$pass[] = $alphabet[$n];
				}
				$password = implode($pass);
		
				if(!checkArr($_POST, "username"))
					die("empty username");
				
				if(!checkArr($_POST, "email"))
					die("empty email");
				
				if(!checkArr($_POST, "type"))
					die("empty type");
				

				try{
					ModelUsers::createNewUser($_POST['username'], $password, $_POST['fullname'], 
											  $_POST['vat'], $_POST['phone_number'], $_POST['email'], $_POST['type']);
					
					$to      = $_POST['email'];
					$subject = 'Welcome to e-Supply Chain!';
					$message = '<b>You have successfully signed up!</b><p> Your password is: '.$password;
					$headers = 'From: std07261@gmail.com' . "\r\n" .
						'Reply-To: std07261@gmail.com' . "\r\n" .
						'Content-type: text/html'. "\r\n" . 
						'X-Mailer: PHP/' . phpversion();

					mail($to, $subject, $message, $headers);
					
					$email = $_POST['email'];
					
					$successMsg = "Επιτυχής δημιουργία λογαριασμού! Στάλθηκε email στο $email με τον κωδικό του νέου λογαριασμού."; 

					require_once('view/header.php');
					require('view/showMessage.php');
				}
				catch(EmailAlreadyExists $ex){
					$failureMsg = "Η δημιουργία λογαριασμού απέτυχε! Το email είναι κατοχυρωμένο.";
					require('view/showMessage.php');
				}
				catch(FullnameAlreadyExists $ex){
					$failureMsg = "Η δημιουργία λογαριασμού απέτυχε! Το username είναι κατοχυρωμένο.";
					require('view/showMessage.php');
				}
				catch(QueryError $ex){
					$failureMsg = "Η δημιουργία λογαριασμού απέτυχε!";
					require('view/showMessage.php');
				}
				
		case 'all':
				$head3 = "Προβολή χρηστών";
				try{
					$users = ModelUsers::list_all();
				}catch(QueryError $ex){
					require_once('view/500.php');
				}catch(Exception $ex){
					require_once('view/500.php');
				}
				
				$add_new_url = "index.php?page=users&action=add";
				$edit_page_url = "index.php?page=users&action=edit&id=";
				require_once('view/header.php');
				require_once('view/search.php');
				require_once('view/showAllUsers.php');
				break;
		
		default: 
				require_once('view/header.php');
				require_once('view/404.php');
				break;	
	}
	
	require_once('view/footer.php');

?>