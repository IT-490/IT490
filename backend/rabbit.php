#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($input){
	include ('account.php');
	$db = mysqli_connect($hostname,$username,$password,$project);
	if(mysqli_connect_error()){
		Print "Failed to connect to MYSQL:" .mysqli_conect_error();
		exit();
	}
	mysqli_select_db($db, $project);
	var_dump($input);
	switch($input['type']){
		case "login":
			$sql = "Select * FROM users WHERE username = '{$input['data']['username']}'";
			$result = mysqli_query($db,$sql);
			if(mysqli_num_rows($result) == 0){
				return 1;
			}else{
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					if($input['data']['password'] == $row["password"]){
						return 0;
					}else{
						return 2;
					}
				}
			}
		case "register":	
			$s="select * from users where username = '{$input['data']['username']}'";
			$result = mysqli_query($db,$s);
			if(mysqli_num_rows($result) != 0){
				return 1;
			}else{
				$sql="insert into users (username, password, firstname, lastname) values ('{$input['data']['username']}','{$input['data']['password']}','{$input['data']['firstname']}', '{$input['data']['lastname']}')";
				mysqli_query($db,$sql);
					return 0;
			}
		case "sanatize":
			return mysqli_real_escape_string($db, $input['data']);
		case "log":
			//some code
		case "update":
			//some code
	}
}

$server = new rabbitMQServer("rabbitMQ.ini", "database");
echo "server started up";
$server->process_requests('process');
?>
