<?php
//Include databse connection info 

require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
function proccess($recieved){
echo "received request".PHP_EOL;
var_dump($request);	
if($recieved['type'] == 'login'){
	$sql = "Select username, password FROM users WHERE username = '$user'";
	$result = mysqli_query($sql);
	if(mysqli_num_rows($result) == 0){
		return 1;
	}
	else{
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			if($pass == $row['password']){
				return 0;
			}
			else{
				return 2;
			}	
		}
	}
}else if($recieved['type'] == 'register'){
	$s="select * from it490 where user = '$user' and pass = '$pass'";
	$t="select top 1 id from users order by id desc";
	$id = $t +1;
	if($s != empty){
		return 1;
	}
	else{
		insert into users (id, username, password, firstname, lastname) values ($id, $user, $pass, $fname, $lname);
	}
}else if($recieved['type'] == 'sanatize'){
	return mysqli_real_escape_string($db, $recieved['data']);
}else if($recieved['type'] == 'log'){
	//code to recieved logs from other systems
}else if($recieved['type'] == 'update'){
	//code to update database from data api
}
}
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
$server->process_request('proccess');
?>
