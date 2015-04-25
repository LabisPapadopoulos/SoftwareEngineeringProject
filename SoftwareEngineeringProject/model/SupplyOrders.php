<?php

Class SupplyOrders{

	public $id; 
	public $supplier;
	public $supplier_name;
	public $order_date;
	public $expected_date;
	public $receipt_date; 
	public $state;
	public $product;
	public $product_name;
	public $quantity;
	public $receipt_quantity;
	public $available_quantity;
	

	
	//A function which returns orders(all,completed,incompleted)
	public static function get_orders($options = null, $from=null, $to=null){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$i = 1;
		$bind_names[0] = '';
		$argExist = false;
		
		$query = "SELECT SupplyOrder.id AS `order_id`,`Suppliers_id`,`fullname`,`order_date`,`expected_date`,`receipt_date`,`state` 
		FROM SupplyOrder, Suppliers WHERE SupplyOrder.Suppliers_id=Suppliers.id";
		
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
		
		if($options == 'incompleted'){
			$query.=" AND `state`='incompleted' ORDER BY `expected_date` DESC";
		}
		else if($options == 'completed'){
			$query.=" AND `state`='completed' ORDER BY `receipt_date` DESC";
		}
		else{
			$query.=" ORDER BY `state` DESC, `order_date` DESC";
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
		
		if(!$result->bind_result($order_id, $Suppliers_id, $fullname, $order_date, $expected_date, $receipt_date, $state)){
			throw new Exception('QueryError');
		}

		$order_info = array();
		while($result->fetch()){
			$order = new SupplyOrders();
			$order->set_orderInfo($order_id, $Suppliers_id, $fullname, $order_date, $expected_date, $receipt_date, $state);
			$order_info[$order_id] = $order;
		}
		return $order_info; 
	}
	
	//function which returns the order of a specific supplier
	public static function get_orders_by_id($id, $options = null){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
	
		$query = "SELECT SupplyOrder.id AS `order_id`,`Suppliers_id`,`fullname`,`order_date`,`expected_date`,`receipt_date`,`state` 
		FROM SupplyOrder, Suppliers WHERE SupplyOrder.Suppliers_id=Suppliers.id AND Suppliers.id = ?";
		
		if($options == 'incompleted'){
			$query.=" AND `state`='incompleted' ORDER BY `expected_date` DESC";
		}
		else if($options == 'completed'){
			$query.=" AND `state`='completed' ORDER BY `receipt_date` DESC";
		}
		else{
			$query.=" ORDER BY `state` DESC, `order_date` DESC";
		}
		
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
		
		if(!$result->bind_result($order_id, $Suppliers_id, $fullname, $order_date, $expected_date, $receipt_date, $state)){
			throw new Exception('QueryError');
		}
		
		$order_info = array();
		while($result->fetch()) {
			$order = new SupplyOrders();
			$order->set_orderInfo($order_id, $Suppliers_id, $fullname, $order_date, $expected_date, $receipt_date, $state);
			$order_info[$order_id] = $order;
		}		
		return $order_info;
	}

	//function which creates a new Supply Order and returns the orderID
	public static function create_new_order($Suppliers_id, $order_date, $expected_date, $products, $quantity){

		$connection = Database::createNewConnection();
		
		if(!$connection->autocommit(false)){
			throw new QueryError();
		}
		
		if (!$connection->query("LOCK TABLES SupplyOrderDetail WRITE, SupplyOrder WRITE, Products READ")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if($expected_date != ''){
			$query = "INSERT INTO SupplyOrder(`Suppliers_id`, `order_date`, `expected_date`) VALUES(?,?,?)";
		}
		else{
			$query = "INSERT INTO SupplyOrder(`Suppliers_id`, `order_date`) VALUES(?,?)";
		}
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if($expected_date != ''){
			if(!$result->bind_param('iss', $Suppliers_id, $order_date, $expected_date)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
		}
		else{
			if(!$result->bind_param('is', $Suppliers_id, $order_date)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
		}
		
		if(!$result->execute()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		$Order_id = $connection->insert_id;
		
		$query = "INSERT INTO SupplyOrderDetail(`order`,`product`,`quantity`, `price`) VALUES(?,?,?,(SELECT `sell_value` FROM Products WHERE `id`=?))";
		
		$i = 0;
		foreach($products as $product){
			
			if(!$result = $connection->prepare($query)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->bind_param('iiii', $Order_id, $product, $quantity[$i], $product)){
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
			$i++;
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
		
		return $Order_id;
	}
	
	//function who edits an existed order
	public static function editOrder($supplierID, $orderID, $expected_date, $productsID, $quantities){
		
		$connection = Database::createNewConnection();
		
		if(!$connection->autocommit(false)){
			throw new QueryError();
		}
		
		if (!$connection->query("LOCK TABLES SupplyOrderDetail WRITE, SupplyOrder WRITE")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$query = "SELECT `product` FROM SupplyOrderDetail WHERE `order`=? ORDER BY `product`";
		
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
			$sortedQuantity[$product] = $quantities[$i];
			$i++;
		}
		
		sort($productsID);
		
		$oldSize = count($oldProducts);
		$newSize = count($productsID);
		
		if($expected_date != ''){
			$query = "UPDATE SupplyOrder SET `expected_date`=? WHERE `id`=?";
			
			if(!$result = $connection->prepare($query)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
				
			if(!$result->bind_param('si', $expected_date, $orderID)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			if(!$result->execute()){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
		}
		
		$add = "INSERT INTO SupplyOrderDetail(`order`, `product`, `quantity`) VALUES(?,?,?)";
		$update = "UPDATE SupplyOrderDetail SET `quantity`=? WHERE `order`=? AND `product`=?";
		$remove = "DELETE FROM SupplyOrderDetail WHERE `order`=? AND `product`=?";
		
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
				$newProduct = $productsID[$j];
			}
			else if($oldSize > 0){
				$newProduct = $oldProducts[$i]+1;
			}
		
			if($oldProduct < $newProduct){		
				$action = "remove";						
				$currentProduct = $oldProduct;
				$i++;
				$oldSize--;
			}
			else if($oldProduct > $newProduct){
				$action = "add";
				$currentProduct = $newProduct;
				$j++;
				$newSize--;
			}
			else{
				$action = "update";
				$currentProduct = $newProduct;
				$j++;
				$i++;
				$oldSize--;
				$newSize--;
			}
			
			switch($action){
			
				case "remove":
					if(!$result = $connection->prepare($remove)){
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
					break;
				case "update":
					if(!$result = $connection->prepare($update)){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
						
					if(!$result->bind_param('iii', $sortedQuantity[$currentProduct], $orderID, $currentProduct)){
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
				case "add":	
					if(!$result = $connection->prepare($add)){
						$connection->rollback();
						$connection->autocommit(true);
						$connection->query("UNLOCK TABLES");
						throw new QueryError();
					}
						
					if(!$result->bind_param('iii', $orderID, $currentProduct, $sortedQuantity[$currentProduct])){
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
	
	//function which returns the products of an order
	public static function viewOrderDetails($id){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
	
		$query = "SELECT `product`, `name`, `quantity`, `receipt_quantity`, Products.`available_quantity` FROM SupplyOrderDetail, Products 
				WHERE `order`=? AND `id`=`product`";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('s', $id)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_result($product, $name, $quantity, $receipt_quantity, $available_quantity)){
			throw new Exception('QueryError');
		}
		
		$order_product_id = array();
		$order_product_name = array();
		$order_quantity = array();
		$order_receipt_quantity = array();
		$order_available_quantity = array();
		$i = 0;
		while($result->fetch()) {
			$order_product_id[$i] = $product;
			$order_product_name[$product] = $name;
			$order_quantity[$product] = $quantity;
			$order_receipt_quantity[$product] = $receipt_quantity;
			$order_available_quantity[$product] = $available_quantity;
			$i++;
		}	

		$order = new SupplyOrders();
		$order->set_orderDetail($id, $order_product_id, $order_product_name, $order_quantity, $order_receipt_quantity);
		$order->set_availableQuantity($order_available_quantity);
		return $order;
	}
	
	//function which returns the general info of an order 
	public static function viewOrder($id){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
	
		$query = "SELECT SupplyOrder.id AS `orderID`, `Suppliers_id`, `fullname`, `order_date`, `expected_date`, `receipt_date`, `state`  
				  FROM SupplyOrder, Suppliers WHERE SupplyOrder.id=? AND Suppliers_id = Suppliers.id";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('s', $id)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_result($orderID, $supplier, $supplier_name, $order_date, $expected_date, $receipt_date, $state)){
			throw new Exception('QueryError');
		}
	
		$result->fetch();
		$order = new SupplyOrders();
		$order->set_orderInfo($orderID, $supplier, $supplier_name, $order_date, $expected_date, $receipt_date, $state);

		return $order;	
	}
	
	//function which updates the status of an order as completed
	public static function updateStatus($id){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "UPDATE SupplyOrder SET `state`='completed' WHERE id=?";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('i', $id)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
	}
	
	//function which set the quantity of a product of a order that was received
	public static function  update_item_received($order_id, $product_id, $received_quantity){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "UPDATE SupplyOrderDetail SET `receipt_quantity`=$received_quantity WHERE `order`=$order_id AND `product`=$product_id";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('iii', $receipt_quantity, $order_id, $product_id)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
	}
	
	//function which receive an order
	public static function receive_order($order_id, $receipt_date, $product, $quantity, $desired){
	
		$connection = Database::createNewConnection();

		$exceptionalMessage = new ExceptionalMessages();
		$exception = false;
		$all_queries_done = true;
		
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES SupplyOrderDetail WRITE, SupplyOrder WRITE, Products WRITE, 
								 Products AS Product1 READ, CustomerOrderDetail READ, CustomerOrder READ")) {
			$connection->autocommit(true);
			throw new Exception('QueryError');
		}
		
		$findInfo = "SELECT Products.`reservedOrder_quantity`,  IFNULL(SUM(InOrder.`reservedQuantity`), 0) AS `order_quantity`
					 FROM Products, 
						Products AS Product1 LEFT JOIN(SELECT `product`, `reservedQuantity` FROM CustomerOrderDetail, CustomerOrder 
								WHERE CustomerOrder.`id`=CustomerOrderDetail.`order`AND (CustomerOrder.`state`='incompleted'
								OR CustomerOrder.`state`='modified')) AS InOrder ON Product1.`id` = InOrder.`product` 
					 WHERE Product1.`id`=Products.`id` AND Products.`id`=?";
		
		$query = "UPDATE `SupplyOrder` SET `receipt_date`=?, `state`='completed' WHERE id=?";
		
		$result = $connection->prepare($query);
		$result->bind_param('si', $receipt_date, $order_id);
		$result->execute() ? null : $all_queries_done=false;
	
		$query_info = "UPDATE `SupplyOrderDetail` SET `receipt_quantity`=? WHERE `order`=? AND `product`=?";
		$result_info = $connection->prepare($query_info);
		
		$query_detail = "UPDATE Products SET `total_quantity`=`total_quantity`+?, `available_quantity`=`available_quantity`+?";
		$query_detail.= ", `reservedOrder_quantity`=`reservedOrder_quantity`-?, `status`='active' WHERE `id`=?";
		$result_detail = $connection->prepare($query_detail);
		
		$i = 0;
		foreach($product as $product_id){
		
			if(!$result = $connection->prepare($findInfo)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
		
			if(!$result->bind_param('i', $product_id)){
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
		
			if(!$result->bind_result($productReservedQuantity, $ordersReservedQuantity)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
		
			if($productReservedQuantity > $ordersReservedQuantity){
				$update_reserved = $ordersReservedQuantity;
			}
			else{
				$update_reserved = $productReservedQuantity;
			}
			
		
			$result_info->bind_param('iii', $quantity[$i], $order_id, $product_id);
			$result_info->execute() ? null : $all_queries_done=false;
			
			$result_detail->bind_param('iiii', $quantity[$i], $quantity[$i], $update_reserved, $product_id);
			$result_detail->execute() ? null : $all_queries_done=false;
			
			if($quantity[$i] < $desired[$i]){	
				$exceptionalMessage->setOrder($product_id, $desired[$i] - $quantity[$i]);
				$exception = true;
			}
			else if($quantity[$i] > $desired[$i]){
				$exceptionalMessage->setOrder($product_id, $quantity[$i] - $desired[$i], 'abounding');
				$exception = true;
			}
			
			$i++;
		}
		
		if($all_queries_done == true){
			if (!$connection->commit()){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		}
		else{
			if (!$connection->rollback()){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new Exception('QueryError');
			}
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new Exception('QueryError');
		}
		
		if($exception == true){
			$exceptionalMessage->completeOrder($order_id, $receipt_date, 'supplier');
		}
		
		return $exceptionalMessage;
	}
	
	//function which deletes an order
	public static function deleteOrder($orderID){
		
		$connection = Database::createNewConnection();
		
		$connection->autocommit(true);
		
		$query = "DELETE FROM SupplyOrder WHERE `id`=?";
		
		if(!$result = $connection->prepare($query)){
			throw new QueryError();
		}
		
		if(!$result->bind_param('i', $orderID)){
			throw new QueryError();
		}
		
		if(!$result->execute()){
			throw new QueryError();
		}
		
	}
	
	public static function findSells($from, $to, $orderState=null){
	
		$connection = Database::createNewConnection();
		
		$i = 3;
		$bind_names[0] = 'ss';
		$bind_names[1] = &$from;
		$bind_names[2] = &$to;
		
		$query = "SELECT `order_date`, SUM(IFNULL(`price`*`receipt_quantity`,`price`*`quantity`)) AS `cost`  
				  FROM SupplyOrder, SupplyOrderDetail 
				  WHERE `order_date`>=? AND `order_date` <= ? AND SupplyOrderDetail.`order`=SupplyOrder.`id`"; 
				  
		if($orderState != null){
			$bind_names[0].='s';
			$bind_name = 'bind' . $i;
            $$bind_name = $orderState;
            $bind_names[$i] = &$$bind_name;
			$i++;
			$query.=" AND SupplyOrder.`state`=?";
		}
		$query.=" GROUP BY SupplyOrder.`order_date`";	

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
		
		if(!$result->bind_result($orderDate, $cost)){
			throw new Exception('QueryError');
		};

		$sells = array();
		$i = 0;
		while($result->fetch()){
			$row = array();
			$row['orderDate'] = $orderDate;
			$row['cost'] = $cost;
			$sells[$i] = $row;
			$i++;
		}
		return $sells;		
	
	}

	private function set_orderInfo($id, $supplier, $supplier_name, $order_date, $expected_date, $receipt_date, $state){
		$this->id = $id;
		$this->supplier = $supplier;
		$this->supplier_name = $supplier_name;
		$this->order_date = $order_date;
		$this->expected_date = $expected_date;
		$this->receipt_date = $receipt_date;
		$this->state = $state;
	}
	
	private function set_orderDetail($id, $product, $product_name, $quantity, $receipt_quantity){
		$this->id = $id;
		$this->product = $product;
		$this->product_name = $product_name;
		$this->quantity = $quantity;
		$this->receipt_quantity = $receipt_quantity;
	}
	
	public function set_availableQuantity($available_quantity){
		$this->available_quantity = $available_quantity;
	}
	
}

?>