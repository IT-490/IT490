<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($data){
	echo "Message arrived";

	if(strpos($data[1],"mysql") == true){
		$decoded = base64_decode($data[0], FALSE );
		file_put_contents("decoded.sql", $decoded);
		exec('mysql -u root -p it490db < decoded.sql');
		exec('rm decoded.sql');
		return "done";
	}
	if(strpos($data[1],"mysql") !== true){
		$decoded = base64_decode ($data[0]);
		file_put_contents("test.base", $data[0]);
		file_put_contents("deployment.tar.gz", $decoded);
		exec('gzip -df deployment.tar.gz');
		exec('tar -xf deployment.tar');
		exec('rm deployment.tar');
		return "done";
	}
}
$server = new rabbitMQServer("rabbitMQ.ini", "backendDeployment");
echo "server started up";
$server->process_requests('process');
?>
