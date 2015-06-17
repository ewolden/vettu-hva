<?php
if (filesize(__DIR__.'/../database/harvestArea.txt') == 0){
	$areas = "nolimit";
}
else{
	$harvestAreaFile = fopen(__DIR__.'/../database/harvestArea.txt', 'r') or die ("Unable to open file");
	$areas = fread($harvestAreaFile, filesize(__DIR__.'/../database/harvestArea.txt'));
	fclose($harvestAreaFile);
}
echo $areas;
?>