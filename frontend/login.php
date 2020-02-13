<?php
include("functions.php");

$name = sanatize($_REQUEST['name']);
$pass = sanatize($_REQUEST['pass']);
$errFlag = 0;

if(empty($name)){
	$errFlag = 1;
	$nameError = 1;
}
if(empty($pass)){
	$errFlag = 1;
	$passError = 1;
}

if($errFlag == 1){
	
}else{
	$response = sendRabbit(array('type' => 'login', 'data' => array('name' => $name, 'pass' => $pass)));
	echo $response;
}


?>
