<?php


Class Customers{

	public $id;
	public $fullname;
	public $vat;
	public $location;
	public $phone_number;
	public $email;
	public $status;
	public $inserted_by;
	public $inserted_by_name;
	
	//function which inserts a new customer
	public static function insertNewCustomer($fullname, $vat, $location, $phone_number, $email, $inserted_by){
	
		$connection = Database::createNewConnection();	
		$connection->autocommit(true);
		
		if (!$connection->query("LOCK TABLES Customers WRITE")) {
			throw new QueryError();
		}
		
		$query = "SELECT IF((SELECT COUNT(*) > 0 FROM Customers WHERE `fullname`=?) ,\"yes\" ,\"no\") AS `answer`";
		
		if(!$result = $connection->prepare($query)){
			throw new QueryError();
		}
		
		if(!$result->bind_param('s', $fullname)){
			throw new QueryError();
		}
		
		if(!$result->execute()){
			throw new QueryError();
		}
		
		if(!$result->store_result()){
			throw new QueryError();
		}
		
		if(!$result->bind_result($answer)){
			throw new QueryError();
		}
		
		$result->fetch();
		if($answer == 'yes'){
			throw new CustomerAlreadyExists();
		}
		
		$query = "INSERT INTO Customers(`fullname`,`vat`,`location`,`phone_number`,`inserted_by`";
		if($email != ''){
			$query.=",`email`";
		}
		$query.=") VALUES(?,?,?,?,?";
		if($email != ''){
			$query.=",?";
		}
		$query.=")";
				
		if(!$result = $connection->prepare($query)){
			throw new QueryError();
		}
		
		if($email != ''){
			if(!$result->bind_param('sissis', $fullname, $vat, $location, $phone_number, $inserted_by, $email)){
				throw new QueryError();
			}
		}
		else{
			if(!$result->bind_param('sissi', $fullname, $vat, $location, $phone_number, $inserted_by)){
				throw new QueryError();
			}
		}
		
		if(!$result->execute()){
			throw new QueryError();
		}
	
	}

	//function which deletes a customer 
	public static function deleteCustomer($fullname){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
	
		$query = "UPDATE Customers SET `status`='deleted' WHERE `fullname`=?";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('s', $fullname)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
	}

	//function which returns all the customers
	public static function showActiveCustomers($option = null){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
	
		$query = "SELECT Customers.`id` AS `id`, Customers.`fullname` AS `fullname`, Customers.`vat` AS `vat`, `location`, 
				  Customers.`phone_number` AS `phone_number`, Customers.`email` AS `email`, Customers.`status` AS `status`, 
				  `inserted_by`, `username` FROM Customers, Users WHERE `inserted_by`=Users.`id`";
		if($option == 'active'){
			$query.=" AND Customers.`status`='active'";
		}
		else if($option == 'deleted'){
			$query.=" AND Customers.`status`='deleted'";
		}
		$query.=" ORDER BY Customers.`status`, `fullname`";
			
		if(!$result = $connection->query($query)){
			throw new Exception('QueryError');
		}
		
		$suppliers_info = array();
		while($row = $result->fetch_assoc()){
			$customer = new Customers();
			$customer->set_info($row['id'], $row['fullname'], $row['vat'], $row['location'], $row['phone_number'], 
								$row['email'], $row['inserted_by'], $row['username'], $row['status']);
			$suppliers_info[$customer->id] = $customer;
		}
				
		return $suppliers_info;
	}

	//function which returns a customer by ID
	public static function getCustomer($id) {
		
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "SELECT Customers.`fullname` AS `fullname`, Customers.`vat` AS `vat`, `location`, 
				  Customers.`phone_number` AS `phone_number`, Customers.`email` AS `email`, Customers.`status` AS `status`, 
				  `inserted_by`, `username` FROM Customers, Users WHERE `inserted_by`=Users.`id` AND Customers.`id`=?";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('i', $id)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		$count = $result->num_rows;
		
		if(!$result->bind_result($fullname, $vat, $location, $phone_number, $email, $status, $inserted_by, $username)){
			throw new Exception('QueryError');
		}
	
		if($count == 1){
			$result->fetch();
			$customer = new Customers();
			$customer->set_info($id, $fullname, $vat, $location, $phone_number, $email, $inserted_by, $username, $status);
			return $customer;
		}
		else{
			return null;
		}

		return $customer;
	}
	
	//function which edits the data of a customer
	public static function editCustomer($fullname, $vat, $location, $phone_number, $email){
		
		if($fullname == ''){
			throw new Exception('Username field is empty');
		}
		
		$connection = Database::createNewConnection();
		
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES Customers WRITE")) {
			$connection->autocommit(true);
			throw new Exception('QueryError');
		}
		
		if($vat != ''){
			$query = "UPDATE Customers SET `vat`=? WHERE `fullname`=?";
			
			if(!$result = $connection->prepare($query)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		
			if(!$result->bind_param('is', $vat, $fullname)){
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
		}
		
		if($location != ''){
			$query = "UPDATE Customers SET `location`=? WHERE `fullname`=?";
			
			if(!$result = $connection->prepare($query)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		
			if(!$result->bind_param('ss', $location, $fullname)){
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
		}
		
		if($phone_number != ''){
			$query = "UPDATE Customers SET `phone_number`=? WHERE `fullname`=?";
			
			if(!$result = $connection->prepare($query)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		
			if(!$result->bind_param('ss', $phone_number, $fullname)){
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
		}
		if($email != ''){
			$query = "UPDATE Customers SET `email`=? WHERE `fullname`=?";
			
			if(!$result = $connection->prepare($query)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		
			if(!$result->bind_param('ss', $email, $fullname)){
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
		}
		
		if (!$connection->commit()){
			$connection->autocommit(true);
			throw new Exception('QueryError');
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new Exception('QueryError');
		}
	}
	
	private function set_info($id, $fullname, $vat, $location, $phone_number, $email, $inserted_by, $inserted_by_name, $status){
		
		$this->id = $id;
		$this->fullname = $fullname;
		$this->vat = $vat;
		$this->location = $location;
		$this->phone_number = $phone_number;
		$this->email = $email;
		$this->inserted_by = $inserted_by;
		$this->inserted_by_name = $inserted_by_name;
		$this->status = $status;
	}
}



?>