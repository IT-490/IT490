<?php

echo $argv[1];
$conn = new mysqli("localhost", "root", "delta523", "it490db");
if($conn->connect_error){
	die("Connection Failed" . $conn->connect_error);
}

$sql1 = "SELECT isBad FROM versions";
if($sql1 == true){

}
$sql= "INSERT INTO versions (system, version, isBad, contents) VALUES ()";


?>
