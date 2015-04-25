<?php

	$responce = array();
	
	$action = checkArr($_GET, "action");
	
	switch($action){
		case 'getProductBySupplier':
				if(!checkArr($_GET, "id")){
					$responce = array("status" => "fail");
					break;
				}
				try{
					$responce = ModelProducts::get_ProductsBySupplierId($_GET['id']);
				}catch(Exception $ex){
					$responce = array("status" => "fail");
					echo json_encode($responce);
					exit();
				}
				if($responce)
					$responce = array_merge(array("status" => "ok"), $responce);
				break;
		case 'getProductsDetails':
			
				$responce = array("status" => "fail");
				if(checkArr($_GET, "date"))
					$date = $_GET['date'];
				else 
					$date = null;
				
				try{
					$responce = ModelProducts::get_activeProducts($_GET['date'], true);
					
					  //SOME TEST DATA
					/*$json = '{"5":{"id":5,"name":"Coca cola","description":"Zero","metric_units":"\u03a4\u03b5\u03bc\u03ac\u03c7\u03b9\u03bf",
									"market_value":0,"sell_value":5,"total_quantity":0,"available_quantity":0,"supplied_by":1,"supplier":"\u039a\u03c9\u03c3\u03c4\u03ae\u03c2","inOrder":"15000"},
							"2":{"id":2,"name":"Nero 2","description":"Nero mikro","metric_units":"\u03a4\u03b5\u03bc\u03ac\u03c7\u03b9\u03bf",
								"market_value":15000,"sell_value":20,"total_quantity":0,"available_quantity":0,"supplied_by":1,"supplier":"\u039a\u03c9\u03c3\u03c4\u03ae\u03c2","inOrder":"1700"},
							"16":{"id":16,"name":"Tyropites","description":"Me eidiki gemisi","metric_units":"Kommatia","market_value":42,
								"sell_value":4700,"total_quantity":830,"available_quantity":0,"supplied_by":10,"supplier":"Mitsos A","inOrder":"1000"},
							"10":{"id":10,"name":"\u0393\u03b1\u03bb\u03bb\u03b9\u03ba\u03cc\u03c2 \u039a\u03b1\u03c6\u03ad\u03c2","description":"\u039a\u03b1\u03c6\u03ad\u03c2 \u03c6\u03af\u03c4\u03c1\u03bf\u03c5",
								"metric_units":"\u03ba\u03b9\u03bb\u03cc","market_value":1.8,"sell_value":4,"total_quantity":0,"available_quantity":0,"supplied_by":3,"supplier":"\u039a\u03b1\u03c4\u03b5\u03c1\u03af\u03bd\u03b1","inOrder":"0"}
						}';
					$responce = json_decode($json);
					*/
					
				}catch(Exception $ex){
					$responce = array("status" => "fail");
					echo json_encode($responce);
					exit();
				}
				
				break;
		default:
				$responce = array("status" => "fail");
				break;
	}
	
	echo json_encode($responce);
?>