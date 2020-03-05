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
			foreach($response as $show){
				for($i = 0; $i < 7; $i++){
					$date = date('Y-m-d', strtotime('+'.$i.' days'));
					$url = "http://api.tvmaze.com/shows/".$show['show']['id']."/episodesbydate?date=".$date;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$res = curl_exec($ch);
					$res = json_decode($res, TRUE);
					if($res['status'] == '404'){
						$data['show'] = $show['show']['name'];
						$data['network'] = $show['show']['network']['name'];
						$data['poster'] = $show['show']['image']['original'];
						$shows[] = $data;
					}else{	
						foreach($res as $episode){
							$data = array();
							$data['name'] = sanatize($show['name']);
							$data['show'] = sanatize($show['show']['name']);
							$data['network'] = sanatize($show['show']['network']['name']);
						        $data['airdate'] = sanatize($show['airdate']." ".$show['airtime'].":00");
							$shows[] = $data;
						}
					}
					curl_close($ch);
				}
			}
			return $shows;

	}
}

$server = new rabbitMQServer("rabbitMQ.ini", "dmz");
echo "server started up";
$server->process_requests('process');

?>
