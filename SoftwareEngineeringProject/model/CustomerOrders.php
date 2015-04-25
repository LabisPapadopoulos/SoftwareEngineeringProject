<?php

class CustomerOrders{

	public $id;
	public $order_date;
	public $expected_date;
	public $receipt_date;
	public $status;
	public $comment;
	public $seller_id;
	public $seller_name;
	public $customer_id;
	public $customer_name;
	
	//CLASS OBJECTS DEFINITION
	public $customer;
	public $seller;
	public $products;
	
	
	
	//function which edit an existed order
	public static function editOrders($orderID, $customerID = null, $expected_date, $productsID, $quantity, $comments=null, $modified = true){
	
		$connection = Database::createNewConnection();
		
		$wishlist = new Wishlist();
		$wishOrder = false;
		
		if($modified == true){
			$new_state = 'modified';
		}
		else{
			$new_state = 'cancelled';
		}
		
		if(!$connection->autocommit(false)){
			throw new QueryError();
		}
		
		if(!$connection->query("LOCK TABLES CustomerOrderDetail WRITE, CustomerOrder WRITE, Products WRITE, Products AS Product1 READ,
								Products AS Product2 READ, Products AS Product3 READ, SupplyOrder READ, SupplyOrderDetail READ, Suppliers READ, 
								Discounts READ, Wishlist READ")) {
			$connection->autocommit(true);
			throw new QueryError();
		}

		$query = "SELECT `product` FROM CustomerOrderDetail WHERE `order`=? ORDER BY `product`";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->bind_param('i', $orderID)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->execute()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->store_result()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->bind_result($productID)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}

		$oldProducts = array();
		$i = 0;
		while($result->fetch()){
			$oldProducts[$i] = $productID;
			$i++;
		}	
		
		$sortedQuantity = array();
		$i = 0;
		foreach($productsID as $product){
			$sortedQuantity[$product] = $quantity[$i];
			$i++;
		}
		
		sort($productsID);
		
		$oldSize = count($oldProducts);
		$newSize = count($productsID);
		
		$query = "SELECT `state` FROM CustomerOrder WHERE `id`=?";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->bind_param('i', $orderID)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
		}
		
		if(!$result->execute()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->store_result()){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->bind_result($state)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		$result->fetch();
		
		$i = 2;
		$bind_names[0] = 's';
		$bind_names[1] = &$new_state;
		
		$query = "UPDATE CustomerOrder SET `state`=?";
		
		if($expected_date != ''){
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $expected_date;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" ,`expected_date`=?";
		}
		if($customerID != null){
			$bind_names[0].='i';
			$bind_name = 'bind' . $i;
            $$bind_name = $customerID;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" ,`customer`=?";
		}
	
		if($comments != null){	
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
			if($comments == ' '){	
				$comments = null;			
			}
            $$bind_name = $comments;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" ,`comments`=?";
		}		
		$query.=" WHERE `id`=?";
		$bind_names[0].='i';
		$bind_name = 'bind' . $i;
        $$bind_name = $orderID;
        $bind_names[$i] = &$$bind_name;
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
	
		call_user_func_array(array($result,'bind_param'),$bind_names);	
			
		if(!$result->execute()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		$sql= "SELECT IFNULL(SUM(InOrder.`quantity`),0) AS `order_quantity`, IFNULL(Product1.`reservedOrder_quantity`,0) AS `reservedOrder_quantity`, 
                      IFNULL(Product1.`available_quantity`, 0) AS `available_quantity`, ExistedOrder.`modified_quantity` AS `previousQuantity`, 
					  ExistedOrder.`reservedQuantity` AS `reservedQuantity`, Product1.`market_value` AS `market_value`, 
					  Product1.`sell_value`-Product1.`sell_value`*IFNULL(Discount.`discount`, 0) AS `final_price`
			   FROM Products AS Product1 LEFT JOIN(SELECT `product`, `quantity` FROM SupplyOrder, SupplyOrderDetail  
                                                WHERE `order`=SupplyOrder.`id` AND `state`='incompleted'  AND `expected_date`!='NULL'";
		if($expected_date != ''){
			$sql.=" AND `expected_date` < ?";
		}										
		$sql.=") AS InOrder ON Product1.`id` = InOrder.`product`, Suppliers, Products AS Product2 LEFT JOIN(SELECT `modified_quantity`, 
		       `reservedQuantity`, `product` FROM CustomerOrderDetail WHERE CustomerOrderDetail.`order`=?) AS ExistedOrder
               ON Product2.`id` = ExistedOrder.`product`, Products AS Product3 LEFT JOIN(SELECT `product`, `discount` FROM Discounts
               WHERE Discounts.`customer`=?) AS Discount ON Discount.`product`=Product3.`id` WHERE Product1.`status`='active' 
			   AND Suppliers.`id`=Product1.`supplied_by` AND Product1.`id`=? AND Product1.`id`=Product2.`id` AND Product1.`id`=Product3.`id`";
			   
		if($state == 'incompleted'){
			$update = "UPDATE CustomerOrderDetail SET `modified_quantity`=?, `reservedQuantity`=?, `quantity`=? WHERE `order`=? AND product=?";
		}
		else{
			$update = "UPDATE CustomerOrderDetail SET `modified_quantity`=?, `reservedQuantity`=? WHERE `order`=? AND product=?";
		}
		
		$add = "INSERT INTO CustomerOrderDetail (`order`,`product`,`quantity`,`modified_quantity`,`reservedQuantity`, `market_value`,
												 `sell_value`) VALUES (?,?,?,?,?,?,?)";
		$query = "UPDATE Products SET `available_quantity` = `available_quantity`-?, `reservedOrder_quantity` = `reservedOrder_quantity`+? 
				   WHERE `id` = ?";	
				   
		$findWish = "SELECT IFNULL((SELECT `quantity` FROM Wishlist WHERE `order`=? AND `product`=?) ,0) AS `wishOrderQuantity`";		   
				   
		$i = 0;
		$j = 0;

		while(($oldSize > 0) || ($newSize > 0)){
		
			if($oldSize > 0){
				$oldProduct = $oldProducts[$i];
			}
			else if($newSize > 0){
				$oldProduct = $productsID[$j]+1;
			}
			
			if($newSize > 0){
				$productID = $productsID[$j];
			}
			else if($oldSize > 0){
				$productID = $oldProducts[$i]+1;
			}
		
			if($oldProduct < $productID){		
				$action = "remove";						
				$currentProduct = $oldProduct;
			}
			else if($oldProduct > $productID){
				$action = "add";
				$currentProduct = $productID;
			}
			else{
				$currentProduct = $productID;
			}
	
			if(!$result = $connection->prepare($sql)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");				
				throw new QueryError();
			}		
			
			if($expected_date != ''){
				if(!$result->bind_param('siii', $expected_date, $orderID, $customerID, $currentProduct)){
					$connection->rollback();
					$connection->autocommit(true);
					$connection->query("UNLOCK TABLES");
					throw new QueryError();
				}
			}
			else{
				if(!$result->bind_param('iii', $orderID, $customerID, $currentProduct)){
					$connection->rollback();
					$connection->autocommit(true);
					$connection->query("UNLOCK TABLES");
					throw new QueryError();
				}
			}
			
			if(!$result->execute()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->store_result()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->bind_result($order_quantity, $reservedOrder_quantity, $available_quantity, $previousQuantity, $reservedQuantity, 
									  $market_value, $final_price)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			$result->fetch();
			
			if(!$result = $connection->prepare($findWish)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");				
				throw new QueryError();
			}
			
			if(!$result->bind_param('ii', $orderID, $currentProduct)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->execute()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->store_result()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->bind_result($wishQuantity)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			$result->fetch();
			
			$previousQuantity+=$wishQuantity;
			$total_previousQuantity = $previousQuantity;
			
			if($oldProduct < $productID){			
				$currentQuantity = 0;	
				$i++;
				$oldSize--;
			}
			else if($oldProduct > $productID){	
				$currentQuantity = $sortedQuantity[$productID];
				$j++;
				$newSize--;
			}
			else{
				if($sortedQuantity[$productID] < $previousQuantity){
					$action = "remove";					
					$currentQuantity = $previousQuantity - $sortedQuantity[$productID];
				}
				else if($sortedQuantity[$productID] > $previousQuantity){
					$action = "update";
					$currentQuantity = $sortedQuantity[$productID] - $previousQuantity;
				}
				else{
					$action = "NoAction";
					$currentQuantity = 0;
				}
				$i++;
				$j++;
				$newSize--;
				$oldSize--;
			}
			
			// if(($wishQuantity != 0) && ($available_quantity > 0) && ($currentQuantity != 0)){			
				// if($available_quantity > $wishQuantity){
					// $available_quantity-= $wishQuantity;
					// $wishQuantity = 0;					
				// }
				// else{
					// $wishQuantity-=$available_quantity;
					// $available_quantity = 0;	
				// }
			// }

			switch($action){
			
				case "remove":
				
					if($currentQuantity == 0){	
						$previousQuantity-=$wishQuantity;
						$update_reserved = $reservedQuantity*(-1);
						$new_reservedQuantity = 0;
					
						if($previousQuantity > $reservedQuantity){
							$update_available = ($previousQuantity-$reservedQuantity)*(-1);
						}
						else{
							$update_available = 0;
						}
						$wishOrder = true;
						$wishlist->setNewWish($currentProduct, 0, "delete");
					}
					else{
				
						if($wishQuantity >= $currentQuantity){
					
							$wishOrder = true;
							
							if($wishQuantity > $currentQuantity){
								$wishlist->setNewWish($currentProduct, $currentQuantity, "delete");
							}
							else{
								$wishlist->setNewWish($currentProduct, 0, "delete");
							}						
							break;
						}
						else if($wishQuantity != 0){	
							
							$wishOrder = true;
							$wishlist->setNewWish($currentProduct, 0, "delete");
							$currentQuantity-=$wishQuantity;
							$previousQuantity-=$wishQuantity;
						}

						if(($previousQuantity - $reservedQuantity) >= $currentQuantity){
							$new_reservedQuantity = $reservedQuantity;
							$update_reserved = 0;
							$update_available = $currentQuantity*(-1);
						}
						else{
							$update_reserved = ($currentQuantity - ($previousQuantity - $reservedQuantity))*(-1);
							$update_available = ($previousQuantity - $reservedQuantity)*(-1);
							$new_reservedQuantity = $reservedQuantity - ($currentQuantity - ($previousQuantity - $reservedQuantity));
						}
						$currentQuantity = $sortedQuantity[$productID];
					}
					
					if(!$result = $connection->prepare($update)){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
					
					if($state == 'incompleted'){				
						if(!$result->bind_param('iiiii', $currentQuantity, $new_reservedQuantity, $total_previousQuantity, 
														 $orderID, $currentProduct)){
							$connection->rollback();
							$connection->autocommit(true);
							$connection->query("UNLOCK TABLES");
							throw new QueryError();
						}
					}
					else{				
						if(!$result->bind_param('iiii', $currentQuantity, $new_reservedQuantity, 
														 $orderID, $currentProduct)){
							$connection->rollback();
							$connection->autocommit(true);
							$connection->query("UNLOCK TABLES");
							throw new QueryError();
						}
					}
					
					if(!$result->execute()){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
					
					if(!$result = $connection->prepare($query)){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
					
					if(!$result->bind_param('iii', $update_available, $update_reserved, $currentProduct)){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
					
					if(!$result->execute()){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
					break;
				
				case "update":				
				case "add":
				
					if($wishQuantity > 0){
						$wish = true;
					}
					else{
						$wish = false;
					}
				
					if($order_quantity - $reservedOrder_quantity + $available_quantity < $currentQuantity){					
						$wishQuantity = $currentQuantity - ($order_quantity - $reservedOrder_quantity + $available_quantity);
						$currentQuantity-=$wishQuantity;
						$wishOrder = true;
						
						if($wish == false){
							$wishlist->setNewWish($currentProduct, $wishQuantity);
						}
						else{
							$wishlist->setNewWish($currentProduct, $wishQuantity, "alreadyExists");
							break;
						}
					}
					
					if($order_quantity - $reservedOrder_quantity >= $currentQuantity){
						$update_reserved = $currentQuantity;
						$update_available = 0;
					}
					else{
						$update_reserved = $order_quantity - $reservedOrder_quantity;
						$update_available = $currentQuantity - $update_reserved;
					}	

					if($action == "add"){
						if(!$result = $connection->prepare($add)){
							$connection->rollback();
							$connection->autocommit(true);
							$connection->query("UNLOCK TABLES");
							throw new QueryError();
						}

						$new_quantity = 0;
						if(!$result->bind_param('iiiiidd', $orderID, $currentProduct, $new_quantity, $currentQuantity, $update_reserved, 
														   $market_value, $final_price)){
							$connection->rollback();
							$connection->autocommit(true);
							$connection->query("UNLOCK TABLES");
							throw new QueryError();
						}
						
						if(!$result->execute()){
							$connection->rollback();
							$connection->autocommit(true);
							$connection->query("UNLOCK TABLES");						
							throw new QueryError();
						}
					}
					else{
						$currentQuantity+=$previousQuantity;
						$new_reservedQuantity = $reservedQuantity+$update_reserved;					
		
						if(!$result = $connection->prepare($update)){
							$connection->rollback();
							$connection->autocommit(true);
							$connection->query("UNLOCK TABLES");
							throw new QueryError();
						}

						if($state == 'incompleted'){
							if(!$result->bind_param('iiiii', $currentQuantity, $new_reservedQuantity, $total_previousQuantity, 
															 $orderID, $currentProduct)){
								$connection->rollback();
								$connection->autocommit(true);
								$connection->query("UNLOCK TABLES");
								throw new QueryError();
							}
						}
						else{
							if(!$result->bind_param('iiii', $currentQuantity, $new_reservedQuantity, 
															 $orderID, $currentProduct)){
								$connection->rollback();
								$connection->autocommit(true);
								$connection->query("UNLOCK TABLES");
								throw new QueryError();
							}
						}
						
						if(!$result->execute()){
							$connection->rollback();
							$connection->autocommit(true);
							$connection->query("UNLOCK TABLES");
							throw new QueryError();
						}
					}
					
					if(!$result = $connection->prepare($query)){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
				
					if(!$result->bind_param('iii', $update_available, $update_reserved, $currentProduct)){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
					
					if(!$result->execute()){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
					break;
				
				default:
					break;
			}
					
			if($state == 'incompleted'){				
				$updateFirstQuantity = "UPDATE CustomerOrderDetail SET `quantity`=? WHERE `order`=? AND product=?";				
				if(!$result = $connection->prepare($updateFirstQuantity)){
					$connection->rollback();
					$connection->autocommit(true);
					$connection->query("UNLOCK TABLES");
					throw new QueryError();
				}
						
				if(!$result->bind_param('iii', $total_previousQuantity, $orderID, $currentProduct)){
					$connection->rollback();
					$connection->autocommit(true);
					$connection->query("UNLOCK TABLES");
					throw new QueryError();
				}
						
				if(!$result->execute()){
					$connection->rollback();
					$connection->autocommit(true);
					$connection->query("UNLOCK TABLES");
					throw new QueryError();
				}
			}
		} 

		if($wishOrder == true){
			
			if($expected_date != ''){
				$wishlist->setDate($expected_date);
			}
			
			try{
				$wishlist->completeWish($orderID);
			}
			catch (Exception $e){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
		}
		
		if (!$connection->commit()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}	

		return $wishlist; 	 		
	}
	
	//function which creates a new Order
	public static function create_new_order($seller, $customer, $order_date, $expected_date, $productsID, $quantity, $comments = null){
	
		$connection = Database::createNewConnection();
	
		$wishlist = new Wishlist();
		$wishOrder = false;
		
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES CustomerOrderDetail WRITE, CustomerOrder WRITE, Products WRITE, Products AS Product1 READ,
								 Products AS Product2 READ, SupplyOrder READ, SupplyOrderDetail READ, Suppliers READ, Discounts READ")) {
			$connection->autocommit(true);
			throw new Exception('QueryError');
		}
		
		$query="INSERT INTO CustomerOrder (`seller`,`customer`,`order_date`, `comments`";
		
		if ($expected_date != ''){
			$query.=",`expected_date`) VALUES(?,?,?,?,?)";
		}
		else {
			$query.=") VALUES(?,?,?,?)";
		}
		
		if(!$result = $connection->prepare($query)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if($expected_date != ''){
			if(!$result->bind_param('iisss', $seller, $customer, $order_date, $comments, $expected_date)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		}
		else{
			if(!$result->bind_param('iiss', $seller, $customer, $order_date, $comments)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		}
		
		if(!$result->execute()){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		$Order_id = $connection->insert_id;

		$sql= "SELECT IFNULL(SUM(InOrder.`quantity`),0) AS `order_quantity`, IFNULL(Product1.`reservedOrder_quantity`,0) AS `reservedOrder_quantity`,
		              IFNULL(Product1.`available_quantity`,0) AS `available_quantity`, Product1.`market_value` AS `market_value`, 
					  Product1.`sell_value`-Product1.`sell_value`*IFNULL(Discount.`discount`, 0) AS `final_price`     
				FROM Products Product1 LEFT JOIN(SELECT `product`, `quantity` FROM SupplyOrder, SupplyOrderDetail 
										         WHERE `order`=SupplyOrder.`id` AND `state`='incompleted'  AND `expected_date`!='NULL'";
		if($expected_date != ''){
			$sql.=" AND `expected_date` < ?";
		}										 
        $sql.=") AS InOrder ON Product1.`id` = InOrder.`product`, Suppliers, Products Product2 LEFT JOIN(SELECT `product`, `discount` FROM Discounts
               WHERE Discounts.`customer`=?) AS Discount ON Discount.`product`=Product2.`id` WHERE Product1.`status`='active' 
			   AND Suppliers.`id`=Product1.`supplied_by` AND Product1.`id`=? AND Product1.`id`=Product2.`id`";			
					
		
		$query1 = "INSERT INTO CustomerOrderDetail (`order`,`product`,`quantity`,`modified_quantity`,`reservedQuantity`, `market_value`,
		          `sell_value`) VALUES (?,?,?,?,?,?,?)";
		
		$query2 = "UPDATE Products SET `available_quantity` = `available_quantity`-?, `reservedOrder_quantity` = `reservedOrder_quantity`+? 
				   WHERE `id` = ?";			   
		
		$i=0;
		foreach($productsID as $product){
					
			if(!$result = $connection->prepare($sql)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception(' QueryError ');
			}		
			
			if($expected_date != ''){
				if(!$result->bind_param('sii', $expected_date, $customer, $product)){
					$connection->rollback();
					$connection->autocommit(true);
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
			}
			else{
				if(!$result->bind_param('ii', $customer, $product)){
					$connection->rollback();
					$connection->autocommit(true);
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
			}
			
			if(!$result->execute()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			if(!$result->store_result()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			if(!$result->bind_result($order_quantity, $reservedOrder_quantity, $available_quantity, $market_value, $final_price)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			$result->fetch();
			
			if($order_quantity - $reservedOrder_quantity + $available_quantity < $quantity[$i]){					
				$wishQuantity = $quantity[$i] - ($order_quantity - $reservedOrder_quantity + $available_quantity);
				$quantity[$i]-=$wishQuantity;
				$wishOrder = true;
				$wishlist->setNewWish($product, $wishQuantity);
			}
			
			if($order_quantity - $reservedOrder_quantity >= $quantity[$i]){
				$update_reserved = $quantity[$i];
				$update_available = 0;
			}
			else{
				$update_reserved = $order_quantity - $reservedOrder_quantity;
				$update_available = $quantity[$i] - $update_reserved;
			}
			
			if(!$result = $connection->prepare($query1)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		
			if(!$result->bind_param('iiiiidd', $Order_id, $product, $quantity[$i], $quantity[$i], $update_reserved, $market_value, $final_price)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			if(!$result->execute()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			if(!$result = $connection->prepare($query2)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError '.$query2);
			}
		
			if(!$result->bind_param('iii', $update_available, $update_reserved, $product)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			if(!$result->execute()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			$i++;
		}
		
		if($wishOrder == true){
			
			if($expected_date != ''){
				$wishlist->setDate($expected_date);
			}
			
			try{
				$wishlist->completeWish($Order_id);
			}
			catch (Exception $e){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		}
		
		if (!$connection->commit()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new Exception('QueryError');
		}	

		return $wishlist; 		
	}
	
	//function which return the orders
	public static function get_orders($id = null, $options = null, $from=null, $to=null, $customerID=null){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$i = 1;
		$bind_names[0] = '';
		$argExist = false;
		
		$query = "SELECT CustomerOrder.`id`, `seller`, `customer`, `order_date`, `expected_date`, `receipt_date`,
 		          CASE `state` WHEN 'completed' THEN 'completed' WHEN 'modified' THEN 'incompleted'
                  WHEN 'cancelled' THEN 'cancelled' ELSE 'incompleted' END, `username`, 
				  Customers.`fullname` FROM CustomerOrder, Customers, Users WHERE `seller`=Users.`id` AND `customer`=Customers.`id`";
		
		if($id != null){
			$argExist = true;
			$bind_names[0].='i';
			$bind_name = 'bind' . $i;
            $$bind_name = $id;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND `seller`=? ";
		}
		if($customerID != null){
			$argExist = true;
			$bind_names[0].='i';
			$bind_name = 'bind' . $i;
            $$bind_name = $customerID;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND `customer`=? ";
		}
		if($from != null){
			$argExist = true;
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $from;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND `order_date`>=? ";
		}
		if($to != null){
			$argExist = true;
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $to;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND `order_date`<=? ";
		}
		
		if($options == 'completed'){
			$query.=" AND `state`='completed' ORDER BY ";
		}
		else if($options == 'incompleted'){
			$query.=" AND (`state`='incompleted' OR `state`='modified') ORDER BY ";
		}
		else{
			$query.=" ORDER BY `state` DESC, ";
		}
	
		$query.="  receipt_date DESC, expected_date DESC ";
	
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if($argExist == true){
			call_user_func_array(array($result,'bind_param'),$bind_names);
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		$count = $result->num_rows;
		
		if(!$result->bind_result($id, $seller_id, $customer_id, $order_date, $expected_date, $receipt_date, $state, $seller_name, $customer_name)){
			throw new Exception('QueryError');
		}
		
		
		$order_info = array();
		while($result->fetch()){
			$order = new CustomerOrders();
			$order->set_order($id, $seller_id, $customer_id, $order_date, $expected_date, $receipt_date, $state, $seller_name, $customer_name);
			$order_info[$id] = $order;
		}		
		return $order_info;
	} 

	//function which returns full information about an order
	public static function get_order_by_id($id){					
		
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		if (!$connection->query("LOCK TABLES CustomerOrder READ, CustomerOrderDetail READ, Products READ, 
                        		 Products AS wishProduct READ, Wishlist READ")) {
			throw new Exception('QueryError');
		}
		
		$query = "SELECT * FROM CustomerOrder WHERE `id`=?";
		
		if(!$result = $connection->prepare($query)){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('i', $id)){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_result($order_id, $sellerID, $customerID, $order_date, $expected_date, $receipt_date, $state, $comment)){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		$result->fetch();
		
		if($state == 'modified'){
			$state = 'incompleted';
		}
		
		$order = new CustomerOrders();
		$order->set_info($order_id, $order_date, $expected_date, $receipt_date, $state, $comment);
		
		$query = "SELECT Products.`id` AS `id`, Products.`name` AS `name`, `modified_quantity`, `deliverable_quantity`, 
                          Products.`total_quantity` AS `total_quantity`, Products.`available_quantity` AS `available_quantity`, 
						  IFNULL(Wish.`quantity`, 0) AS `wish_quantity`, CustomerOrderDetail.`quantity` AS `quantity`
				   FROM CustomerOrderDetail, Products , Products AS wishProduct LEFT JOIN(SELECT `product`, `quantity` FROM Wishlist
                                                                    WHERE `order`=?) AS Wish ON Wish.`product`=wishProduct.`id`
				   WHERE `order`=? AND Products.`id` = CustomerOrderDetail.`product` AND Products.`id`=wishProduct.`id`";
		
		if(!$result = $connection->prepare($query)){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('ii', $id, $id)){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_result($productID, $product_name, $modified_quantity, $deliverable_quantity, $total_quantity, 
								 $available_quantity, $wish_quantity, $start_quantity)){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		$products = array();
		
		while($result->fetch()){
			$product = new ProductsInOrder();
			$product->set_basicInfo($productID, $product_name, $modified_quantity, $deliverable_quantity, $total_quantity,
			                        $available_quantity, $wish_quantity);
			$product->start_quantity = $start_quantity;						
			array_push($products, $product);
		}
		
		$order->products = $products;
		
		if(!$connection->query("UNLOCK TABLES")){
			throw new Exception('QueryError');
		}	
		
		
		$order->customer = Customers::getCustomer($customerID);
		$order->seller = ModelUsers::get_user($sellerID);
		
		return $order;
	}
	
	//function whcih sends an order
	public static function send_order($order_id, $send_date, $product, $quantity){
	
		$connection = Database::createNewConnection();
		
		$exceptionalMessage = new ExceptionalMessages();
		$exception = false;
		
		$wishlist = new Wishlist();
	
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES CustomerOrderDetail WRITE, CustomerOrder WRITE, Products WRITE")) {
			$connection->autocommit(true);
			throw new Exception('QueryError');
		}
	
		$query = "UPDATE `CustomerOrder` SET `receipt_date`=?, `state`='completed' WHERE id=?";
	
		if(!$result = $connection->prepare($query)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		if(!$result->bind_param('si', $send_date, $order_id)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		if(!$result->execute()){								
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		$query = "SELECT `total_quantity`, `available_quantity`, `modified_quantity`, `reservedQuantity` FROM Products, CustomerOrderDetail 
				  WHERE Products.`id` = ? AND Products.`id`=`product` AND `order`=?";
		
		$query_product = "UPDATE Products SET `total_quantity`=`total_quantity`-?, `available_quantity`=`available_quantity`-?, 
						  `reservedOrder_quantity`=`reservedOrder_quantity`-? WHERE `id`=?";
						  
		$query_info = "UPDATE `CustomerOrderDetail` SET `deliverable_quantity`=? WHERE `order`=? AND `product`=?";

		$i = 0;
		foreach($product as $product_id){
		

			if(!$result = $connection->prepare($query)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}	
			
			if(!$result->bind_param('ii', $product_id, $order_id)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			if(!$result->execute()){															
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			if(!$result->store_result()){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			if(!$result->bind_result($total_quantity, $available_quantity, $expected_quantity, $reservedQuantity)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			$result->fetch();
				
			if($total_quantity < $quantity[$i]){	
			
				$exception = true;
				
				try{
					$exceptionalMessage->setOrder($product_id, $quantity[$i]-$total_quantity);
				}
				catch (Exception $e){
					$connection->rollback();
					$connection->autocommit(true);
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
				$update_total = $total_quantity;
				$update_available = $available_quantity;
				$deliverable = 0;
			}
			else{
				$update_total = $quantity[$i];
				$update_available = $quantity[$i];
				$deliverable = 0;
			}
			
							  
			if(!$result_product = $connection->prepare($query_product)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}		
			$reservedQuantity = 0;
			if(!$result_product->bind_param('iiii', $update_total, $update_available, $reservedQuantity, $product_id)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			if(!$result_product->execute()){												
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		
		
			if(!$result_info = $connection->prepare($query_info)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		
			if(!$result_info->bind_param('iii', $deliverable, $order_id, $product_id)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}

			if(!$result_info->execute()){												
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			$wishlist->setNewWish($product_id, 0, "delete");
			
			$i++;
		}
		
		try{
			$wishlist->completeWish($order_id);
		}
		catch (Exception $e){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if (!$connection->commit()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new Exception('QueryError');
		}
		
		if($exception == true){
			$exceptionalMessage->completeOrder($order_id, $send_date, 'customer');
		}
		
		return $exceptionalMessage;
	}
	
	//function which returns an array that contains, for each day between startDate and endDate, the amount
    //of orders placed by $sellerID. If sellerID is empty, it returns the amount of orders placed by all sellers
    public static function getNumberOfOrders($startDate, $endDate, $sellerID = null) {

		$connection = Database::createNewConnection();
		$connection->autocommit(true);

        $query = "SELECT `order_date`, COUNT(*) AS `count` FROM CustomerOrder WHERE `order_date` BETWEEN ? AND ? ";
        if( $sellerID != null )
            $query.= " AND `seller`=? ";
        $query.= " GROUP BY `order_date` ";

		
        if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if($sellerID == null){
			if(!$result->bind_param('ss', $startDate, $endDate)){
				throw new Exception('QueryError');
			}
		}
		else{
			if(!$result->bind_param('ssi', $startDate, $endDate, $sellerID)){
				throw new Exception('QueryError');
			}
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_result($order_date, $count)){
			throw new Exception('QueryError');
		};

        $orders = array();
		while($result->fetch()){
			$orders[$order_date] = $count;
		}
		
        return $orders;
    }
	
	//function which returns a list of user as objects who have set order in a specific period
	public static function getUsers($dateFrom = null, $dateTo = null, $orderState = null){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$argExist = false;
		$i = 1;
		$bind_names[0] = '';
		
		$query = "SELECT DISTINCT(Users.`username`), Users.`email`, Users.`vat`, Users.`phone_number`, Users.`id`, 
				  Users.`fullname`, Users.`status`, Users.`type`  FROM CustomerOrder, Users WHERE Users.`id`=`seller` ";
				  
		if($dateFrom != null){		
			$argExist = true;
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $dateFrom;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND `order_date`>=?";
		}
		if($dateTo != null){
			$argExist = true;
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $dateTo;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.="  AND `order_date` <= ?";
		}
		if(($orderState != null) && ($orderState == 'completed' || $orderState == 'incompleted')){
			$argExist = true;
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $orderState;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND CustomerOrder.`state`=?";
		}
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if($argExist == true){
			call_user_func_array(array($result,'bind_param'),$bind_names);
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_result($username, $email, $vat, $phone_number, $id, $fullname, $status, $type)){
			throw new Exception('QueryError');
		};

        $sellerList = array();
		while($result->fetch()){
			$sellerList[$id] = new ModelUsers;
			$sellerList[$id]->set_info($id, $username, $fullname, $email, $vat, $phone_number, $type, $status);
		}
		
		return $sellerList;
	}
	
	//function which returns daily markets/sells
	public static function findMarketsSells($from, $to, $userID=null, $orderState=null){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$i = 3;
		$bind_names[0] = 'ss';
		$bind_names[1] = &$from;
		$bind_names[2] = &$to;
		
		$query = "SELECT `username`, `order_date`, SUM(IFNULL(`sell_value`*`deliverable_quantity`,`sell_value`*`modified_quantity`)) AS `income`, 
				  SUM(IFNULL(`market_value`*`deliverable_quantity`,`market_value`*`modified_quantity`)) AS `outgoing`  
				  FROM CustomerOrder, Users, CustomerOrderDetail 
				  WHERE `order_date`>=? AND `order_date` <= ? AND CustomerOrderDetail.`order`=CustomerOrder.`id`
				  AND Users.`id`=CustomerOrder.`seller`"; 
		if($userID != null){		  
			$bind_names[0].='i';
			$bind_name = 'bind' . $i;
            $$bind_name = $userID;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND Users.`id`=?";
		}
		if($orderState != null){
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $orderState;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND CustomerOrder.`state`=?";
		}
		$query.=" GROUP BY CustomerOrder.`order_date`";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		call_user_func_array(array($result,'bind_param'),$bind_names);
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_result($username, $orderDate, $income, $outgoing)){
			throw new Exception('QueryError');
		};

		$markets_sells = array();
		$i = 0;
		while($result->fetch()){
			$row = array();
			$row['username'] = $username;
			$row['orderDate'] = $orderDate;
			$row['income'] = $income;
			$row['outgoing'] = $outgoing;
			$markets_sells[$i] = $row;
			$i++;
		}
		return $markets_sells;
	}
	
	//function which marks an order as deleted
	public static function deleteOrder($orderID){
		
		$productsID = array();
		$quantities = array();
		
		try{
			self::editOrders($orderID, null, '', $productsID, $quantities, null, false);
		}
		catch(QueryError $ex){
			throw new QueryError();
		}
	}
	
	//function which returns the comment of an order
	public static function getComment($orderID){
		
		$connection = Database::createNewConnection();
		
		$query = "SELECT IFNULL(`comments`,null) AS `comment` FROM CustomerOrder WHERE `id`=?";
		
		if(!$result = $connection->prepare($query)){
			throw new QueryError();
		}
		
		if(!$result->bind_param('i', $orderID)){
			throw new QueryError();
		}
		
		if(!$result->execute()){
			throw new QueryError();
		}
		
		if(!$result->store_result()){
			throw new QueryError();
		}
		
		if(!$result->bind_result($comment)){
			throw new QueryError();
		}
		
		$result->fetch();
		
		return $comment;
		
	}	
	
	//function add new comment
	public static function setComment($orderID, $comment){
	
		$connection = Database::createNewConnection();
		
		$query = "UPDATE CustomerOrder SET `comments`=? WHERE `id`=?";
		
		if(!$result = $connection->prepare($query)){
			throw new QueryError();
		}
		
		if(!$result->bind_param('si', $comment, $orderID)){
			throw new QueryError();
		}
		
		if(!$result->execute()){
			throw new QueryError();
		}
	
	}
	
	
	private function set_info($id, $order_date, $expected_date, $receipt_date, $state, $comment = null){
		$this->id = $id;
		$this->order_date = $order_date;
		$this->expected_date = $expected_date;
		$this->receipt_date = $receipt_date;
		$this->status = $state;
		$this->comment = $comment;
	}
	
	private function set_order($id, $seller_id, $customer_id, $order_date, $expected_date, $receipt_date, $state, $seller_name, $customer_name){
		$this->id = $id;
		$this->seller_id = $seller_id;
		$this->customer_id = $customer_id;
		$this->order_date = $order_date;
		$this->expected_date = $expected_date;
		$this->receipt_date = $receipt_date;
		$this->status = $state;
		$this->seller_name = $seller_name;
		$this->customer_name = $customer_name;
	} 

}

?>