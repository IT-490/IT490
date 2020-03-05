<?php
include('../frontend/functions.php');
$shows = array();
for($i = 0; $i < 7; $i++){
	$ch = curl_init();
	$date = date('Y-m-d', strtotime('+'.$i.' days'));
	$url ="http://api.tvmaze.com/schedule?country=US&date=".$date;
	echo "Pulling shows from: ".$url.PHP_EOL;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	$response = json_decode($response, TRUE);
	foreach($response as $show){
		$data = array();
		$data['name'] = sanatize($show['name']);
		$data['show'] = sanatize($show['show']['name']);
		$data['network'] = sanatize($show['show']['network']['name']);
		$data['airdate'] = sanatize($show['airdate']." ".$show['airtime'].":00");
		$shows[] = $data;
	}
	curl_close($ch);
}
echo "Total Number of Shows: ".count($shows).PHP_EOL;
$response = sendRabbit(array('type'=> 'update', 'data'=> $shows));
if($response == 0){
	echo PHP_EOL."Data Inserted Succesfully!".PHP_EOL;
}else if($response == 1){
	echo PHP_EOL."Error Inserting Data!".PHP_EOL;
}
?>
