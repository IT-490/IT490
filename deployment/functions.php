<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function sendRabbit($action, $data, $cluster, $system, $iniFile){
    if($system == "homepage" || "login" || "forums" || "email" || "show" || "profile" || "functions" || "search" || "frontRabbit"){
        $server = "frontend";
    }else if($system == "update" || "dmzRabbit"){
        $server = "api"
    }else if($system == "database" || "backRabbit"){
        $server = "backend"
    }else if($system == "error"){
        if($cluster == "development"){
            $server = "frontend"
        }else{
            $server = "all";
        }
    }

    $client = new rabbitMQClient($cluster."ini", $server);
    $response = $client->send_request($data);
    return $response;
}
?>