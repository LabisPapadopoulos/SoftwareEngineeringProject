<?php
if(!defined("INDEX_CONTROLLER"))
	die("invalid request!");
define("MAIN_CONTROLLER", true);

try_access(array("admin"));

$action = checkArr($_GET, "action");

require_once('view/header.php');

switch($action){
	case 'view':
			if(!checkArr($_GET, "id"))
				die("No ID specified");

			$id = $_GET["id"];
			try{
				$discounts = Discounts::getDiscounts($id);
				$customer = $discounts->customer;
				$products = ModelProducts::viewPricelist();
			}catch(QueryError $ex){
				require_once('view/500.php');
			}catch(Exception $ex){
				require_once('view/500.php');
			}
			
			require_once("view/discounts.php");

			break;
	case 'submit':
			if(!checkArr($_GET, "id") || !checkArr($_POST, "products") || !checkArr($_POST, "discounts"))
				die("Invalid request");

			foreach($_POST["discounts"] as $key => $value) 
				$discounts[$_POST["products"][$key]] = $value/100;
			
			try{
				Discounts::makeNewDiscounts($_GET["id"], $_POST["products"], $discounts);
			}catch(QueryError $ex){
				require_once('view/500.php');
			}catch(Exception $ex){
				require_once('view/500.php');
			}

			$redirectUrl = "index.php?page=customers&id=". $_GET["id"] ."&succ=1";
			require_once("view/redirect.php");

			break;
	default:	
			require_once('view/404.php');
			break;
}

require_once('view/footer.php');

?>