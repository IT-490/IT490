<?php
include('../functions.php');
if(isset($_REQUEST['ajax'])){
	//add error handling here
}else{
	$id = sanatize($_REQUEST['id']);
	$rows = sendRabbit(array('type'=> 'getThreads', 'data'=> $id));
	include('header.php');
	echo '<button onclick="goBack()">Back</button>';
	echo '<table>';
	foreach($rows as $row){
		echo "<tr>";
		echo "<td><a href='./thread.php?cat=".$id."&id=".$row['topicID']."'>".$row['subject']."</td>";
		echo "<td><a href='../profile.php?user=".$row['topicCreator']."'>".$row['topicCreator']."</td>";
		echo "<td>".$row['lastPost']."</td>";
		echo "</tr>";
	}
	echo '</table>';
	echo '<button id="content" onclick="newThread()">New Thread</button>';
	echo "
	<script>
		function newThread(){
			window.location.href='./newThread.php?cat=$id';
		}
	</script>";
}
?>
