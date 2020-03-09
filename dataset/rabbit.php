<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
function process($input){
	switch($input['type']){
		case "search":
			$ch = curl_init();
			$url = "http://api.tvmaze.com/search/shows?q=".$input['data'];
			curl_setopt($ch, CURLOPT_URL, $url);
        		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			$response = json_decode($response, TRUE);
			curl_close($ch);
			$shows = array();
			foreach($shows as $show){
				$data['show'] = $show['show']['name'];
				$data['network'] = $show['show']['network']['name'];
				$data['poster'] = $show['show']['image']['original'];
				$shows[] = $data;
			}
			return $shows;

	}
}

$server = new rabbitMQServer("rabbitMQ.ini", "dmz");
echo "server started up";
$server->process_requests('process');

?>
