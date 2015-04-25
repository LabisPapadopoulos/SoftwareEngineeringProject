<?php

class ExceptionalMessages{

	public $id;
	public $date;
	public $supplierOrder;
	public $customerOrder;
	
	//arrays
	public $products = array();
	public $quantities = array();
	public $types = array();
	
	
	//function which sets message for one object of an order
	public function setOrder($product_id, $missingQuantity, $type = null){
	
		array_push($this->products, $product_id);
		array_push($this->quantities, $missingQuantity);
		
		if($type != null){
			array_push($this->types, $type);
		}
		else{
			array_push($this->types, 'missing');
		}
	}
	
	//function which completes an ExceptionalMessage for an order
	public function completeOrder($orderID, $date, $orderType){
	
		$connection = Database::createNewConnection();
		
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES ExceptionalMessage WRITE")) {
			throw new Exception('QueryError');
		}
		
		if($orderType == 'customer'){
			$query = "INSERT INTO ExceptionalMessage(`customerOrder`, `product`, `quantity`, `type`, `date`) VALUES(?, ?, ?, ?, ?)";
		}
		else{
			$query = "INSERT INTO ExceptionalMessage(`SupplyOrder`, `product`, `quantity`, `type`, `date`) VALUES(?, ?, ?, ?, ?)";
		}
		
		foreach($this->products as $key=>$value){
		
			if(!$result = $connection->prepare($query)){
				$connection->rollback();
				$connection->query("UNLOCK TABLES");
				throw new Exception(' QueryError ');
			}
			
			if(!$result->bind_param('iiiss', $orderID, $value, $this->quantities[$key], $this->types[$key], $date)){
				$connection->rollback();
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			if(!$result->execute()){
				$connection->rollback();
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		}
		
		if (!$connection->commit()){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new Exception('QueryError');
		}
	
	}


};

?>