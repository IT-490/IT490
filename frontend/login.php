<?php
include("functions.php");

$name = sanatize($_REQUEST['name']);
$pass = sanatize($_REQUEST['pass']);
$errFlag = False;
$nameErr = False;
$passErr = False;

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
}else{
	$response = sendRabbit(array('type' => 'login', 'data' => array('name' => $name, 'pass' => sha1($pass))));
	$obj->response = $response;
}

$encObj = json_encode($obj);
echo $encObj;
?>
