<?php
require_once (__DIR__.'/../database/dbHelper.php');
require_once (__DIR__.'/../PHPExcel_1.8.0_doc/Classes/PHPExcel.php');

ini_set('max_execution_time', 1500);
ini_set('memory_limit', '2048M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

register_shutdown_function('shutdownFunction');

function shutDownFunction() { 
    $error = error_get_last();
    if ($error['type'] == 1) {
        echo "fatal";    
    } 
}


createSheets();	

function createSheets(){
	$userId = $_POST['userId'];
	$db = new dbHelper();
	$objPHPExcel = new PHPExcel();
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	$cacheSettings = array( ' memoryCacheSize ' => '256MB');
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	$userTables = array('user', 'user_usage','category_preference','preference_value', 'user_tag','user_storytag','stored_story','story_state');
	$nonUserTables = array('story', 'state','story_media','media_format','story_subcategory','category_mapping','subcategory', 'category');
	$sheetIndex = 0;
	if($userId == 'all'){
		$whereClause = '1';
		$whereValue = '1';
	}
	else {
		$whereClause = 'userId';
		$whereValue = $userId;
	}
	foreach ($userTables as $userTable){
		$newSheet = $objPHPExcel->createSheet($sheetIndex);
		$objPHPExcel->setActiveSheetIndex($sheetIndex);
		$data = $db->getSelected($userTable, '*', $whereClause,$whereValue);
		
		/*Get the columns in the table. Stored in dbConstants.php*/
		$columns = array_slice($db->getTableColumns($userTable),2);
		
		createSheetFromTable($data, $columns, $sheetIndex, $objPHPExcel);

		$newSheet->setTitle($userTable);
		$newSheet->freezePane('A2');
		$sheetIndex++;
	}
	foreach ($nonUserTables as $table){
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
	print_r('Before save...');
	$objWriter->save($fileName);
	print_r('After save...');
	chmod($fileName,0776);
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