<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($data){
	echo $data[0].PHP_EOL;
	echo $data[0];

	ob_start();
	switch($data[0]){
		case "error":
			passthru("tar -ac errorLog.php | gzip -f");
			break;
		case "backRabbit":
			passthru("tar -ac rabbit.php account.php | gzip -f");
			break;
		case "database":
			passthru("mysqldump --no-data -u test -pdelta523 | gzip -f");
			break;
	}
	$file = ob_get_contents();
	ob_end_clean();
	$file = base64_encode($file);
	file_put_contents("test.base", $file);
	return $file;
}
$server = new rabbitMQServer("rabbitMQ.ini", "backendDevDeployment");
echo "server started up";
$server->process_requests('process');
?>
