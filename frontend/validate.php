<?php
session_start();

if(isset($_SESSION['user'])){
	$obj->set = True;
	$obj->username = $_SESSION['user'];
}else{
	$obj->set = False;
}
$obj = json_encode($obj);
echo $obj;
?>
