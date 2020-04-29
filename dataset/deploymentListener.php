<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($data){
	ob_start();
	switch($data){
		case "update":
			passthru("tar -ac update.php | gzip -f");
			break;
		case "rabbit":
			passthru("tar -ac rabbit.php | gzip -f");
			break;
		case "error":
			passthru("tar -ac errorLog.php | gzip -f");
			break;
	}
	$file = ob_get_contents();
	ob_end_clean();
	$file = base64_encode($file);
	return $file;
}
$server = new rabbitMQServer("rabbitMQ.ini", "datasetDeployment");
echo "server started up";
$server->process_requests('process');
?>