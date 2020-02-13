<?php
//Include databse connection info later
$s="select * from it490 where user = '$user' and pass = '$pass'";
$t="select top 1 id from users order by id desc";
$id = $t +1;
if($s != empty){
	echo "User already exists";
}
else{
	insert into users (id, username, password, firstname, lastname) values ($id, $user, $pass, $fname, $lname);
}

?>
