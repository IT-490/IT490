<?php
echo('test');
include("functions.php");
$username = sanatize($_REQUEST['name']);
$password = sanatize($_REQUEST['pass']);
$firstname = sanatize($_REQUEST['fname']);
$lastname = sanatize($_REQUEST['lname']);
$email = sanatize($_REQUEST['email']);

$errflag = False; 
if(empty($username)){
	$errflag = True;
}
if(empty($password)){
	$errflag = True;
}
if(empty($firstname)){
	$errflag = true;
}
if(empty($lastname)){
	$errflag = true;
}
if(empty($email)){
	$errflag = true;
}
if($errflag == true){
	$obj->dataerror = true;
}else{
	$response = sendRabbit(array('type' => 'register', 'data' => array('username' => $username, 'password' => sha1($password), 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email)));
	if($response == 0){
		session_start();
		$_SESSION['user'] = $username;
		$obj->sqlerror = false;
	}else if ($response == 1){
		$obj->sqlerror = true;
	}
}
$obj = json_encode($obj);
echo $obj;
?>
