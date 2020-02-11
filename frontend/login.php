<?php
include("functions.php");

$db = mysqli_connect($hostname, $username, $password, $project);
if(mysqli_connect_error()){
	print('Failled to connect to database.' + mysqli_connect_error);
	exit();
}


?>
