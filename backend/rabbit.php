#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function getShow($db, $showID){
	$sql = "SELECT name FROM shows WHERE showID = {$showID}";
	if(($result = mysqli_query($db, $sql)) == false){
		//error happened here
	}else{
		if(mysqli_num_rows($result) == 0){
			return false;
		}else{
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				return $row['name'];
			}
		}
	}
}
function process($input){
	include ('account.php');
	$db = mysqli_connect($hostname,$username,$password,$project);
	if(mysqli_connect_error()){
		Print "Failed to connect to MYSQL:" .mysqli_conect_error();
		$result = mysqli_connect_error();
		error($result);
		exit();
	}
	mysqli_select_db($db, $project);
	var_dump($input);
	switch($input['type']){
		case "login":
			$sql = "Select * FROM users WHERE username = '{$input['data']['username']}'";
			$result = mysqli_query($db,$sql);
			if($result != $sql){
				error($result);
				return null;
				break;
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
			$s="select * from users where username = '{$input['data']['username']}';";
			$result = mysqli_query($db,$s);
			if($result !=$s){
				error($result);
				return null;
				break;
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
			return $input["data"];
		case "update":
			//some code
		case "getThreads":
			$sql = "SELECT * FROM forum_topics WHERE topicShow = '{$input['data']}' ORDER BY postCount DESC, lastPost ASC";
			$data = array();
			if(!($result = mysqli_query($db,$sql))){
				return 1;
			}else{
				if(mysqli_num_rows($result) == 0){
					return 0;
				}else{
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						$data[] = $row;
					}
					return $data;
				}

			}
		case "newThread":
			$sql = "INSERT INTO forum_topics (subject, topicShow, topicCreator, lastPoster) values ('{$input['data']['subject']}', {$input['data']['show']}, '{$input['data']['user']}', '{$input['data']['user']}')";
			if(mysqli_query($db,$sql)){
				$id = mysqli_insert_id($db);
				$response = process(array('type'=>'newPost', 'data'=> array('content'=> $input['data']['content'], 'user'=> $input['data']['user'], 'id'=> $id)));
				if($response == 0){
					return $id;
				}else{
					return 1;
				}
			}else{
				//error happened here
				return 1;
			}
		case "getPosts":
			$sql = "SELECT content, postDate, poster, postTopic FROM forum_posts WHERE postTopic = {$input['data']}";
			$data = array();
			if(!($result = mysqli_query($db,$sql))){
				return 1;
			}else{
				if(mysqli_num_rows($result) == 0){
					return 0;
				}else{
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						$data[] = $row;
					}
					return $data;
				}

			}
		case "newPost":
			$sql = "INSERT INTO forum_posts (content, poster, postTopic) values ('{$input['data']['content']}', '{$input['data']['user']}', {$input['data']['id']});";
			$sql .= "UPDATE forum_topics SET postCount = postCount + 1, lastPoster = '{$input['data']['user']}' WHERE topicID = {$input['data']['id']};";
			echo $sql;
			//need to fix up this section of code
			mysqli_multi_query($db, $sql);
			//if(mysqli_multi_query($db, $sql) && mysqli_next_result()){
				return 0;
			//}else{
				//error logging happens here
			//	return 1;
			//}
	}
}
function error ($result){
	include('../frontend/functions.php/');
	sendError($result);
}

$server = new rabbitMQServer("rabbitMQ.ini", "database");
echo "server started up";
$server->process_requests('process');
?>
