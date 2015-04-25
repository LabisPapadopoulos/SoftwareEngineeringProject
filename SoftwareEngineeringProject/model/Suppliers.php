<?php

Class ModelSuppliers{
	
	public $id;
	public $fullname;
	public $vat;
	public $location;
	public $phone_number;
	public $email;
	public $status;

	//function which returns all the suppliers(only the active if $option='active')
	public static function get_suppliers($option = null){

		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "SELECT * FROM Suppliers";
		if($option == 'active'){
			$query.=" WHERE `status`='active'";
		}		
		$query.=" ORDER BY `fullname`";
		
		if(!$result = $connection->query($query)){
			throw new Exception('QueryError');
		}
		
		while($row = $result->fetch_Object("ModelSuppliers")){		
			$suppliers_info[$row->id] = $row;
		}
		
		return $suppliers_info;
	}
	
	//function which returns the supplier with id=$id
	public static function get_supplier_by_id($id){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "SELECT * FROM Suppliers WHERE id=?";
		
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
		
		if(!$result->bind_result($id, $fullname, $vat, $location, $phone_number, $email, $status)){
			throw new Exception('QueryError');
		}
	
		if($count == 1){
			$result->fetch();
			$supplier_info = new ModelSuppliers();
			$supplier_info->set_info($id, $fullname, $vat, $location, $phone_number, $email, $status);
			return $supplier_info;
		}
		else{
			return null;
		}
	}
	
	//function which inserts a new suppliers
	public static function insertNewSupplier($fullname, $vat, $location, $phone_number, $email){
	
		$connection = Database::createNewConnection();
		$all_queries_done = true;
		
		if(!$connection->autocommit(false)){
			throw new QueryError();
		}
		
		
		if (!$connection->query("LOCK TABLES Suppliers WRITE")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$query = "SELECT IF((SELECT COUNT(*) > 0 FROM Suppliers WHERE `fullname`=?) ,\"yes\" ,\"no\") AS `answer`";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->bind_param('s', $fullname)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->execute()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->store_result()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->bind_result($answer)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$result->fetch();
		if($answer == 'yes'){
			$connection->autocommit(true);
			throw new SupplierAlreadyExists();
		}
		
		$query = "INSERT INTO Suppliers(`fullname`,`vat`,`location`,`phone_number`) VALUES(?,?,?,?)";
		
		$result = $connection->prepare($query);	
		$result->bind_param('siss', $fullname, $vat, $location, $phone_number);
		$result->execute() ? null : $all_queries_done=false;
		
		if($email != ''){
			$query = "UPDATE Suppliers SET `email`=? WHERE `fullname`=?";
			
			$result = $connection->prepare($query);	
			$result->bind_param('ss', $email, $fullname);
			$result->execute() ? null : $all_queries_done=false;	
		}
		
		if($all_queries_done == true){
			if (!$connection->commit()){
				$connection->autocommit(true);
				throw new Exception('QueryError');
			}
		}
		else{
			if (!$connection->rollback()){
				$connection->autocommit(true);
				throw new Exception('QueryError');
			}
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}
	}
	
	//function which deletes a supplier 
	public static function deleteSupplier($fullname){
	
		$connection = Database::createNewConnection();	
		$connection->autocommit(true);
		
		$query = "UPDATE Suppliers SET `status`='deleted' WHERE `fullname`=?";
		
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
	
	private function set_info($id, $fullname, $vat, $location, $phone_number, $email, $status){
		$this->id = $id;
		$this->fullname = $fullname;
		$this->vat = $vat;
		$this->location = $location;
		$this->phone_number = $phone_number;
		$this->email = $email;
		$this->status = $status;
	}
	
}
?>	