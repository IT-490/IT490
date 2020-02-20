#!/usr/bin/php
<?php
include ('account.php');
$db = mysqli_connect($hostname,$username,$password,$project);
if(mysqli_connect_error()){
	Print "Failed to connect to MYSQL:" .mysqli_conect_error();
	exit();
}
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
mysqli_select_db($db, $project);
$recieved = recieveRabbit();
function process($recieved){
	var_dump($recieved);
if($recieved['type'] == 'login'){
	$sql = "Select username, password FROM users WHERE username = $recieved['username']";
	$result = mysqli_query($db,$sql);
	if(mysqli_num_rows($result) == 0){
		return 1;
	}
	else{
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		if($pass == $row[$recieved['password']]){
			return 0;
		}
		else{
			return 2;
	}
	}
	}
}else if($recieved['type'] == 'register'){
	$s="select * from it490 where user = $recieved['username']";
	$result = mysqli_query($db,$s);
	if(mysqli_num_rows($result) != 0){
		return 1;
	}
	else{
		
		$sql="insert into users 'username', 'password', 'firstname', 'lastname') values ($recieved['username'], $recieved['password'], $recieved['firstname'], $recieved['lastname'])";
		mysqli_query($db,$sql);
		return 0;
	}
}else if($recieved['type'] == 'sanatize'){
	return mysqli_real_escape_string($db, $recieved['data']);
	
}else if($recieved['type'] == 'log'){

}else if($recieved['type'] == 'update'){

}
}

$server - new rabbitMQServer("testRabbitMQ", "testServer");
$server->process_request('process');
?>
