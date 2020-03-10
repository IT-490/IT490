<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
function process($input){
	echo "NEW REQUEST".PHP_EOL;
	switch($input['type']){
		case "search":
			$ch = curl_init();
			$url = "http://api.tvmaze.com/search/shows?q=".$input['data'];
			echo $url;
			curl_setopt($ch, CURLOPT_URL, $url);
        		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			$response = json_decode($response, TRUE);
			curl_close($ch);
			$shows = array();
			foreach($response as $show){
				$data['name'] = $show['show']['name'];
				$data['network'] = $show['show']['network']['name'];
				$data['poster'] = $show['show']['image']['original'];
				$shows[] = $data;
			}
			var_dump($shows);
			return $shows;

	}
}

$server = new rabbitMQServer("rabbitMQ.ini", "dmz");
echo "server started up".PHP_EOL;
$server->process_requests('process');

?>
