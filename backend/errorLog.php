<?php
$handler = fopen('errorLog.txt','a+')
fwrite($handler, $error);

$server = new rabbitMQServer("rabbitMQ.ini", "logs");
echo "log file recieved";
$server->process_requests('error');
?>
