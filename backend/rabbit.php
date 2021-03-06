#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('../frontend/functions.php');
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

function sanatize($db, $data){
	if(gettype($data) != 'array'){
		$data = mysqli_real_escape_string($db, $data);
	}else{
		$keys = array_keys($data);
		foreach($keys as $key){
			if(gettype($data[$key]) != 'array'){
				$data[$key] = mysqli_real_escape_string($db, $data[$key]);
			}else{
				$data[$key] = sanatize($db, $data[$key]);
			}
		}
	}
	return $data;
}

function isFriend($db, $user, $friend){
	$sql = "SELECT * FROM friends WHERE (user = '$user' or friend = '$user') and (user = '$friend' or friend = '$friend') and accepted = true";
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
		error("Failed to connect to MYSQL:" .mysqli_conect_error());
		exit();
	}
	mysqli_select_db($db, $project);
	var_dump($input);
	$input['data'] = sanatize($db, $input['data']);
	switch($input['type']){
		case "login":
			$sql = "Select * FROM users WHERE username = '{$input['data']['username']}'";
			$result = mysqli_query($db,$sql);
			if($result == false){
				error("ERROR: ".$sql." failed to execute");
				return 3;
			}
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
				error("ERROR: ".$sql." failed to execute");
				return 3;
			}	
			if(mysqli_num_rows($result) != 0){
				return 1;
			}else{
				$sql="insert into users (username, password, firstname, lastname, email) values ('{$input['data']['username']}','{$input['data']['password']}','{$input['data']['firstname']}', '{$input['data']['lastname']}', '{$input['data']['email']}')";
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
					error("ERROR: ".$sql." failed to execute");
					return 1;
				}
				if(mysqli_num_rows($result) == 0){
					//if it doesnt create it and set showID to $id
					$sql = "INSERT INTO shows (name, network, poster) values ('{$show['show']}', '{$show['network']}', '{$show['poster']}')";
					if(!(mysqli_query($db, $sql))){
						error("ERROR: ".$sql." failed to execute");
						return 1;
					}else{
						$id = mysqli_insert_id($db);
					}
				}else{
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						//if it does set showID to $id
						$id = $row['showID'];
					}
				}
				//check to see if episode is already in table
				$sql = "SELECT * FROM episodes WHERE showID = $id and airdate = '{$show['airdate']}'";
				if(!($result = mysqli_query($db, $sql))){
					error("ERROR: ".$sql." failed to execute");
					echo $sql.PHP_EOL;
					return 1;
				}
				if(mysqli_num_rows($result) == 0){
					//if episode not already in table add it
					$sql = "INSERT INTO episodes (name, showID, airdate) values ('{$show['name']}', $id, '{$show['airdate']}')";
					if(!(mysqli_query($db, $sql))){
						error("ERROR: ".$sql." failed to execute");
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
				error("ERROR: ".$sql." failed to execute");
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
					error("ERROR: ".$sql." failed to execute");
					return false;
				}
			}else{
				//error happened here
				error("ERROR: ".$sql." failed to execute");
				return false;
			}
		case "getPosts":
			$sql = "SELECT content, postDate, poster, postTopic FROM forum_posts WHERE postTopic = {$input['data']}";
			$data = array();
			if(!($result = mysqli_query($db,$sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}else{
				if(mysqli_num_rows($result) == 0){
					return 0;
				}else{
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						$row['content'] = stripcslashes($row['content']);
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
				error("ERROR: ".$sql." failed to execute");
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
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}else{
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$data['shows'][] = array('name'=>getShow($db, $row['showID']), 'id'=> $row['showID']);
				}
				
				$sql = "SELECT * FROM friends WHERE (user = '{$input['data']['user']}' or friend = '{$input['data']['user']}') and accepted = true";
				if(!($result = mysqli_query($db, $sql))){
					error("ERROR: ".$sql." failed to execute");
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
				error("ERROR: ".$sql." failed to execute");
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
				error("ERROR: ".$sql." failed to execute");
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
				error("ERROR: ".$sql." failed to execute");
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
				error("ERROR: ".$sql." failed to execute");
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
					error("ERROR: ".$sql." failed to execute");
					return 1;
				}
			}else if($input['data']['action'] == 'deny'){
				$sql = "DELETE FROM friends WHERE friend = '{$input['data']['user']}' and user = '{$input['data']['requestor']}'";
				if(mysqli_query($db, $sql)){
					return 0;
				}else{
					error("ERROR: ".$sql." failed to execute");
					return 1;
				}
			}
		case "getSchedule":
			//get shows users follow
			$sql = "SELECT showID FROM following WHERE user = '{$input['data']}'";
			$following = array();
			$data = array();
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if((mysqli_num_rows($result) != 0)){
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$following[] = $row['showID'];
				}
				$start = date('Y-m-d', strtotime('-'.date('w').' days'))." 00:00:00";
				$tillEnd = 6 - date('w');
				$end = date('Y-m-d', strtotime('+'.$tillEnd.' days'))." 23:59:59";
				foreach($following as $id){
					$sql = "SELECT * FROM episodes WHERE airdate >= '$start' and airdate <= '$end' and showID = $id";
					echo $sql;
					if(!($result = mysqli_query($db, $sql))){
						error("ERROR: ".$sql." failed to execute");
						return 1;
					}
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						$row['name'] = getShow($db, $row['showID']);
						$data[] = $row;		
					}
				}
			}
			return $data;
		case "getShow":
			$sql = "SELECT * FROM shows WHERE showID = {$input['data']}";
			$data = array();
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if((mysqli_num_rows($result) == 0)){
				return 2;
			}
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$data['show'] = $row;
			}
			$sql = "SELECT * FROM episodes WHERE showID = {$input['data']} and airdate > '" . date('Y-m-d H:i:s') . "'";
			echo $sql;
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$data['episodes'][] = $row;
			}
			return $data;
		case "likeShow":
			//want to verify show exists before allowing user to like it
			$sql = "SELECT * FROM shows WHERE showID = {$input['data']['showID']}";
			if(!($result = mysqli_query($db, $sql))){
				//error happened here
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if(mysqli_num_rows($result) == 0){
				return 2;
			}
			$response = process(array('type'=>'isLiked', 'data'=> array('user'=> $input['data']['user'], 'showID'=> $input['data']['showID'])));
			if($response === 1){
				//error happened here
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}else if($response === true){
				//user already likes show
				return 2;
			}
			$sql = "INSERT INTO liked (user, showID) values ('{$input['data']['user']}', {$input['data']['showID']})";
			if(!(mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			return 0;
		case "unlikeShow":
			//check to see if show exists	
			$sql = "SELECT * FROM shows WHERE showID = {$input['data']['showID']}";
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if(mysqli_num_rows($result) == 0){
				return 2;
			}
			//check to see if user likes the show
			$response = process(array('type'=>'isLiked', 'data'=> array('user'=> $input['data']['user'], 'showID'=> $input['data']['showID'])));
			if($response === 1){
				return 1;
			}else if($response === false){
				//user doesn't like the show
				return 2;
			}
			$sql = "DELETE FROM liked WHERE user = '{$input['data']['user']}' and showID = {$input['data']['showID']}";
			if(!(mysqli_query($db, $sql))){
				return 1;
			}
			return 0;
		case "followShow":
			$sql = "SELECT * FROM shows WHERE showID = {$input['data']['showID']}";
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if(mysqli_num_rows($result) == 0){
				return 2;
			}
			$response = process(array('type'=>'isFollowing', 'data'=> array('user'=> $input['data']['user'], 'showID'=> $input['data']['showID'])));
			if($response === 1){
				return 1;
			}else if($response === true){
				return 2;
			}
			$sql = "INSERT INTO following (user, showID) values ('{$input['data']['user']}', {$input['data']['showID']})";
			if(!(mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			return 0;
		case "unfollowShow":
			$sql = "SELECT * FROM shows WHERE showID = {$input['data']['showID']}";
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if(mysqli_num_rows($result) == 0){
				return 2;
			}
			$response = process(array('type'=>'isFollowing', 'data'=> array('user'=> $input['data']['user'], 'showID'=> $input['data']['showID'])));
			if($response === 1){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}else if($response === false){
				return 2;
			}
			$sql = "DELETE FROM following WHERE user = '{$input['data']['user']}' and showID = {$input['data']['showID']}";
			echo $sql;
			if(!(mysqli_query($db, $sql))){
				echo 'oops';
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			return 0;
		case "isLiked":
			$sql = "SELECT * FROM liked WHERE showID = {$input['data']['showID']} and user = '{$input['data']['user']}'";
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if(mysqli_num_rows($result) != 0){
				//user doesn't like show already
				return true;
			}else{
				return false;
			}
		case "isFollowing":
			$sql = "SELECT * FROM following WHERE showID = {$input['data']['showID']} and user = '{$input['data']['user']}'";
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if(mysqli_num_rows($result) != 0){
				//user doesn't like show already
				return true;
			}else{
				return false;
			}
		case "search":
			$sql = "SELECT * FROM shows WHERE name LIKE '%{$input['data']}%'";
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			if(mysqli_num_rows($result) == 0){
				$response = sendAPI(array('type'=>'search', 'data'=> $input['data']));
				$response = sanatize($db, $response);
				$shows = array();
				//check to see if any shows were actually returned
				if(!(empty($response))){
					//if shows were returned loop through each show and append a value to the end of the insert statement
					//TODO optimize this code with a multiquery
					foreach($response as $show){
						//check to see if show already exists to avoid :w
						$sql = "SELECT * FROM shows WHERE name = '{$show['name']}'";
						if(!($result = mysqli_query($db, $sql))){
							error("ERROR: ".$sql." failed to execute");
							return 1;
						}
						if(mysqli_num_rows($result) == 0){
							$sql = "INSERT INTO shows (name, network, poster) values ('{$show['name']}', '{$show['network']}', '{$show['poster']}')";
							if(!(mysqli_query($db, $sql))){
								echo "broke";
								error("ERROR: ".$sql." failed to execute");
								return 1;
							}else{
								$id = mysqli_insert_id($db);
								$show['showID'] = $id;
								$shows[] = $show;
											
							}
						}else{
							while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
								$shows[] = $row;
							}
						}
					}
					return $shows;
				}else{
					//if no shows were returned send return code 2
					return 2;
				}
			}else{
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$shows[] = $row;
				}
				echo "returning ".count($shows)." results";
				var_dump($shows);
				return $shows;
			}
		case "getUsers":
			$sql = "SELECT username, email FROM users";
			if(!($result = mysqli_query($db, $sql))){
				error("ERROR: ".$sql." failed to execute");
				return 1;
			}
			$data = array();
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$data[] = $row;
			}
			var_dump($data);
			return $data;
	}
}
function error ($result){
	//include('../frontend/functions.php');
	sendError("DB: ".$result);
}

$server = new rabbitMQServer("rabbitMQ.ini", "database");
echo "server started up";
$server->process_requests('process');
?>
