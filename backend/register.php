<?php
$db = mysqli_connect($

$recieved = recieveRabbit();
if($recieved['type'] = 'login'){
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
}
if($recieved['type'] = 'register'){
	$s="select * from it490 where user = '$user' and pass = '$pass'";
	$t="select top 1 id from users order by id desc";
	$id = $t +1;
	if($s != empty){
		return 1;
	}
	else{
		insert into users (id, username, password, firstname, lastname) values ($id, $user, $pass, $fname, $lname);
	}
}
if($recieved['type'] = 'sanatize'){

}
?>
