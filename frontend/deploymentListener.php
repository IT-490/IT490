<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($data){
	$decoded = base64_decode(string $data [, bool $strict = FALSE ] ) : string;
	file_put_contents(string deployment.zip, $decoded);
	shell_exec('gzip -d tar-xf deployment.zip');
}
$server = new rabbitMQServer("rabbitMQ.ini", "frontendDeployment");
echo "server started up";
$server->process_requests('process');
?>





