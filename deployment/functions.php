<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function sendRabbit($action, $data, $cluster, $system){
    if(in_array($system, array("homepage","login","forums","email","show","profile", "functions","search","frontRabbit"))){
        $server = "frontend";
    }else if(in_array($system, array("update","dmzRabbit"))){
        $server = "api";
    }else if(in_array($system, array("database","backRabbit"))){
        $server = "backend";
    }else if($system == "error"){
        if($cluster == "development"){
            $server = "frontend";
        }else{
            $server = "all";
        }
    }
    echo $cluster.".ini";
    echo $server;
    $client = new rabbitMQClient($cluster.".ini", $server);
    echo "there";
    $response = $client->send_request(array($data, $system));
    return $response;
}
?>
