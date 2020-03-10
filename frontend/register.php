<?php
include("functions.php");
$username =$_REQUEST['name'];
$password =$_REQUEST['pass'];
$firstname =$_REQUEST['fname'];
$lastname =$_REQUEST['lname'];
$email =$_REQUEST['email'];
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
	$obj->dataerror = false;
	$response = sendRabbit(array('type' => 'register', 'data' => array('username' => $username, 'password' => sha1($password), 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email)));
	if($response == 0){
		session_start();
		$_SESSION['user'] = $username;
		$obj->sqlerror = false;
	}else if ($response == 1){
		$obj->sqlerror = true;
	}
}
echo json_encode($obj);
exit();
?>
