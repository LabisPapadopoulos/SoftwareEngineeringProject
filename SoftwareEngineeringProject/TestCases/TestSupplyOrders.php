<?php
require_once('simpletest/autorun.php');
require_once('../config/configs.php');

require_once('../model/db.php');
require_once('../model/Exceptions.php');
require_once('../model/Products.php');
require_once('../model/Suppliers.php');
require_once('../model/SupplyOrders.php');

global $order_id, $supplier, $products, $quantity;


class TestSupplyOrders extends UnitTestCase {
	
	
	
	function TestNewSupplyOrder(){
		global $order_id, $supplier, $products, $quantity;
		
		$this->assertNotNull($supplier = ModelSuppliers::get_supplier_by_id(1));
		
		$order_date = date('Y-m-d');
		$expected_date = "2014-01-01";
		
		$products = array();
		$quantity = array();
		
		$this->assertNotNull($product_list = ModelProducts::get_ProductsBySupplierId($supplier->id));
		
		$this->assertTrue(count($product_list) >= 3, "We need at least 3 active products!");
		
		$products = array_reverse(array(array_pop($product_list)->id, array_pop($product_list)->id, array_pop($product_list)->id));
		$quantity = array(5, 10, 15);
		
		
		//save the order id to static var
		$order_id = SupplyOrders::create_new_order($supplier->id, $order_date, $expected_date, $products, $quantity);
	}
	
	function TestOrderDetails(){
		global $order_id, $supplier, $products, $quantity;
		
		
		$this->assertNotNull($order = SupplyOrders::viewOrderDetails($order_id));
		
		$this->assertEqual($order->id, $order_id, "ERROR IN ORDER ID");
		$this->assertTrue(in_array($products[0], $order->product), "ERROR IN PRODUCT ID 0");
		$this->assertEqual($order->quantity[$products[0]], 5, "ERROR IN QUANTITY 0");
		$this->assertTrue(in_array($products[1], $order->product), "ERROR IN PRODUCT ID 1");
		$this->assertEqual($order->quantity[$products[1]], 10, "ERROR IN QUANTITY 1");
		$this->assertTrue(in_array($products[2], $order->product), "ERROR IN PRODUCT ID 2");
		$this->assertEqual($order->quantity[$products[2]], 15, "ERROR IN QUANTITY 2");
		
		$order = SupplyOrders::viewOrder($order_id);
	
		$this->assertEqual($order->expected_date, "2014-01-01", "ERROR IN DATE");
	}
	
	function TestSupplyOrderEdit(){
		global $order_id, $supplier, $products, $quantity;
		
		array_pop($products);
		array_pop($quantity);
		
		$quantity = array(50, 50);
		
		SupplyOrders::editOrder($supplier->id, $order_id, "2015-05-05", $products, $quantity);
	}
	
	function TestSupplyOrderUpdate(){
		return;
		global $order_id, $supplier, $products, $quantity;
		
		$this->assertNotNull($order = SupplyOrders::viewOrderDetails($order_id));
		
		$this->assertEqual($order->id, $order_id, "ERROR IN ORDER ID");
		$this->assertTrue(in_array($products[0], $order->product), "ERROR IN PRODUCT ID 0");
		$this->assertEqual($order->quantity[$products[0]], 50, "ERROR IN QUANTITY 0");
		$this->assertTrue(in_array($products[1], $order->product), "ERROR IN PRODUCT ID 1");
		$this->assertEqual($order->quantity[$products[1]], 50, "ERROR IN QUANTITY 1");
		
		$order = SupplyOrders::viewOrder($order_id);
		$this->assertEqual($order->expected_date, "2015-05-05", "ERROR IN DATE");
		
	}
	
};

?>