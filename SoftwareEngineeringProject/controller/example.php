<?php
include '../model/Users.php';
include '../model/Customers.php';
include '../model/SupplyOrders.php';
include '../model/CustomerOrders.php';
include '../model/Products.php';
include '../model/Suppliers.php';
include '../model/Wishlist.php';
include '../model/Discounts.php';
include '../model/ExceptionalMessages.php';
	//$user = ModelUsers::createNewUser('Panakia','1234','Legete etc',21312,'1111111','example@dfs','seller');
	//$user = Customers::insertNewCustomer('Kotsovolos AE',1234,'Athens Greece Nikolopoulou 12','3432-4332','',1);
	//$user = ModelUsers::deleteUser('seller');
	//$user = Customers::deleteCustomer('Xalubdourgiki AE');
	//$user = Customers::showActiveCustomers();
	//$user = ModelUsers::login('XristosMal','2013');
	//ModelUsers::deleteUser('saksoo');
	//ModelUsers::editProfile('skatoules', '', '', '', '210-1234123', 'email@example.gr');
	//ModelUsers::modifyProfile('storekeeper','admin',true);
	// $users = ModelUsers::list_all('active');
	// foreach($users as $user){
		// echo "id: ".$user->id.' ';
		// echo 'username: '.$user->username.' ';
		// echo 'fullname: '.$user->fullname.' ';
		// echo 'email: '.$user->email.' ';
		// echo 'vat: '.$user->vat.' ';
		// echo 'phone_number: '.$user->phone_number.' ';
		// echo 'type: '.$user->type.'<br/>';
	// }
	// $user = ModelUsers::get_user(8);
	// if($user!=null){
		// echo "id: ".$user->id.' ';
		// echo 'username: '.$user->username.' ';
		// echo 'fullname: '.$user->fullname.' ';
		// echo 'email: '.$user->email.' ';
		// echo 'vat: '.$user->vat.' ';
		// echo 'phone_number: '.$user->phone_number.' ';
		// echo 'type: '.$user->type.'<br/>';
	// }
	
	// $suppliers = ModelSuppliers::get_suppliers();
	// foreach($suppliers as $supplier){
		// echo 'id: '.$supplier->id.' ';
		// echo 'fullname: '.$supplier->fullname.' ';
		// echo 'vat: '.$supplier->vat.' ';
		// echo 'location: '.$supplier->location.' ';
		// echo 'phone_number: '.$supplier->phone_number.' ';
		// echo 'email: '.$supplier->email.' ';
		// echo '<br/>';
	// }
	// $supplier = ModelSuppliers::get_supplier_by_id(1);
	// foreach($suppliers as $supplier){
		// echo 'id: '.$supplier->id.' ';
		// echo 'fullname: '.$supplier->fullname.' ';
		// echo 'vat: '.$supplier->vat.' ';
		// echo 'location: '.$supplier->location.' ';
		// echo 'phone_number: '.$supplier->phone_number.' ';
		// echo 'email: '.$supplier->email.' ';
		// echo 'status: '.$supplier->status.' ';
		// echo '<br/>';
	//}
	//$answer = ModelSuppliers::insertNewSupplier('Mitsos AEtemp2', 1111, 'Athens', '210-9712541', '');
	//ModelSuppliers::deleteSupplier('Mitsos AEtemp2');
	//$orders = SupplyOrders::get_orders('incompleted');
	// $orders = SupplyOrders::get_orders_by_id(1,'');
	// foreach($orders as $order){
		// echo 'id: '.$order->id.' ';
		// echo 'Supplier_id: '.$order->supplier.' ';
		// echo 'supplier: '.$order->supplier_name.' ';
		// echo 'ordet date: '.$order->order_date.' ';
		// echo 'expected date: '.$order->expected_date.' ';
		// echo 'receipt_date: '.$order->receipt_date.' ';
		// echo 'state: '.$order->state.' ';
		// echo '<br/>';
	// }
	// $orders = SupplyOrders::viewOrder(122);
	// echo 'id: '.$orders->id.' ';
	// echo 'Supplier_id: '.$orders->supplier.' ';
	// echo 'supplier: '.$orders->supplier_name.' ';
	// echo 'ordet date: '.$orders->order_date.' ';
	// echo 'expected date: '.$orders->expected_date.' ';
	// echo 'receipt_date: '.$orders->receipt_date.' ';
	// echo 'state: '.$orders->state.' ';
	// echo '<br/>';
	//SupplyOrders::updateStatus(122);
	// $products[0] = 2;
	// $products[1] = 1;
	// $products[2] = 3;
	
	// $quantity[0] = 10;
	// $quantity[1] = 23;
	// $quantity[2] = 50;
	// $orderid = SupplyOrders::create_new_order(10, '2013-04-20', '', $products, $quantity);
	// echo 'orderid: '.$orderid;
	// $order = SupplyOrders::viewOrderDetails(114);
	// echo 'id: '.$order->id.'<br/>';
	// foreach($order->product as $product){
		// echo 'product id: '.$product.' ';
		// echo 'product name: '.$order->product_name[$product].' ';
		// echo 'quantity: '.$order->quantity[$product].' ';
		// echo 'receipt_quantity: '.$order->receipt_quantity[$product].' ';
		// echo '<br/>';
	// }
	//if($user == NULL){
	//	echo 'mi egkuro login';
	//}
	//else{
	//	echo 'egkuro<br/>';
	//	echo "id ".$user->id.'<br/>';
	//	echo 'username '.$user->username.'<br/>';
	//	echo 'fullname '.$user->fullname.'<br/>';
	//	echo 'email '.$user->email.'<br/>';
	//	echo 'vat '.$user->vat.'<br/>';
	//	echo 'phone_number '.$user->phone_number.'<br/>';
	//	echo 'type '.$user->type.'<br/>';
	//}
	//ModelUsers::createNewUser('XristosMal', '2013', 'Xristos Mallios', '1345', '210-3465887', 'xristos@hotmail.com', 'admin');
	//$user = Customers::editCustomer('Xalubdourgiki AE', 12345,'','','xalubdourgiki@gmail.com');
	//$user = SupplyOrders::get_orders_by_id(1);
	
	// $products[0] = 2;
	// $products[1] = 1;
	// $products[2] = 3;
	
	// $quantity[0] = 10;
	// $quantity[1] = 23;
	// $quantity[2] = 5;
	
	// $user = SupplyOrders::create_new_order('Vasilopoulos', '2013-04-05','2013-04-08', $products, $quantity);
	
	//$user = ModelProducts::get_activeProducts();
	//$user = ModelProducts::get_ProductsBySupplierId(4);
	//$user = ModelProducts::viewPricelist();
	//foreach($user as $order){
	//	echo $order->id." ".$order->name." ".$order->description." ".$order->metric_units." ".$order->market_value." ".$order->sell_value."</br>";
	//}
	//$user = ModelProducts::editPricelist(3, '', '22');
	// $order = SupplyOrders::viewOrderDetails(25);
	// foreach($user as $order){
		// echo 'id: '.$order->id.' ';
		// echo 'id: '.$order->order_date.' ';
		// echo 'id: '.$order->expected_date.' ';
		// echo 'id: '.$order->id.' ';
		// echo 'id: '.$order->id.' ';
		
	// echo $order;
	// $order = SupplyOrders::viewOrder(1);
	// $user = SupplyOrders::get_orders();
	// $user = ModelProducts::get_activeProducts();
	// $order;
	//foreach($user as $order){
	//	$order->get_productSupplier($order->id);
	//	echo $order->id." ".$order->name." ".$order->total_quantity." ".$order->available_quantity." ".$order->supplier." "."</br>";
	//}
	//echo $user;
	//$user = ModelSuppliers::insertNewSupplier('Mitsos AE',1234,'Patra','3432-4332','asdas@gmail.com');
	//$user = ModelSuppliers::deleteSupplier('Vasilopoulos');
	//$user = ModelProducts::removeProduct(1);
	//SupplyOrders::updateStatus(2);
	//SupplyOrders::update_item_received(25, 5, 103)
	// $product[0] = 9;
	// $product[1] = 10;
	// $order[0] = 95;
	// $order[1] = 10;
	// $query = SupplyOrders::receive_order(113, '2013-04-20', $product, $order);
	// echo 'query '.$query;
	//echo $user;
	//$result = CustomerOrders::get_orders();
	//foreach($result as $x){
	//	echo $x->customer_name." ".$x->seller_name." ".$x->receipt_date." ".$x->order_date."<br/>";
	//}
	// $product = new ModelProducts();
	// $query = $product->get_productSupplier(6);
	// echo 'supplier: '.$product->supplier.'<br/>';
	// echo 'query: '.$query;
	// $products = ModelProducts::get_activeProducts();
	// //$products = ModelProducts::get_ProductsBySupplierId(1);
	// foreach($products as $product){
		// echo 'id: '.$product->id.' ';
		// echo 'name: '.$product->name.' ';
		// echo 'available_quantity: '.$product->available_quantity.' ';
		// echo 'total_quantity: '.$product->total_quantity.' ';
		// echo 'inOrder: '.$product->inOrder.' ';
		// echo '<br/>';
	// }
	//ModelProducts::editPricelist(4,10,'');
	//ModelProducts::removeProduct(6);
	//ModelProducts::add_item('Tyropites', 'Me eidiki gemisi', 'Kommatia', 42, 50, 35, 10);
	// $price = ModelProducts::get_product_by_id(12);
	// echo 'id: '.$price->id.' ';
	// echo 'name: '.$price->name.' ';
	// echo 'description: '.$price->description.' ';
	// echo 'metric_units: '.$price->metric_units.' ';
	// echo 'market_value: '.$price->market_value.' ';
	// echo 'sell_value: '.$price->sell_value.' ';
	// echo 'supplier: '.$price->supplier.' ';
	// echo 'supplierID: '.$price->supplied_by.' ';
	// echo 'Total quantity: '.$price->total_quantity.' ';
	// echo 'Available quantity: '.$price->available_quantity.' ';
	// echo 'Order quantity: '.$price->inOrder;
	// echo '<br/>';
	// $pricelist = ModelProducts::viewPricelist();
	// foreach($pricelist as $price){
		// echo 'id: '.$price->id.' ';
		// echo 'name: '.$price->name.' ';
		// echo 'description: '.$price->description.' ';
		// echo 'metric_units: '.$price->metric_units.' ';
		// echo 'market_value: '.$price->market_value.' ';
		// echo 'sell_value: '.$price->sell_value.' ';
		// echo 'supplier: '.$price->supplier.' ';
		// echo 'supplierID: '.$price->supplied_by.' ';
		// echo '<br/>';
	// }
	// $customers = Customers::showActiveCustomers('deleted');
	// foreach($customers as $customer){
		// echo 'id: '.$customer->id.' ';
		// echo 'fullname: '.$customer->fullname.' ';
		// echo 'vat: '.$customer->vat.' ';
		// echo 'phone_number: '.$customer->phone_number.' ';
		// echo 'email: '.$customer->email.' ';
		// echo 'location: '.$customer->location.' ';
		// echo 'status: '.$customer->status.' ';
		// echo 'inserted_by_id: '.$customer->inserted_by.' ';
		// echo 'inserted_by_name: '.$customer->inserted_by_name.' ';
		// echo '<br/>';
	// }
	//Customers::insertNewCustomer('Fullname1', 1234, 'location1', 'phone_number1', '', 4);
	// $customer = Customers::getCustomer(6);
	// echo 'id: '.$customer->id.' ';
		// echo 'fullname: '.$customer->fullname.' ';
		// echo 'vat: '.$customer->vat.' ';
		// echo 'phone_number: '.$customer->phone_number.' ';
		// echo 'email: '.$customer->email.' ';
		// echo 'location: '.$customer->location.' ';
		// echo 'status: '.$customer->status.' ';
		// echo 'inserted_by_id: '.$customer->inserted_by.' ';
		// echo 'inserted_by_name: '.$customer->inserted_by_name.' ';
		// echo '<br/>';
	//ModelProducts::editProduct(11, 'Από τη Κολομβία', '', 160, 140, 3);
	//Customers::editCustomer("Fullname1", 12345, "Ilioupoli, Attiki", "210-9134652", "sdi0700103@di.uoa.gr");
	// $product[0] = 8;
	//$product[1] = 14;
	// $order[0] = 600;
	//$order[1] = 10;
	
	// CustomerOrders::create_new_order(4, 1, '2013-04-23', '', $product, $order)
	
	// $orders = CustomerOrders::get_orders(4, 'incompleted');
	// foreach($orders as $order){
		// echo 'id: '.$order->id.' ';
		// echo 'order date: '.$order->order_date.' ';
		// echo 'expected_date: '.$order->expected_date.' ';
		// echo 'receipt_date: '.$order->receipt_date.' ';
		// echo 'state: '.$order->status.' ';
		// echo 'seller id: '.$order->seller_id.' ';
		// echo 'seller_name: '.$order->seller_name.' ';
		// echo 'Customer id : '.$order->customer_id.' ';
		// echo 'Customer name: '.$order->customer_name.' ';
		// echo '<br/>';
	// }
	// $product[0] = 8;
	// $product[1] = 5;
	// $product[2] = 16;
	// $quantity[0] = 260; 
	// $quantity[1] = 70;
	// $quantity[2] = 10;
	// $query = CustomerOrders::create_new_order(4, 1, '2013-04-27', '', $product, $quantity);
	//echo 'query '.$query;
	// $product[0] = 5;
	// $product[1] = 8;
	// $product[2] = 10;
	// $product[3] = 16;
	// $quantity[0] = 70; 
	// $quantity[1] = 260;
	// $quantity[2] = 246;
	// $quantity[3] = 264;
	// //SupplyOrders::receive_order(133, '2013-04-27', $product, $quantity);


// $order = 189;
// $date = "2013-05-11";
// $product[0]=1;	
// $product[1]=5;	
// $product[2]=9;	
// $product[3]=11;

// $quantity[0]=500;	
// $quantity[1]=441;	
// $quantity[2]=500;	
// $quantity[3]=630;	
// $result = CustomerOrders::send_order($order, $date, $product, $quantity);
	// echo $result;
	// $wishlist = new Wishlist();
	
	// $wishlist->setDate('2013-04-27');
	// $wishlist->setNewWish(5, 6);
	// $wishlist->setNewWish(10, 9);
	
	// $wishlist->completeWish(1);
	
	// $i = 0;
	// foreach($wishlist->id_array as $id){
		// echo 'id: '.$id.' ';
		// echo 'order: '.$wishlist->order.' ';
		// echo 'produt: '.$wishlist->product_array[$i].' ';
		// echo 'quantity: '.$wishlist->quantity_array[$i].' ';
		// echo '<br/>';
		// $i++;
	// }
	// echo 'date: '.$wishlist->wishDate;
	
	
	// $order = CustomerOrders::get_order_by_id(133);
	// echo 'order_id: '.$order->id.' '.'<br/>';
	// echo 'order_date: '.$order->order_date.' '.'<br/>';
	// echo 'expected_date: '.$order->expected_date.' '.'<br/>';
	// echo 'receipt_date: '.$order->receipt_date.' '.'<br/>';
	// echo 'status: '.$order->status.' '.'<br/>';
	
	// foreach($order->products as $product){
		// echo ' <b>Product id:</b> '.$product->id.' ';
		// echo ' <b>Product name:</b> '.$product->name.' ';
		// echo ' <b>Quantity:</b> '.$product->quantity.' ';
		// echo ' <b>Deliverable quantity:</b> '.$product->deliverable_quantity.' ';
		// //var_dump($product);
		// echo '<br/>';
	// }
	
	// $customer = $order->customer;
	// echo '<br/><b>Customer ID:<b/> '.$customer->id.'<br/>';
	//echo var_dump($order->seller);
	
	//echo $order;
	 // $customer = Customers::getCustomer(2);
	 // echo 'id '.$customer->id;
	 // echo ' name '.$customer->fullname;
	 // echo ' vat '.$customer->vat;
	 // echo ' email '.$customer->email;
	 // echo ' location '.$customer->location;
	 
	 //$result = ModelUsers::createConfirmationLink('sdi0700103@di.uoa.gr');
	 //echo 'create confirmation done';	 
	 //$result = ModelUsers::retrievePassword('46a3c929e720ee076cb9aa882dc1a654041b7b3c');
	 //echo 'retrieve password done: '.$result;
	 
	 // $result = Discounts::getDiscounts(3);
	 
	 // echo 'Customer<br/>';
	 // echo 'ID: '.$result->customer->id.' ';
	 // echo 'NAME: '.$result->customer->fullname.' ';
	 // echo 'EMAIL: '.$result->customer->email.' ';
	 // echo 'VAT: '.$result->customer->vat.' ';
	 // echo 'PHOE NUMBER: '.$result->customer->phone_number.' ';
	 
	 // foreach($result->products as $product){
		// echo 'ID: '.$product->id.' ';
		// echo 'NAME: '.$product->name.' ';
		// echo 'DESCRIPTION: '.$product->description.' ';
		// echo 'METRIC UNITS: '.$product->metric_units.' ';
		// echo 'TOTAL QUALITY: '.$product->total_quantity.' ';
		// echo 'AVAILABLE QUALITY: '.$product->available_quantity.' ';
		// echo 'inOrder QUALITY: '.$product->inOrder.' ';
		// echo 'SELL VALUE: '.$product->sell_value.' ';
		// echo 'MARKET VALUE: '.$product->market_value.' ';
		// echo 'discount: '.$result->discount[$product->id].' ';
		// echo '<br/>';
	 // }
	 
	 // ModelProducts::editProduct(3, 'Description', 'Metrikes Monades', $_POST['quantity'], 
											       // $_POST['quantity'], $_POST['supplier']);

												   
	// $customer = 3;
	// $products[0] = 7;
	// $products[1] = 9;
	// $products[2] = 11;
	// $discounts[7] = 0.2;
	// $discounts[9] = 0.3;
	// $discounts[11] = 0.15;
	// Discounts::makeNewDiscounts($customer, $products, $discounts);
	//echo $nowFormat = date('Y-m-d h:m:s');
	// $result = ModelUsers::retrievePassword('2e9c04de202fbe098a62cba0c043c61b55d10185');
	// echo $result;
	//ModelUsers::createNewUser('Username', 'pass', 'Tha legete kapws', 2321321, '12321', 'sdi0700103@di.uoa.com', 'manager');
	
	// $order = SupplyOrders::viewOrderDetails(133);
	// foreach($order->product as $product){
		// echo 'id: '.$product.' ';
		// echo 'onoma: '.$order->product_name[$product].' ';
		// echo 'posothta: '.$order->quantity[$product].' ';
		// echo 'diathesimi posothta: '.$order->available_quantity[$product].' ';
		// echo '<br/>';
	// }
	
	// $exceptionalMessages = new ExceptionalMessages();
	// $exceptionalMessages->setOrder(3, 1);
	// $exceptionalMessages->setOrder(4, 2);
	// $exceptionalMessages->setOrder(5, 3);
	// $exceptionalMessages->completeOrder(133, '2013-05-07');
	
	//CustomerOrders::editOrders(155, 1, '2013-05-09', 'productsID', 'quantity');
	
	// $array[0] = 2;
	// $array[1] = 1;
	// $array[2] = 3;
	
	// sort($array);
	
	// foreach ($array as $key=>$value){
		// echo 'Key '.$key.' value '.$value;
		// echo '<br/>';
	// }
	
	// echo 'size '.count($array);
	
	// $order = 185;
	// $customer = 3;
	// $date = '2014-05-05';
	
	// $product[0] = 1;
	// //$product[1] = 5;
	// //$product[1] = 6;
	// //$product[2] = 9;
	// //$product[3] = 11;
	
	// $quantity[0] = 0;
	// //$quantity[1] = 100;
	// $quantity[1] = 460;
	// $quantity[2] = 500;
	// $quantity[3] = 122;
	
	// $product[0] = 5;
	// $product[1] = 9;
	// $product[2] = 11;
	
	// $quantity[0] = 500;
	// $quantity[1] = 80;
	// $quantity[2] = 522;
	
	//$result = CustomerOrders::editOrders($order, $customer, $date, $product, $quantity);
	//echo '<br/>'.$result;
	
	// $users = CustomerOrders::getUsers('2013-04-25', '2013-05-01', 'completed');
	// foreach($users as $user){
		// echo 'id '.$user->id.' ';
		// echo 'username '.$user->username.' ';
		// echo 'name '.$user->fullname.' ';
		// echo 'vat '.$user->vat.' ';
		// echo 'email '.$user->email.' ';
		// echo 'phone_number '.$user->phone_number.' ';
		// echo 'status '.$user->status.' ';
		// echo '<br/>';
	// }
	//$results = CustomerOrders::findMarketsSells('2013-04-25', '2013-05-01', 1, $orderState='completed');
	// $results = CustomerOrders::findMarketsSells('2013-05-11', '2013-05-18');
	// foreach($results as $index => $array)
	// {
		// echo 'username '.$array['username'].' income '.$array['income'].' outgoing '.$array['outgoing'].' date '.$array['orderDate'].'<br/>';
	// }
	// $results = CustomerOrders::get_orders(null, null,'2013-04-25', '2013-05-01');
	// foreach($results as $result){
		// echo $result->id.' '.$result->seller.' '.$result->customer.'<br/>';
	// }
	// $results = SupplyOrders::get_orders('completed', '2013-04-24', '2013-05-24');
	// foreach($results as $result){
		// echo 'orderID '.$result->id.' suppliers '.$result->supplier.' fullname '.$result->supplier_name.' orderDate '.$result->order_date.
		// ' state '.$result->state.'<br/>';
		
	// }
/*PAraggeleies pelatwn*/	
	 $customer = 2;
	 $seller = 1;
	 $order=216;
	 $order_date = '2013-05-26';
	 $expected_date = '';
	 $products[0] = 1;
	 $products[1] = 2;
	 $products[2] = 5;
	 $quantity[0] = 350;
	 $quantity[1] = 837;
	 $quantity[2] = 5;
	
	//CustomerOrders::create_new_order($seller, $customer, $order_date, $expected_date, $products, $quantity)
	// $order = 199;
	// $products[0] = 5;
	// $products[1] = 16;
	// $products[2] = 23;
	// $quantity[0] = 4;
	// $quantity[1] = 3;
	// $quantity[2] = 3;
	 //CustomerOrders::editOrders($order, $customer, $expected_date, $products, $quantity)

/*Paraggelies promitheutwn*/
	//$order = 214;
	//$supplier = 4;
	//$order_date = '2013-05-24';
	//$expected_date = '2014-01-03';
	//$products[0] = 1;
	//$products[0] = 7;
	//$products[1] = 11;
	//$products[2] = 8;
	
	//$quantity[0] = 15;
	//$quantity[0] = 0;
	//$quantity[1] = 0;
	//$quantity[2] = 20;
	//SupplyOrders::create_new_order($supplier, $order_date, $expected_date, $products, $quantity);
	//SupplyOrders::editOrder($supplier, $order, $expected_date, $products, $quantity)
	//SupplyOrders::deleteOrder(208);
	//$sells = SupplyOrders::findSells('2012-03-03', '2015-04-04', 'incompleted');
	//foreach($sells as $index => $array){
	//	echo 'date '.$array['orderDate'].' cost '.$array['cost'].'<br/>';
	//}
	//$products = array();
	//$products[0] = 8;
	
	//$quantities = array();
	//$quantities[0] = 50;
	//CustomerOrders::create_new_order(1, 1, '2013-05-26', '2013-05-28', $products, $quantities);
	$result = ModelProducts::suggestProduct();
	foreach($result as $product){
		//echo ' id: '.$product->id;
		//echo ' name: '.$product->name;
		//echo ' supplier: '.$product->supplier;
		//echo ' limit: '.$product->limit;
		//echo ' available: '.$product->available_quantity;
		//echo ' wish: '.$product->wish_quantity;
		//echo '<br/>';
	}
	
	$result = ModelProducts::get_activeProducts();
	foreach($result as $product){
		echo ' id: '.$product->id;
		echo ' name: '.$product->name;
		echo '<br/>';
	}
	
?>