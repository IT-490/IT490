<?php
include("functions.php");

$db = mysqli_connect($hostname, $username, $password, $project);
if(mysqli_connect_error()){
	print('Failled to connect to database.' + mysqli_connect_error);
	exit();
}
$name = sanatize($db, $_REQUEST['name']);
$pass = sanatize($db, $_REQUEST['pass']);
$errFlag = 0;

if(empty($name)){
	$errFlag = 1;
	$nameError = 1;
}
if(empty($pass)){
	$errFlag = 1;
	$nameError = 1;
}

if($errFlag == 1){
	//return a json object with error information
}else{
	sendRabbit("array of data here", "database")
}


?>
