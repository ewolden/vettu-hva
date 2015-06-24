<?php
require_once(__DIR__.'/../database/dbUser.php');

$db = new dbUser();
$rows = $db->getSelected('user', 'userId', '1','1');
$users = '';
foreach($rows as $row){
	$users .= $row['userId'].',';
}
$users = rtrim($users, ',');
echo $users;
?>