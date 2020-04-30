<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($data){
	if(strpos($data,"mysql") == true){
		$decoded = base64_decode($data, FALSE );
		file_put_contents("decoded.sql", $decoded);
		exec('mysql -u root -p it490db < $decoded.sql');

	}
	if(strpos($data,"mysql") !== true){
		$decoded = base64_decode ($data, FALSE );
		file_put_contents("deployment.tar.gz", $decoded);
		exec('gzip -d deployment.tar.gz');
		exec('tar -xf deployment.tar');
	}
}
$server = new rabbitMQServer("rabbitMQ.ini", "backendDeployment");
echo "server started up";
$server->process_requests('process');
?>
