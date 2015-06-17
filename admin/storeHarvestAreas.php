<?php
$counties = array(
	'sør-trøndelag' => "s%C3%B8r-tr%C3%B8ndelag" ,
	'nord-trøndelag' => "nord-tr%C3%B8ndelag",
	'nordland' => "nordland",
	'troms' => "troms",
	'finnmark' => "finnmark",
	'møre og romsdal' => "m%C3%B8re+og+romsdal", 
	'sogn og fjordane' => "sogn+og+fjordane",
	'hordaland' => "hordaland",
	'rogaland' => "rogaland",
	'vest-agder' => "vest-agder",
	'aust-agder' => "aust-agder",
	'telemark' => "telemark",
	'buskerud' => "buskerud",
	'oppland' => "oppland",
	'hedmark' => "hedmark",
	'oslo' => "oslo",
	'akershus' => "akershus",
	'østfold' => "%C3%B8stfold",
	'vestfold' => "vestfold",
	'nolimit' => "");

$splitValue = "%20OR%20";	
$insertString = "";
$harvestAreaFile = fopen(__DIR__.'/../database/harvestArea.txt', 'w') or die ("Unable to open file");
$areas = $_POST['areas'];
$areaAray = explode("OR",$areas);
foreach($areaAray as $area){
	$area = trim($area);
	$insertString .= $counties[$area] .''.$splitValue;
}
$insertString = substr($insertString, 0, -strlen($splitValue));
$areas = fwrite($harvestAreaFile, $insertString);
fclose($harvestAreaFile);
?>