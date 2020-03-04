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

function isFriend($db, $user, $friend){
	$sql = "SELECT * FROM friends WHERE (user = '$user' or friend = '$user') and (user = '$friend' or friend = '$friend') and accepted = true";
	echo $sql;
	if(!($result = mysqli_query($db, $sql))){
		return 3;
	}else{
		if(mysqli_num_rows($result) == 0){
			return 0;
		}else{
			return 1;
		}
	}
}

function isRequest($db, $user, $friend){
	$sql = "SELECT * FROM friends WHERE (user = '$user' or friend = '$user') and (user = '$friend' or friend = '$friend') and accepted = false";
	echo $sql;
	if(!($result = mysqli_query($db, $sql))){
		return 3;
	}else{
		if(mysqli_num_rows($result) == 0){
			return 0;
		}else{
			return 1;
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
			if($result == false){
				error($result);
				return 3;
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
			if($result == false){
				error($result);
				return 3;
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
			foreach($input['data'] as $show){
				//TODO add premiered date into check to prevent multiple shows being pulled with the same name?
				//check to see if showw exists
				$sql = "SELECT * FROM shows WHERE name = '{$show['show']}'";
				if(!($result= mysqli_query($db, $sql))){
					echo $sql.PHP_EOL;
					return 1;
				}
				if(mysqli_num_rows($result) == 0){
					//if it doesnt create it and set showID to $id
					$sql = "INSERT INTO shows (name, network) values ('{$show['show']}', '{$show['network']}')";
					if(!(mysqli_query($db, $sql))){
						return 1;
					}
					$id = mysqli_insert_id($db);
				}else{
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						//if it does set showID to $id
						$id = $row['showID'];
					}
				}
				//check to see if episode is already in table
				$sql = "SELECT * FROM episodes WHERE showID = $id and airdate = '{$show['airdate']}'";
				if(!($result = mysqli_query($db, $sql))){
					echo $sql.PHP_EOL;
					return 1;
				}
				if(mysqli_num_rows($result) == 0){
					//if episode not already in table add it
					$sql = "INSERT INTO episodes (name, showID, airdate) values ('{$show['name']}', $id, '{$show['airdate']}')";
					if(!(mysqli_query($db, $sql))){
						echo $sql.PHP_EOL;
						return 1;
					}
				}
				
			}
			return 0;
		case "getThreads":
			$sql = "SELECT * FROM forum_topics WHERE topicShow = '{$input['data']}' ORDER BY postCount DESC, lastPost ASC";
			$data = array();
			$data['rows'] = array();
			if(!($result = mysqli_query($db,$sql))){
				return 1;
			}else{
				if(!($data['show'] = getShow($db, $input['data']))){
					return 2;
				}else{
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						$data['rows'][] = $row;
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
		case "getProfile":
			//TODO clean up this code using multi_query to cut down on the ammount of code here
			$data = array();
			$data['shows'] = array();
			$data['friends'] = array();
			$sql = "SELECT * FROM users WHERE username = '{$input['data']['user']}'";
			if(!($result = mysqli_query($db, $sql))){
				return 1;
			}else{
				if(mysqli_num_rows($result) == 0){
					return 2;
				}
			}
			if(isset($input['data']['requestor'])){
				if(($response = isFriend($db, $input['data']['user'], $input['data']['requestor'])) == 3){
					return 1;
				}else{
					if($response == 1){
						$data['isFriend'] = 1;
					}else{
						$data['isFriend'] = 0;
					}
				}
			}
			$sql = "SELECT * FROM following WHERE user = '{$input['data']['user']}'";
			if(!($result = mysqli_query($db, $sql))){
				return 1;
			}else{
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$data['shows'][] = getShow($db, $row['showID']);
				}
				
				$sql = "SELECT * FROM friends WHERE (user = '{$input['data']['user']}' or friend = '{$input['data']['user']}') and accepted = true";
				if(!($result = mysqli_query($db, $sql))){
					return 1;
				}else{
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						if($row['friend'] == $input['data']['user']){
							$data['friends'][] = $row['user'];
						}else if($row['user'] == $input['data']['user']){
							$data['friends'][] = $row['friend'];
						}
					}
					return $data;
				}
			}
		case "addFriend":
			//TODO if a request exists from that user to the requesting user accept the request
			if(($response = isFriend($db, $input['data']['user'], $input['data']['requestor'])) == 3){
				return 1;
			}
			if($response == 1){
				return 2;
			}
			if(($response = isRequest($db, $input['data']['user'], $input['data']['requestor'])) == 3){
				return 1;
			}
			if($response == 1){
				return 2;
			}
			$sql = "INSERT INTO friends (user, friend, accepted) values ('{$input['data']['requestor']}', '{$input['data']['user']}', false)";
			echo $sql;
			if(!mysqli_query($db, $sql)){
				return 1;
			}else{
				return 0;
			}
		case "removeFriend":
			if(($response = isFriend($db, $input['data']['user'], $input['data']['requestor'])) == 3 ){
				return 1;
			}
			if(!$response == 1){
				return 2;
			}
			$sql = "DELETE FROM friends WHERE (user = '{$input['data']['user']}' or friend = '{$input['data']['user']}') and (user = '{$input['data']['requestor']}' or friend = '{$input['data']['requestor']}')";
			if(!mysqli_query($db, $sql)){
				return 1;
			}else{
				return 0;
			}
		case "getFriends":
			$data = array();
			$data['friends'] = array();
			$data['requests'] = array();
			$sql = "SELECT * FROM friends WHERE (user = '{$input['data']}' or friend = '{$input['data']}') and accepted = true";
			echo $sql;
			if(!($result = mysqli_query($db, $sql))){
				return 1;
			}else{
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					if($row['user'] == $input['data']){
						$data['friends'][] = $row['friend'];
					}else if($row['friend'] == $input['data']){
						$data['friends'][] = $row['user'];
					}
				}
			}
			$sql = "SELECT * FROM friends WHERE  friend = '{$input['data']}' and accepted = false";
			echo $sql;
			if(!($result = mysqli_query($db, $sql))){
				return 1;
			}else{
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$data['requests'][] = $row['user'];
				}
			}
			return $data;
		case "requestResponse":
			if(($response = isRequest($db, $input['data']['user'], $input['data']['requestor'])) == 3){
				return 1;
			}
			if($response == 0){
				return 2;
			}
			if($input['data']['action'] == 'accept'){
				$sql = "UPDATE friends SET accepted = true WHERE friend = '{$input['data']['user']}' and user = '{$input['data']['requestor']}'"; 
				if(mysqli_query($db, $sql)){
					return 0;
				}else{
					return 1;
				}
			}else if($input['data']['action'] == 'deny'){
				$sql = "DELETE FROM friends WHERE friend = '{$input['data']['user']}' and user = '{$input['data']['requestor']}'";
				if(mysqli_query($db, $sql)){
					return 0;
				}else{
					return 1;
				}
			}
	}
}
function error ($result){
	include('../frontend/functions.php');
	sendError($result);
}

$server = new rabbitMQServer("rabbitMQ.ini", "database");
echo "server started up";
$server->process_requests('process');
?>
