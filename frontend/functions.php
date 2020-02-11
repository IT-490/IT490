<?php
	function sanatize($db, $data){
		$data = trim($data);
		$data = mysqli_real_escape_string($db, $data);
		return $data;
	}
	
	function sendRabbit($data, $key){
			
	}
?>
