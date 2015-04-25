<?php
if(!defined("INDEX_CONTROLLER"))
	die("invalid request!");

require_once('PHPExcel/PHPExcel.php');
require_once('PHPExcel/PHPExcel/Writer/Excel2007.php');

/* Create new PHPExcel object */
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setTitle("Στατιστικά επιχείρησης");

	/* Generate all sheets and set common attributes */
		
		$numberOfSheets = 3;

		for($i = 0; $i < $numberOfSheets-1; $i++)
			$objPHPExcel->createSheet();


		for($i = 0; $i < 3; $i++) {
			$objPHPExcel->setActiveSheetIndex($i);
			$sheet = $objPHPExcel->getActiveSheet();

			/* Set columns to auto-width */
			for($column = 'A'; $column <= 'J'; $column++)
				$sheet->getColumnDimension($column)->setAutoSize(true);

			/* Make the first row bold */
			$sheet->getStyle("1")->getFont()->setBold(true);
		}

	/* First sheet: Προμήθειες */
		
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();

		$sheet->setTitle('Προμήθειες');

		/* Insert the titles of the first row */
		$titles = array("ID", "Προμηθευτής", "Ημερομηνία παραγγελίας", "Αναμενόμενη παραλαβή", "Παραλαβή", "Κατάσταση");
		$sheet->fromArray($titles, NULL, 'A1');

		/* Insert all data */
		$sheet->fromArray($supplierOrders, NULL, 'A2');

	/* Second sheet: Παραγγελίες */

		$objPHPExcel->setActiveSheetIndex(1);
		$sheet = $objPHPExcel->getActiveSheet();

		$sheet->setTitle('Παραγγελίες');

		/* Insert the titles of the first row */
		$titles = array("ID", "Πελάτης", "Πωλητής", "Ημερομηνία παραγγελίας", "Αναμενόμενη παραλαβή", "Παραλαβή", "Κατάσταση");
		$sheet->fromArray($titles, NULL, 'A1');

		/* Insert all data */
		$sheet->fromArray($customerOrders, NULL, 'A2');

	/* Third sheet: Έσοδα/Έξοδα */

		$objPHPExcel->setActiveSheetIndex(2);
		$sheet = $objPHPExcel->getActiveSheet();

		$sheet->setTitle('Έσοδα-Έξοδα');

		/* Insert the titles of the first row */
		$titles = array("Ημερομηνία", "Έσοδα από πωλήσεις", "Έξοδα προμηθειών");
		$sheet->fromArray($titles, NULL, 'A1');

		/* Insert all data */
		$sheet->fromArray($profitStats, NULL, 'A2');


/* Save file */
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

$dir = dirname(__FILE__);
$objWriter->save($dir . "/rwxrwxrwx/file.xlsx" );

?>

<p>Το αρχείο Excel για το διάστημα <?= $start ?> - <?= $end ?> δημιουργήθηκε. Πατήστε <a href="view/rwxrwxrwx/file.xlsx">εδώ</a> για να το κατεβάσετε.</p>

