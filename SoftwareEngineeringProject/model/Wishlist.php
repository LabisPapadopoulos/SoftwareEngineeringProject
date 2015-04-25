<?php

class Wishlist{

	public $id_array = array();
	public $product_array = array();
	public $quantity_array = array();
	public $exists = array();
	public $order;
	public $wishDate;
	
	
	//function which sets message for one object of an order
	public function setNewWish($product, $quantity, $option = null){
	
		array_push($this->product_array, $product);
		array_push($this->quantity_array, $quantity);
		
		if($option == "alreadyExists"){
			array_push($this->exists, "true");
		}
		else if($option == "delete"){
			array_push($this->exists, "delete");
		}
		else{
			array_push($this->exists, "false");
		}
	}
	
	//function which sets the date of the creation
	public function setDate($date){
		$this->wishDate = $date;
	}
	
	//function which completes a wish
	public function completeWish($order){
	
		$connection = Database::createNewConnection();
		
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES Wishlist WRITE")) {
			throw new Exception('QueryError');
		}
		
		$add = "INSERT INTO Wishlist(`order`, `product`, `quantity`, `date`) VALUES(?,?,?,?)";
		$update = "UPDATE Wishlist SET `quantity`=`quantity`+? WHERE `order`=? AND `product`=?";
		$delete = "DELETE FROM Wishlist WHERE `order`=? AND `product`=?";
		
		$i = 0;
		foreach($this->product_array as $product){
			
			if($this->exists[$i] == "false"){
				if(!$result = $connection->prepare($add)){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception(' QueryError ');
				}
				
				if(!$result->bind_param('iiis', $order, $product, $this->quantity_array[$i], $this->wishDate)){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
			}
			else if(($this->exists[$i] == "delete") && ($this->quantity_array[$i] == 0)){
				if(!$result = $connection->prepare($delete)){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception(' QueryError ');
				}
				
				if(!$result->bind_param('ii', $order, $product)){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
			}
			else if(($this->exists[$i] == "delete") && ($this->quantity_array[$i] != 0)){
			
				$this->quantity_array[$i]*=(-1);
				
				if(!$result = $connection->prepare($update)){
						$connection->rollback();
						$connection->query("UNLOCK TABLES");			
						throw new Exception('QueryError');
					}
					
					if(!$result->bind_param('iii', $this->quantity_array[$i], $order, $product)){
						$connection->rollback();
						$connection->query("UNLOCK TABLES");
						throw new Exception('QueryError');
					}
			}
			else{
			
				$query = "SELECT IF((SELECT COUNT(*) > 0 FROM Wishlist WHERE `order`=? AND `product`=?), \"yes\", \"no\") AS `answer` ";
			
				if(!$result = $connection->prepare($query)){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception(' QueryError ');
				}
				
				if(!$result->bind_param('ii', $order, $product)){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
			
				if(!$result->execute()){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
				
				if(!$result->store_result()){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
				
				if(!$result->bind_result($answer)){
					$connection->rollback();
					$connection->query("UNLOCK TABLES");
					throw new Exception('QueryError');
				}
				
				$result->fetch();
				
				if($answer == "yes"){
					if(!$result = $connection->prepare($update)){
						$connection->rollback();
						$connection->query("UNLOCK TABLES");			
						throw new Exception('QueryError');
					}
					
					if(!$result->bind_param('iii', $this->quantity_array[$i], $order, $product)){
						$connection->rollback();
						$connection->query("UNLOCK TABLES");
						throw new Exception('QueryError');
					}
				}
				else{
					if(!$result = $connection->prepare($add)){
						$connection->rollback();
						$connection->query("UNLOCK TABLES");
						throw new Exception(' QueryError ');
					}
					
					if(!$result->bind_param('iiis', $order, $product, $this->quantity_array[$i], $this->wishDate)){
						$connection->rollback();
						$connection->query("UNLOCK TABLES");
						throw new Exception('QueryError');
					}
				}
			}
			
			if(!$result->execute()){
				$connection->rollback();
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
			
			$id = $connection->insert_id;
			array_push($this->id_array, $id);
			
			$i++;
		}
		
		if (!$connection->commit()){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new Exception('QueryError');
		}
		
	}
	
}

?>