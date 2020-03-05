<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($error){
	$handler = fopen('errorLog.txt','a+');
	fwrite($handler, $error);
	fclose($handler);
	echo 'log recieved';
}
$server = new rabbitMQServer("rabbitMQ.ini", "log");
echo "server started up";
$server->process_requests('process');
?>
