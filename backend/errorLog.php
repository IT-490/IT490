<?php
require_once('/home/ubuntu/git/IT490/path.inc');
require_once('/home/ubuntu/git/IT490/get_host_info.inc');
require_once('/home/ubuntu/git/IT490/rabbitMQLib.inc');
function process($error){
//	if(isset($error['type'])){
//		return null;
//	}
	$handler = fopen('errorLog.txt','a+');
	fwrite($handler, $error."\n");
	fclose($handler);
	echo 'log recieved'.PHP_EOL;
}
$server = new rabbitMQServer("rabbitMQ.ini", "log");
echo "server started up";
$server->process_requests('process');
?>
