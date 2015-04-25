<?php

Class ProductsInOrder extends ModelProducts{

    public $quantity;
    public $completed_quantity;
	public $wish_quantity;
	public $start_quantity;
	
	public function set_basicInfo($id, $name, $quantity, $completed_quantity, $total_quantity, $available_quantity, $wish_quantity){
		$this->quantity = $quantity;
		$this->completed_quantity = $completed_quantity;
		$this->wish_quantity = $wish_quantity;
		$this->total_quantity = $total_quantity;
		$this->available_quantity = $available_quantity;
		$this->id = $id;
		$this->name = $name;
	}
	
	public function set_suggestInfo($id, $name, $supplier, $limit, $available_quantity, $wish_quantity){
		$this->id = $id;
		$this->name = $name;
		$this->supplier = $supplier;
		$this->limit = $limit;
		$this->available_quantity = $available_quantity;
		$this->wish_quantity = $wish_quantity;
	}
};


Class ModelProducts{
	
	public $id; 
	public $name; 
	public $description;
	public $metric_units;
	public $market_value;
	public $sell_value;
	public $total_quantity; 
	public $available_quantity;
	public $supplied_by;	//id tou supplier
	public $supplier;		//onoma tou supplier
	public $inOrder;
	public $limit;
	
	
	
	//function which return the Supplier of a product
	public function get_productSupplier($product_id){
	
			$connection = Database::createNewConnection();
			$connection->autocommit(true);
			
			$query = "SELECT `fullname` FROM Products, Suppliers WHERE Products.id=? AND `supplied_by`=Suppliers.id";
			if(!$result = $connection->prepare($query)){
				throw new Exception('QueryError');
			}
			
			if(!$result->bind_param('i', $product_id)){
				throw new Exception('QueryError');
			}
			
			if(!$result->execute()){
				throw new Exception('QueryError');
			}
			
			if(!$result->store_result()){
				throw new Exception('QueryError');
			}
			$count = $result->num_rows;
			
			if($count == 1){
				if(!$result->bind_result($fullname)){
					throw new Exception('QueryError');
				}
				
				$result->fetch();
				$this->supplier = $fullname;
			}
			else{
				throw new Exception('There is no such a product ID');
			}
			return $query;
	}
	
	//function which returns the active products
	public static function get_activeProducts($date = null, $availability=false){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
	
		$query = "SELECT Products.`id`, `name`, `description`, `metric_units`, `market_value`, `sell_value`, `total_quantity`, `available_quantity`, `supplied_by`, 
						 IFNULL(SUM(InOrder.quantity), 0) AS `order_quantity`, `fullname`, Products.`reservedOrder_quantity`
				  FROM Products LEFT JOIN(SELECT `product`, `quantity` FROM SupplyOrder, SupplyOrderDetail 
										  WHERE `order`=SupplyOrder.`id` AND `state`='incompleted'  
										  AND `expected_date`!='NULL'";
		if(($date != '') && ($date != null)){
			$query.=" AND `expected_date`<?";
		}
		$query.=") AS InOrder ON Products.`id` = InOrder.`product`, Suppliers 
				 WHERE Suppliers.`status`='active' AND Products.`supplied_by`=Suppliers.`id`
				 GROUP BY Products.`id`
				 ORDER BY `name`";
			
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
			
		if($date != ''){	
			if(!$result->bind_param('s', $date)){
				throw new Exception('QueryError');
			}
		}
			
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
			
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_result($id, $name, $description, $metric_units, $market_value, $sell_value, $total_quantity, 
								 $available_quantity, $supplied_by, $order_quantity, $fullname, $reserved)){
			throw new Exception('QueryError');
		}
		
		$products_info = array();
		while($result->fetch()) {
			if($availability == true){
				$order_quantity-=$reserved;
			}
			if($order_quantity < 0){
				$order_quantity = 0;
			}
			
			$product = new ModelProducts();
			$product->set_productInfo($id, $name, $total_quantity, $available_quantity, $supplied_by, $order_quantity, $fullname);
			$product->set_marketInfo($description, $metric_units, $market_value, $sell_value);
			$products_info[$id] = $product;
		}		
		
		return $products_info;
	}

	//function which return the products that are provided by a Supplier
	public static function get_ProductsBySupplierId($supplier_id, $option=null){
		
		$connection = Database::createNewConnection();
		$connection->autocommit(true);

		$query = "SELECT Products.`id`, `name`, `total_quantity`, `available_quantity`, `fullname`,
						 IFNULL(SUM(InOrder.`quantity`), 0) AS order_quantity
				  FROM Products LEFT JOIN(SELECT `product`, `quantity` FROM SupplyOrder, SupplyOrderDetail 
										  WHERE `order`=SupplyOrder.`id` AND `state`='incompleted') 
										  AS InOrder ON Products.`id` = InOrder.`product`, Suppliers 
				  WHERE ";
		if($option == 'active'){
			$query.="Products.`status`='active' AND ";
		}
		
		$query.="Products.`supplied_by`=Suppliers.`id` AND `supplied_by`=?
				 GROUP BY Products.`id` 
				 ORDER BY `name`";
			
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('i', $supplier_id)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		$count = $result->num_rows;
		
		if(!$result->bind_result($id, $name, $total_quantity, $available_quantity, $fullname, $order_quantity)){
			throw new Exception('QueryError');
		}	

		$products_info = array();
		while($result->fetch()) {
			$product = new ModelProducts();
			$product->set_productInfo($id, $name, $total_quantity, $available_quantity, $supplier_id, $order_quantity, $fullname);
			$products_info[$id] = $product;
		}		
		return $products_info;
	}
	
	//function which returns the index of the pricelist(only for active products)
	public static function viewPricelist(){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "SELECT Products.`id`, `name`, `description`, `metric_units`, `market_value`, `sell_value`, `supplied_by`, `fullname`
				  FROM Products, Suppliers WHERE Products.`status` = 'active' AND Suppliers.`id` = Products.`supplied_by` 
				  ORDER BY `name`";
			
		if(!$result = $connection->query($query)){
			throw new Exception('QueryError');
		}
		
		$pricelist = array();
		while($row = $result->fetch_assoc()){
			$product = new ModelProducts();
			$product->set_productPrice($row['id'], $row['name'], $row['description'], $row['metric_units'], $row['market_value'],
									   $row['sell_value'], $row['supplied_by'], $row['fullname']);
			$pricelist[$row['id']] = $product;
		}
		return $pricelist;
	}
	
	//function which modifies the pricelist
	public static function editPricelist($id, $market_value, $sell_value){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$first_value = true;
		
		if($market_value == '' && $sell_value == ''){
			return ;
		}
		
		$query = "UPDATE Products SET";
		if($market_value != ''){
			$query = $query." `market_value`=?";
			$first_value = false;
		}
		if($sell_value != ''){
			if($first_value == false){
				$query = $query.",";
			}
			$query = $query." `sell_value`=?";
		}
		$query = $query." WHERE id=?";
		
		if(!$result = $connection->prepare($query)){
			throw new QueryError();
		}
		
		if(($sell_value != '') && ($market_value != '')){
			if(!$result->bind_param('ssi', $market_value, $sell_value, $id)){
				throw new QueryError();
			}
		}
		else if($sell_value != ''){
			if(!$result->bind_param('si', $sell_value, $id)){
				throw new QueryError();
			}
		}
		else{
			if(!$result->bind_param('si', $market_value, $id)){
				throw new QueryError();
			}
		}
		
		if(!$result->execute()){
			throw new QueryError();
		}
	}
	
	//function which removes a product
	public static function removeProduct($product_id){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "UPDATE Products SET `status`='deleted' WHERE id=?";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
	
		if(!$result->bind_param('i', $product_id)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
	}
	
	//function which add a new item 
	public static function add_item($name, $description, $metric, $market_value, $sell_value, $quantity, $supplier, $available_quantity, $limit){
		
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		if (!$connection->query("LOCK TABLES Suppliers READ, Products WRITE")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$query = "SELECT IF((SELECT COUNT(*) > 0 FROM Products WHERE `name`=?) ,\"yes\" ,\"no\") AS `answer`";
		
		if(!$result = $connection->prepare($query)){
			throw new QueryError();
		}
		
		if(!$result->bind_param('s', $name)){
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
			throw new ProductAlreadyExists();
		}
		
		$query = "SELECT `id` FROM Suppliers WHERE `fullname`=?";
		
		if(!$result = $connection->prepare($query)){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}		
			
		if(!$result->bind_param('s', $supplier)){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->execute()){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->store_result()){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->bind_result($fullname)){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		$result->fetch();
		
		
		$query = "INSERT INTO `Products` (`name`, `description`, `metric_units`, 
				  `market_value`, `sell_value`, `total_quantity`, `available_quantity`, `supplied_by`, `limit`) 
				  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
				  
		if(!$result = $connection->prepare($query)){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
	
		if(!$result->bind_param('sssddiiii', $name, $description, $metric, $market_value, $sell_value, $quantity, $available_quantity, 
											  $fullname, $limit)){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->execute()){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		$connection->query("UNLOCK TABLES");
	}
	
	//function which returns the info of a product
	public static function get_product_by_id($id){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "SELECT Products.`id`, `name`, `description`, `metric_units`, `market_value`, `sell_value`, `total_quantity`, 
						`available_quantity`, IFNULL(SUM(InOrder.`quantity`), 0) AS `order_quantity`, `supplied_by`, 
						`fullname`, Products.`status`, Products.`limit` AS `limit`
				  FROM Products LEFT JOIN(SELECT `product`, `quantity` FROM SupplyOrder, SupplyOrderDetail 
										  WHERE `order`=SupplyOrder.`id` AND `state`='incompleted'  AND `expected_date`!='NULL') 
										  AS InOrder ON Products.`id` = InOrder.`product`, Suppliers 
				  WHERE Products.`status`='active' AND Suppliers.`id`=`supplied_by` AND Products.`id`=?";
		
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
		
		if(!$result->bind_result($id, $name, $description, $metric_units, $market_value, $sell_value, $total_quantity, $available_quantity,
								 $order_quantity, $supplied_by, $supplier, $status, $limit)){
			throw new Exception('QueryError');
		}
		
		$result->fetch();
		$product = new ModelProducts();
		$product->set_productInfo($id, $name, $total_quantity, $available_quantity, $supplied_by, $order_quantity, $supplier);
		$product->set_marketInfo($description, $metric_units, $market_value, $sell_value);
		$product->limit = $limit;
		
		return $product;		
	}
	
	//function who edits the info of a product
	public static function editProduct($id, $description, $metric_units, $total_quantity, $available_quantity, $supplied_by, $limit){
		
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		if (!$connection->query("LOCK TABLES Products WRITE, Suppliers READ")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$query = "UPDATE Products SET ";
		
		$i = 1;
		$bind_names[0] = '';
		$first_input = false;
		
		if($description != ''){
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $description;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" `description`=?";
			$first_input = true;
		}
		
		if($metric_units != ''){
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $metric_units;
            $bind_names[$i] = &$$bind_name;
			$i++;
			if($first_input == true){
				$query.=",";
			}
			$first_input = true;
			$query.=" `metric_units`=?";
		}
		
		if($total_quantity != ''){
			$bind_names[0].='i';
			$bind_name = 'bind' . $i;
            $$bind_name = $total_quantity;
            $bind_names[$i] = &$$bind_name;
			$i++;
			if($first_input == true){
				$query.=",";
			}
			$first_input = true;
			$query.=" `total_quantity`=?";
		}
		
		if($available_quantity != ''){
			$bind_names[0].='i';
			$bind_name = 'bind' . $i;
            $$bind_name = $available_quantity;
            $bind_names[$i] = &$$bind_name;
			$i++;
			if($first_input == true){
				$query.=",";
			}
			$first_input = true;
			$query.=" `available_quantity`=?";
		}
		
		if($limit != ''){
			$bind_names[0].='i';
			$bind_name = 'bind' . $i;
            $$bind_name = $limit;
            $bind_names[$i] = &$$bind_name;
			$i++;
			if($first_input == true){
				$query.=",";
			}
			$first_input = true;
			$query.=" `limit`=?";
		}
		
		if($supplied_by != ''){
		
			$sql = "SELECT `id` FROM Suppliers WHERE `fullname`=?";
			
			if(!$result = $connection->prepare($sql)){
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
		
			if(!$result->bind_param('s', $supplied_by)){
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
		
			if(!$result->execute()){
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->store_result()){
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->bind_result($supplierID)){
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			$count = $result->num_rows;
			
			if($count == 0){
				$connection->query("UNLOCK TABLES");
				throw new SuppliersDoesNotExists();
			}
			
			$result->fetch();
		
			$bind_names[0].='i';
			$bind_name = 'bind' . $i;
            $$bind_name = $supplierID;
            $bind_names[$i] = &$$bind_name;
			$i++;
			if($first_input == true){
				$query.=",";
			}
			$first_input = true;
			$query.=" `supplied_by`=?";
		}
		$bind_names[0].='i';
		$bind_name = 'bind' . $i;
        $$bind_name = $id;
        $bind_names[$i] = &$$bind_name;
		$query.= " WHERE `id`=?";
		
		if(!$result = $connection->prepare($query)){
			$connection->query("UNLOCK TABLES");
			throw new QueryError($query);
		}
	
		call_user_func_array(array($result,'bind_param'),$bind_names);	
			
		if(!$result->execute()){
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}
	}
	
	//function which suggests products that has to be supplied
	public static function suggestProduct(){
	
		$connection = Database::createNewConnection();
			
		if (!$connection->query("LOCK TABLES Products READ, Products AS Product1 READ, Products AS Products2 READ, Suppliers READ, 
		                         Wishlist READ, CustomerOrder READ")) {
			throw new QueryError();
		}
		
		$query = "SELECT Products.`id` AS `id`, Products.`name`, Suppliers.`fullname` AS `fullname`, Products.`limit`, 
                         Products.`available_quantity`, IFNULL(SUM(Wish.`quantity`), 0) AS `wish_quantity`
                  FROM Products, Suppliers, 
                  Products AS Product1 LEFT JOIN (SELECT Products2.`id` AS `id`, Wishlist.`quantity` AS `quantity` 
				                                  FROM Products Products2, Wishlist, CustomerOrder 
												  WHERE Wishlist.`product`=Products2.`id` AND Wishlist.`order`=CustomerOrder.`id` 
												  AND (CustomerOrder.`state`='incompleted' OR CustomerOrder.`state`='modified'))
                                       AS Wish ON Product1.`id`=Wish.`id`
				  WHERE Suppliers.`id`=Products.`supplied_by` AND Products.`limit`*11/10 >= Products.`available_quantity` 
				  AND Product1.`id`=Products.`id`
				  GROUP BY Products.`id`
				  ORDER BY Suppliers.`fullname`";
				  
		if(!$result = $connection->prepare($query)){
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
		
		if(!$result->bind_result($id, $name, $supplier, $limit, $available_quantity, $wishQuantity)){
			$connection->query("UNLOCK TABLES");
			throw new Exception('QueryError');
		}
		
		$products = array();
		
		while($result->fetch()){
			$product = new ProductsInOrder();
			$product->set_suggestInfo($id, $name, $supplier, $limit, $available_quantity, $wishQuantity); 
			array_push($products, $product);
		}
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}
		
		return $products;
	}
	
	public function set_productInfo($id, $name, $total_quantity, $available_quantity, $supplied_by, $inOrder, $supplier){
		$this->id = $id;
		$this->name = $name;
		$this->total_quantity = $total_quantity;
		$this->available_quantity = $available_quantity;
		$this->supplied_by = $supplied_by;
		$this->inOrder = $inOrder;
		$this->supplier = $supplier;
	}
	
	private function set_productPrice($id, $name, $description, $metric_units, $market_value, $sell_value, $supplied_by, $supplier){
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->metric_units = $metric_units;
		$this->market_value = $market_value;
		$this->sell_value = $sell_value;
		$this->supplied_by = $supplied_by;
		$this->supplier = $supplier;
	}
	
	public function set_marketInfo($description, $metric_units, $market_value, $sell_value){
		$this->description = $description;
		$this->metric_units = $metric_units;
		$this->market_value = $market_value;
		$this->sell_value = $sell_value;
	}
	
}

?>