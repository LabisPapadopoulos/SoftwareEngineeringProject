<?php
    if(!defined("INDEX_CONTROLLER"))
        die("invalid request!");
    define("MAIN_CONTROLLER", true);

    /* We have POST data - user has sent their credentials */
    if(checkArr($_POST, "form-submit", "login")){

		try{
			$ret = ModelUsers::login( $_POST['username'], $_POST['password'] );
		}
		catch(QueryError $ex){
			$failureMsg = "Παρουσιάστηκε πρόβλημα στον Server";
			require_once('view/login.php');
			exit();
		}catch(Exception $ex){
			require_once('view/500.php');
		}
		
		if(checkArr($_POST, "keep_login", 1))
			$_SESSION['keep_login'] = true;
		else
			$_SESSION['keep_login'] = false;
		
		/* Login was successful - redirect to index */
		if( isset($ret) ) {
			$_SESSION['user'] = serialize($ret);
			if($redirect = checkArr($_GET, "redirect"))
				$redirectUrl = "index.php?" . unserialize(base64_decode($redirect));
			else
				$redirectUrl = "index.php?page=index";

			require_once('view/redirect.php');
			exit();
		}
		/* Login not successful - show an error message and let user retry */
		else{
			$failureMsg = "Τα στοιχεία εισόδου που εισάγατε δεν είναι σωστά";
			require_once('view/login.php');
			exit();
		}
		
    }elseif(checkArr($_GET, "action", "forgot-password")){
        if(checkArr($_POST, "form-submit", "forgot")){

            try{
                ModelUsers::createConfirmationLink($_POST['email']);
                $successMsg = 'Ένα μήνυμα στάλθηκε στο email σας';
                require_once('view/forgot-password.php');
            }catch(EmailNotExists $ex){
                $failureMsg = 'Tο email δεν χρησιμοποιείται από κανένα χρήστη';
                require_once('view/forgot-password.php');
            } catch(SendEmailFail $ex){
                $failureMsg = 'Παρουσιάστηκε πρόβλημα στην αποστολή του νέου password';
                require_once('view/forgot-password.php');
            }catch(QueryError $ex){
                require_once('view/500.php');
            }

        }elseif(checkArr($_GET, "create", "new")){

            try{
                ModelUsers::retrievePassword($_GET['key']);
                $successMsg = 'Ο νέος κώδικος στάλθηκε στο email σας';
            }catch(ValidationLinkNotExists $ex){
                $failureMsg = 'To link δεν είναι έγκυρο';
            } catch(ValidationPeriodHashExpired $ex){
                $failureMsg = 'Η περιόδος παραλαβής νέου κωδικού έχει λήξει';
            } catch(SendEmailFail $ex){
                $failureMsg = 'Παρουσιάστηκε πρόβλημα στην αποστολή του νέου password';
            } catch(QueryError $ex){
                $failureMsg = 'Παρουσιάστηκε πρόβλημα στον Server';
            } 
            require_once('view/login.php');
            // Links are like: index.php?page=login&action=forgot-password&create=new&key=#hash#
        }else{
            $title = "Ξέχασες τον κωδικό σου;";
            require_once('view/forgot-password.php');
        }
    }else{

        $title = "Είσοδος";
        require_once('view/login.php');
    }
?>