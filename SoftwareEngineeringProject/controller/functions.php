<?php
	function checkArr(&$array, $key, $value = null){
		if(is_null($array) || !isset($array) || empty($array))
			return null;
		
		if(isset($array[$key]) && !empty($array[$key]))
		{
			if($value == null)
				return $array[$key];
			else 
				return $array[$key] == $value;
			//TODO here is the problem of new order the === is failing when called checkArr($_POST, "hidden", 1)
		}
		else
			return null;
	}
	
	function checkVar(&$var, $value = null){
		if(is_null($var))
			return null;
		
		if(isset($var) && !empty($var))
		{
			if($value == null)
				return $var;
			else
				return $var == $value;
		}
		else
			return null;
	}
	
	function have_access($access_need){
		global $User;
		
		if($User->type == "admin")
			return true;
		
		switch($access_need){
			
			/* case 'admin':
			case 1:
					return ($User->type == "admin");
					break;
					 */
			case 'manager':
			case 2:
					return ($User->type == "manager");
					break;

			case 'seller':
			case 3:
					return ($User->type == "seller");
					break;
					
			case 'storekeeper':
			case 4:
					return ($User->type == "storekeeper");
					break;
		}
		return false;
	}
	
	function checkEmptyQuantity(&$item, $key){
		if( $item <= 0 ){
			global $array_keys;
			array_push($array_keys, $key);
		}
	}
	
	function my_XSS_SECURE(&$item, $key){
		$item = htmlspecialchars($item, ENT_QUOTES, "UTF-8");
	}
	
	function try_access($role){
		foreach($role as $user)
			if(have_access($user))
				return true;
		
		$failureMsg = "Δεν έχετε δικαιώματα για αυτή τη σελίδα...";
		$redirectUrl = "index.php";
		require_once('view/redirect.php');
		die();
	}

	function getStartEndDates() {
		$start = checkArr($_GET, "start");
		$end = checkArr($_GET, "end");

		/* check if there is no day-range */
		if($start && $end && strtotime($end) - strtotime($start) <= 0){
			$end = null;
			$start = null;
		}
					
		/* If $start is not set, set it as 1 week ago */
		if($start == null)
			$start = date("Y-m-d",strtotime("-1 week"));

		/* If $end is not set, set it as today */
		if($end == null)
			$end = date("Y-m-d");

		return array($start, $end);
	}
	
	function to_utf8($in){
		// if (is_array($in)) {
			// foreach ($in as $key => $value) {
				// $out[to_utf8($key)] = to_utf8($value);
			// }
		// } elseif(is_string($in)) {
			// if(mb_detect_encoding($in) != "UTF-8")
				// return utf8_encode($in);
			// else
				// return $in;
		// } else {
			// return $in;
		// }
		// return $out;
		return $in;
	}
	
?>