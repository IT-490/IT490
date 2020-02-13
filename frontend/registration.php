<?php
include ("functions.php");

$username = sanatize($_REQUEST['name']);
$password = sanatize($_REQUEST['pass']);
$firstname = sanatize($_REQUEST['fname']);
$lastname = sanatize($_REQUEST['lname']);
$email = sanatize($_REQUEST['email']);



$errflag = false 
if($username == empty){
	$errflag = true;
}
if($password == empty){
	$errflag = true;
}
if($firstname == empty){
	$errflag = true;
}
if($lastname == empty){
	$errflag = true;
}
if($email == empty){
	$errflag = true;
}
if($errflag == true){
	$obj->dataerror = true;
}
else{
	$response = ;
	if ($response = 1){
		$obj->sqlerror = true;
	}
}

?>
