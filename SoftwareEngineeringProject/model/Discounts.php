<?php

Class Discounts{

	//ARRAY OF DISCOUNT PER PRODUCT
	public $discounts;

	//CLASS OBJECTS DEFINITION	
	public $customer;
	
	//ARRAY OF OBJECTS
	public $products;
	
	
	//function which returns the discounts of a customer
	public static function getDiscounts($id){
	
		$discountsInfo = new Discounts();
	
		$discountsInfo->customer = Customers::getCustomer($id);
		if($discountsInfo->customer == null){
			throw new NotValidUserID();
		}
	
		$connection = Database::createNewConnection();
	
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES Discounts READ, Products READ, Suppliers READ, SupplyOrder READ, SupplyOrderDetail READ")) {
			$connection->autocommit(true);
			throw new Exception('QueryError');
		}
		
		$query = "SELECT `product`, `discount` FROM Discounts WHERE `customer`=?";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->bind_param('i', $id)){
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
		
		if(!$result->bind_result($product, $discount)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		$sql = "SELECT Products.`id`, `name`, `description`, `metric_units`, `market_value`, `sell_value`, `total_quantity`, 
					   `available_quantity`, (IFNULL(SUM(InOrder.`quantity`), 0)-`reservedOrder_quantity`) AS `order_quantity`, 
					   `supplied_by`, `fullname`, Products.`status`
				FROM Products LEFT JOIN(SELECT `product`, `quantity` FROM SupplyOrder, SupplyOrderDetail 
										WHERE `order`=SupplyOrder.`id` AND `state`='incompleted'  AND `receipt_date`!='NULL') 
										AS InOrder ON Products.`id` = InOrder.`product`, Suppliers 
				WHERE Products.`status`='active' AND Suppliers.`id`=`supplied_by` AND Products.`id`=?";
		
		
		$discountsInfo->products = array();
		$discountsInfo->discounts = array();
		
		while($result->fetch()){
			
			if(!$SqlResult = $connection->prepare($sql)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception($connection->error);
			}
			
			if(!$SqlResult->bind_param('i', $product)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$SqlResult->execute()){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$SqlResult->store_result()){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$SqlResult->bind_result($id, $name, $description, $metric_units, $market_value, $sell_value, $total_quantity, $available_quantity,
									 $order_quantity, $supplied_by, $supplier, $status)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			$SqlResult->fetch();
			$product = new ModelProducts();
			$product->set_productInfo($id, $name, $total_quantity, $available_quantity, $supplied_by, $order_quantity, $supplier);
			$product->set_marketInfo($description, $metric_units, $market_value, $sell_value);

			$discountsInfo->products[$product->id] = $product;
			$discountsInfo->discounts[$product->id] = $discount;
		
		}

		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}
		
		return $discountsInfo;
		
	}
	
	//function which creates new discounts for a customer
	public static function makeNewDiscounts($customerID, $productsID, $discounts){
	
		$connection = Database::createNewConnection();
		
		if(!$connection->autocommit(false)){
			throw new QueryError();
		}
		
		if (!$connection->query("LOCK TABLES Discounts WRITE")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$query = "DELETE FROM Discounts WHERE `customer` = ?";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->bind_param('i', $customerID)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
				
		if(!$result->execute()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}		
				
		$query = "INSERT INTO Discounts(`product`, `customer`, `discount`) VALUES(?,?,?)";
		
		if(!$result = $connection->prepare($query)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}	
		
		foreach($productsID as $product){
			
			if(!$result->bind_param('iid', $product, $customerID, $discounts[$product])){
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
		
		if (!$connection->commit()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}	
	
	}
};

?>