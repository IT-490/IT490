<?php
include("functions.php");

$name = sanatize($_REQUEST['username']);
$pass = sanatize($_REQUEST['password']);
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
	$response = sendRabbit(array('type' => 'login', 'data' => array('username' => $name, 'password' => sha1($pass))));
	$obj->response = $response;
	if($response == 0){
		session_start();
		$_SESSION['user'] = $name;
		header('Location: ./index.html');
	}
}

$encObj = json_encode($obj);
echo $encObj;
?>
