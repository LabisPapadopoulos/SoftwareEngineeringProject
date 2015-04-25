<?php 

error_reporting(E_ALL);

require_once('PHPExcel/PHPExcel.php');
require_once('PHPExcel/PHPExcel/Writer/Excel2007.php');


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setTitle("Στατιστικά επιχείρησης");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'wheeee');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

$dir = dirname(__FILE__);
// echo $dir;

$objWriter->save($dir . "/PHPExcel/rwxrwxrwx/test.xlsx" );


?>

wheee
