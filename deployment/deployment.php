<?php
include('../backend/account.php');
include('./functions.php');
echo $argv[1];
$db = mysqli_connect($hostname, $username, $password, $project);
mysqli_select_db($db, $project);
if(mysqli_connect_error()){
	echo "Failed to connect to mysql:" .mysqli_connect_error();
}	
if($argv[1] == "pull"){
	$data = sendRabbit($argv[1], $argv[2], 'development', $argv[2]);
	$sql ="update versions set isCurrent = 0 where isCurrent = 1;insert into versions (system, version, contents) values ('{$argv[2]}', '{$argv[3]}', '$data')";
	echo mysqli_multi_query($db,$sql);
	echo "data inserted... Sending...";
	sendRabbit($argv[1], $data, 'production', $argv[2]);
}elseif($argv[1] == "set"){
	$sql ="select isCurrent from versions where system = '$argv[2]' and version = '$argv[3]'";
	$result = mysqli_query($db, $sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		if($row['isCurrent'] == 1){
			echo "were here np";
			$sql = "SELECT version, contents FROM versions WHERE system = '$argv[2]' AND isBad = 0 and version != '$argv[3]' ORDER BY version DESC LIMIT 1";
			$result = mysqli_query($db, $sql);
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				sendRabbit($argv[1], $row['contents'], 'production', $argv[2]);
				$sql = "update versions SET isCurrent = 1 where version = '{$row['version']}'";
				mysqli_query($db,$sql);
   }
		}
		$sql = "update versions set isCurrent = 0, isBad = 1 where version = '$argv[3]' and system = '$argv[2]'";
		echo mysqli_query($db,$sql);
 }
}
else{
	echo "error Command  $argv[1] not found";
}

?>
