<?php
include ('../backend/account.php');
echo $argv[1];
$db = mysqli_connect($hostname, $username, $password, $project);
if(mysqli_connect_error()){
	echo "Failed to connect to mysql:" .mysqli_connect_error();
if($argv[1] == "pull"){
	$data = sendRabbit($argv[3], 'development', $argv[2], rabbitMQ.ini);
	$sql ="insert into versions (system, version, contents) values ('{$argv[2]}', '$argv[3]}' '$data')";
	mysqli_query($db,$sql);
	sendRabbit($data, 'production', $argv[2], rabbitMQ.ini);
}
elseif($argv[1] == "set"){
	$sql ="select from versions where system = $argv[2] and version = $argv[3]";
	$result = mysqli_query($db, $sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		if($row['isCurrent'] == 1){
			$sql = "SELECT version, contents FROM versions WHERE system = $argv[2] AND isBad = 0 ORDER BY version DESC LIMIT 1";
			$result = mysqli_query($db, $sql)
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				sendRabbit($row['contents'], 'production', $argv[2], rabbitMQ.ini);
				$sql = "update versions SET isCurrent = 1 where version = '{$row['version']}'";
				mysqli_query($db,$sql);
   }
  }
 }
}
else{
	echo "error Command  $argv[1] not found";
}

?>
