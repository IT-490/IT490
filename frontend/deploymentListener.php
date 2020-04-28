<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function process($data){
	ob_start();
	switch($data){
		case "homepage":
			passthru("tar -ac index.html | gzip -f");
			break;
		case "login":
			passthru("tar -ac login.html login.php register.html register.php logout.php validate.php | gzip -f");
			break;
		case "forums":
			passthru("tar -ac ./forums/header.php ./forums/index.html ./forums/newThread.php ./forums/thread.php ./forums/threads.php | gzip -f"); 
			break;
		case "show":
			passthru("tar -ac shows.php | gzip -f");
			break;
		case "profile":
			passthru("tar -ac profile.php schedule.php friends.php | gzip -f");
			break;
		case "error":
			passthru("tar -ac errorLog.php | gzip -f");
			break;
		case "rabbit":
			passthru("tar -ac rabbitMQ.ini rabbitMQLib.inc path.inc get_host_info.inc local.ini | gzip -f");
			break;
		case "functions":
			passthru("tar -ac functions.php | gzip -f");
			break;
		case "search":
			passthru("tar -ac search.php | gzip -f");
			break;
		case "email":
			passthru("tar -ac email.php | gzip -f");
			break;
	}
	$file = ob_get_contents();
	ob_end_clean();
	$file = base64_encode($file);
	return $file;
}
$server = new rabbitMQServer("rabbitMQ.ini", "frontendDeployment");
echo "server started up";
$server->process_requests('process');
?>
