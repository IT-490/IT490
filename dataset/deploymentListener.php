<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($data){
	$decode = base64_decode($data, FALSE);
	file_put_contents("deployment.tar.gz", $decode);
	exec('gzip -d deployment.tar,gz');
	exec('tar -xf deployment.tar');
}
$server = new rabbitMQServer("rabbitMQ.ini", "datasetDeployment");
echo "server started up";
$server->process_requests('process');
?>
