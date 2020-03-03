<?php
include('../functions.php');
if(isset($_REQUEST['ajax'])){
	//add error handling here
	session_start();
	$user = sanatize($_SESSION['user']);
	$content = sanatize($_REQUEST['content']);
	sendRabbit(array('type'=> 'newPost', 'data'=> array('user'=> $user, 'content'=> $content)));
	exit();
}else{
	$id = sanatize($_REQUEST['id']);
	$cat = sanatize($_REQUEST['cat']);
	$rows = sendRabbit(array('type'=> 'getPosts', 'data'=> $id));
	include('header.php');
	echo '<button onclick="goBack()">Back</button>';
	echo '<table>';
	foreach($rows as $row){
		echo "<tr>";
		echo "<td><a href='../profile.php?user=".$row['poster']."'>".$row['poster']."</td>";
		echo "<td>".$row['postDate']."</td>";
		echo "<td>".$row['content']."</td>";
		echo "</tr>";
	}
	echo '</table>';
	echo '<form>';
	echo '<textarea id="content" rows="10" cols="50"></textarea>';
	echo '</form>';

	echo '<button id="content" onclick="sendData()">Reply</button>';
	echo "
	<script>
		function sendData(){
			var content = $(#'content').val();
			$.ajax({
			url: 'thread.php',
			type: 'POST',
			data: {'ajax': 'true', 'content': content}
			}).done({
				location.reload();
			});
		}
	</script>";
}
?>
