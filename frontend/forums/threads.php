<?php
include('../functions.php');
if(isset($_REQUEST['ajax'])){
	//add error handling here
}else{
	session_start();
	$id = sanatize($_REQUEST['id']);
	$data = sendRabbit(array('type'=> 'getThreads', 'data'=> $id));
	include('header.php');
	echo '<div class="container mt-4 mb-4 mw-100">';
	if($data == 2){
		echo '<h4 style="text-align: center">ERROR 404: SHOW NOT FOUND</h4>';
	}else{
		echo '
		<h4>'.$data['show'].'</h4><br>
		<table class="table">
		<thead class="thead-dark">
		<tr>
			<th scope="col">Subject</th>
			<th scope="col">Replies</th>
			<th scope="col">Last Post</th>
		</tr>
		';

		foreach($data['rows'] as $row){
			echo "<tr>";
			echo "<td><a href='./thread.php?cat=".$id."&id=".$row['topicID']."'>".$row['subject']."</a><br> Started by <a href='../profile.php?user=".$row['topicCreator']."'>".$row['topicCreator']."</a> ". date('Y-m-d',strtotime($row['lastPost'])) ."</td>";
			echo "<td>".$row['postCount']."</td>";
			echo "<td><a href=' ../profile.php?user=". $row['lastPoster']. "'>".$row['lastPoster']."</a><br>".date('Y-m-d',strtotime($row['lastPost']))."</td>";
			echo "</tr>";
		}
		echo '</table>';
		if(isset($_SESSION['user'])){
			echo '<button class="btn btn-dark" id="content" onclick="newThread()">New Thread</button>';
			echo "
			<script>
				function newThread(){
					window.location.href='./newThread.php?id=$id';
				}
			</script>";
		}
	}
	echo '</div>';
}
?>
