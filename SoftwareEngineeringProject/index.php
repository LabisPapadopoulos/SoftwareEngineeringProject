<?php
	/*
	 * This is the first file of the project that handles all the requests
	 */

	//XSS SECURITY
		ini_set('session.cookie_httponly', true);

	session_start();
	
	require_once('config/configs.php');
	require_once('controller/functions.php');
	
	if($CONFIG['allow_compression'])
		ob_start();
	
	if($CONFIG['debug']){
		error_reporting(E_ALL | E_NOTICE | E_STRICT);
		ini_set('display_errors', '1');
	}else
		ini_set('display_errors', '0');
	
	//checking the session idle time
	if(!checkArr($_SESSION, "keep_login", true)){	
		if(!checkArr($_SESSION, "last_active"))
			$_SESSION['last_active'] = time() + $CONFIG['max_idle_time'];
		else
			if($_SESSION['last_active'] < time()){
				session_unset();
				session_destroy();
			}else
				$_SESSION['last_active'] = time() + $CONFIG['max_idle_time'];
	}
	
	//XSS SECURITY
	if(!checkArr($_SESSION, "last_ip"))
		$_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
	if($_CONFIG['CHECK_LAST_IP'] && $_SESSION['last_ip'] !== $_SERVER['REMOTE_ADDR']){
		session_unset();
		session_destroy();
		die("invalid session");
	}
	
	//XSS SECURITY
	if($_CONFIG['FORM_TOKENS']){
		if ($_SERVER["REQUEST_METHOD"] == "POST" && !checkArr($_POST, "token", checkArr($_SESSION, "token"))) {
			session_unset();
			session_destroy();
			die("invalid form");
		}
		//NEW TOKEN
		$_SESSION['token'] = sha1(uniqid(mt_rand() + intval("E-SC-Project") , true));
		
	}
	
	//include all model here
	require_once('model/include.php');
	
	//Load the User object in session
	if(checkArr($_SESSION, "user") && $_SESSION['user'] != "user")
		$User = unserialize($_SESSION['user']);
	else
		$User = null;

	//set a default page title
	$title = "Πρόγραμμα Διαχείρησης αποθήκης";

	// Use define constants in order to manipulate the request flow and don't allow 
	// instant call of other files without including the nessesary files first.
	define("INDEX_CONTROLLER", true);
	
	//XSS Security, reject html special characters
	array_walk_recursive($_POST, "my_XSS_SECURE");
	array_walk_recursive($_GET, "my_XSS_SECURE");
	
	/* If user is not logged in prompt them to login */
	if($User === null)
		$page = "login";
	elseif($User !== null && checkArr($_GET, "page", "login")){
		$redirectUrl = "index.php?page=index";
		
		require_once('view/redirect.php');
	}
	elseif(checkArr($_GET, "page"))
		$page = $_GET['page'];
	else
		$page = "index";

	if(isset($_GET['method']) && $_GET['method'] == "ajax")
		require_once('controller/ajax.php');
	else
		switch($page){
			case 'index':
					require_once('controller/index.php');
					break;
			case 'login':
					require_once('controller/login.php');
					break;
			case 'warehouse':
					require_once('controller/warehouse.php');
					break;
			case 'suppliersOrders':
					require_once('controller/suppliersOrders.php');
					break;
			case 'customersOrders':
					require_once('controller/customersOrders.php');
					break;
			case 'logout':
					require_once('controller/logout.php');
					break;
			case 'users':
					require_once('controller/user.php');
					break;
			case 'suppliers':
					require_once('controller/suppliers.php');
					break;
			case 'customers':
					require_once('controller/customers.php');
					break;
			case 'stats':
					require_once('controller/stats.php');
					break;
			case 'pricelist':
					require_once('controller/pricelist.php');
					break;
			case 'profile':
					require_once('controller/profile.php');
					break;
			case 'newSupplier':
					require_once('controller/newSupplier.php');
					break;
			case 'discount':
					require_once('controller/discount.php');
					break;
			default:
					require_once('view/404.php');
					break;
		}
	
?>