<?php
if(!defined("INDEX_CONTROLLER"))
	die("invalid request!");

try_access(array("admin"));

$title = "Στατιστικά";

require_once('view/header.php');

$action = checkArr($_GET, "action");

list($start, $end) = getStartEndDates();

switch($action){
	case 'profit':

				$rawSupplyCosts = SupplyOrders::findSells($start, $end);

				$supplyCosts = array();
				foreach($rawSupplyCosts as $row)
					$supplyCosts[$row["orderDate"] ] = $row["cost"];

				$rawIncome = CustomerOrders::findMarketsSells($start, $end);

				$incomeStats = array();
				foreach($rawIncome as $row) 
					$incomeStats[$row["orderDate"]] = $row["income"];

				require_once('view/profit.php');
				break;
	case 'sales':
								
				$sellerID = checkArr($_GET, "sellerID");

				$stats = CustomerOrders::getNumberOfOrders($start, $end, $sellerID);
				if( !checkVar($stats) && checkVar($sellerID) ) {
					$sellerID = null;
					unset($_GET["sellerID"]);
					$stats = CustomerOrders::getNumberOfOrders($start, $end, null);
					$warningMsg = "Ο πωλητής που επιλέξατε δεν καταχώρησε παραγγελίες στο συγκεκριμένο χρονικό διάστημα. Αντί για αυτό, εμφανίζονται στατιστικά για όλους τους πωλητές";
				}

				$rawIncomeStats = CustomerOrders::findMarketsSells($start, $end, $sellerID);

				$incomeStats = array();

				/* Calculate a total for each day */
				foreach($rawIncomeStats as $row)
					$incomeStats[$row["orderDate"]] = $row["income"];

				$sellers = CustomerOrders::getUsers($start, $end);

				$seller_stat = array();
				foreach($sellers as $seller){
					if( !(checkVar($sellerID) && $seller->id != $sellerID) )
					$seller_stat[$seller->id] = array('seller' => $seller, 'orders' => array_sum(CustomerOrders::getNumberOfOrders($start, $end, $seller->id))  );
				}

				require_once('view/showMessage.php');
				require_once('view/sales.php');
				break;
	case 'excel':

				require_once('view/excel.php');
				break;

	case 'download':

				/* Fetch supplier orders */
				$rawSupplierOrders = SupplyOrders::get_orders(null, $start, $end);
				
				$supplierOrders = array();
				foreach($rawSupplierOrders as $row) {

					if($row->state == "incompleted")
						$row->state = "Σε αναμονή";
					else if($row->state == "completed")
						$row->state = "Ολοκληρωμένη";
					else if($row->state == "cancelled")
						$row->state = "Ακυρωμένη";

					$supplierOrders[$row->id] = array( $row->id, $row->supplier_name, $row->order_date, $row->expected_date, $row->receipt_date, $row->state );
				}

				/* Fetch customer orders */
				$rawCustomerOrders = CustomerOrders::get_orders(null, null, $start, $end);

				$customerOrders = array();
				foreach($rawCustomerOrders as $row) {

					if($row->status == "incompleted")
						$row->status = "Σε αναμονή";
					else if($row->status == "completed")
						$row->status = "Ολοκληρωμένη";
					else if($row->status == "cancelled")
						$row->status = "Ακυρωμένη";

					$customerOrders[$row->id] = array( $row->id, $row->customer_name, $row->seller_name, $row->order_date, $row->expected_date, $row->receipt_date, $row->status );
				}

				/* Fetch income/expenditure stats */
				$rawSupplyCosts = SupplyOrders::findSells($start, $end);

				$supplyCosts = array();
				foreach($rawSupplyCosts as $row)
					$supplyCosts[$row["orderDate"] ] = $row["cost"];

				$rawIncome = CustomerOrders::findMarketsSells($start, $end);

				$incomeStats = array();
				foreach($rawIncome as $row) 
					$incomeStats[$row["orderDate"]] = $row["income"];

				$profitStats = array();

				/* Create an interval between the dates */
				$beginDate = new DateTime($start);
				$endDate = new DateTime($end);

				/* Increment by one day so the endDate is included.. */
				$endDate->modify("+1 day");

				$interval = DateInterval::createFromDateString('1 day');
				$period = new DatePeriod($beginDate, $interval, $endDate);

				/* For each day betewen start and end... */
				foreach ( $period as $dt ) {
					$day = $dt->format("Y-m-d");

					$profitStats[$day] = array( $day, checkArr($incomeStats, $day), checkArr($supplyCosts, $day) );
				}

				require_once('view/generateExcelSpreadsheet.php');

				break;
	default:
				require_once('view/404.php');
				break;
}

require_once('view/footer.php');
?>