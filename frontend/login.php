<?php
include("functions.php");

$name = sanatize($_REQUEST['name']);
$pass = sanatize($_REQUEST['pass']);
$errFlag = False;

if(empty($name)){
	$errFlag = True;
	$nameErr = True;
}
if(empty($pass)){
	$errFlag = True;
	$passErr = True;
}

if($errFlag){
	$obj->nameErr = $nameErr;
	$obj->passErr = $passErr;
	$encObj = json_encode($obj);
	echo $encObj;
}else{
	$response = sendRabbit(array('type' => 'login', 'data' => array('name' => $name, 'pass' => sha1($pass))));
	echo $response;
}


?>
