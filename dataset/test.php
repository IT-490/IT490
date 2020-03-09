<?php
	include('../frontend/functions.php');
	$response = sendRabbit(array('type'=> 'search', 'data'=> 'garden'));
	var_dump($response);
?>
