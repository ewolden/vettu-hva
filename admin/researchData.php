<?php
require_once (__DIR__.'/../database/dbHelper.php');
require_once (__DIR__.'/../PHPExcel_1.8.0_doc/Classes/PHPExcel.php');

ini_set('max_execution_time', 1500);
createSheets();
	
function createSheets(){
	$db = new dbHelper();
	$objPHPExcel = new PHPExcel();
	$tables = array('user','story', 'user_usage','category_preference','preference_value', 'user_tag','user_storytag','stored_story','state','story_state','story_media','media_format','story_subcategory','category_mapping','subcategory', 'category');
	$sheetIndex = 0;
	foreach ($tables as $table){
		$newSheet = $objPHPExcel->createSheet($sheetIndex);
		$objPHPExcel->setActiveSheetIndex($sheetIndex);
		$data = $db->getSelected($table, '*', '1','1');
		
		/*Get the columns in the table. Stored in dbConstants.php*/
		$columns = array_slice($db->getTableColumns($table),2);
		
		createSheetFromTable($data, $columns, $sheetIndex, $objPHPExcel);

		$newSheet->setTitle($table);
		$newSheet->freezePane('A2');
		$sheetIndex++;
	}
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$fileName = 'researchData.xlsx';
	$objWriter->save($fileName);
}


function createSheetFromTable($data, $columns, $sheetIndex, $objPHPExcel){
	/*Used to create cell-identifiers in Excel*/
	$alphabet = array('A','B','C','D','E','F','G','H','I','J','K');
	
	/*Sets the names of the columns in the first row*/
	for ($x=0; $x<count($columns);$x++){
		$objPHPExcel->setActiveSheetIndex($sheetIndex)
			->setCellValue($alphabet[$x].'1',$columns[$x]);
		/*Autoadjust the columnsize*/
		$objPHPExcel->getActiveSheet()->getColumnDimension($alphabet[$x])
		->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getStyle($alphabet[$x].'1')->getFont()->setBold(true);
	}
	
		
	/*Populate the cells with data from the database*/
	$rowCount = 2;
	if (isset($data)){
		foreach ($data as $row){
			for ($x=0; $x<count($columns);$x++){
				$objPHPExcel->setActiveSheetIndex($sheetIndex)
					->setCellValue($alphabet[$x].$rowCount,$row[$x]);
			}
			$rowCount++;
		}
	}
}
?>