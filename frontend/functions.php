<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

	function sendRabbit($data){
		$client = new rabbitMQClient("rabbitMQ.ini","database");
		$response = $client->send_request($data);
		return $response;	
	}

	function sendAPI($data){
		$client = new rabbitMQClient("rabbitMQ.ini","dmz");
		$response = $client->send_request($data);
		return $response;	
	}

//	function sanatize($data){
//		$data = trim($data);
//		$data = sendRabbit(array('type' => 'sanatize', 'data' => $data));
//		return $data;
//	}
	function sendError($data){
		$client = new rabbitMQClient("rabbitMQ.ini", "log");
		$response = $client->send_request($data);
		return $response;
	}
?>
