<?php
include('../functions.php');
session_start();
if(isset($_REQUEST['ajax'])){
	//add error handling here\
	$user = sanatize($_SESSION['user']);
	$content = sanatize($_REQUEST['content']);
	$id = sanatize($_REQUEST['id']);
	sendRabbit(array('type'=> 'newPost', 'data'=> array('user'=> $user, 'content'=> $content, 'id'=> $id)));
	exit();
}else{
	$id = sanatize($_REQUEST['id']);
	$cat = sanatize($_REQUEST['cat']);
	$rows = sendRabbit(array('type'=> 'getPosts', 'data'=> $id));
	include('header.php');
	echo '<div class="container mt-4 mb-4 mw-100">';
	echo '<button class="btn btn-dark" onclick=goBack()>Back</button><br><br>';
	//echo '<table>';
	foreach($rows as $row){
		echo "
		<table class='table'>
		<thead class='thead-dark'>
		<tr>
			<th scope='col'>".date('Y-m-d h:i A',strtotime($row['postDate']))."</th>
			<th></th>
			<th></th>
		</tr>
		";
		echo "<tr>";
		echo "<td colspan='3'><a href='../profile.php?user=".$row['poster']."'>".$row['poster']."</td>";
		echo "</tr>";
		echo "<tr>";
		//need to fix line breaking here
		echo "<td colspan='3' style='word-wrap: break-word;;'>".$row['content']."</td>";
		echo "</tr>";
		echo "<table>";
		echo "<br>";
	}
	//echo '</table><br>';
	if(isset($_SESSION['user'])){
		echo '<form>';
		echo '<textarea class="form-control mw-100" maxlength="2000" id="content" rows="8" ></textarea>';
		echo '</form>';
		echo '<br>';
		echo '<button class="btn btn-dark" id="content" onclick=sendData()>Reply</button>';
		echo "
		<script>
			function sendData(){
				var content = $('#content').val();
				$.ajax({
				url: 'thread.php',
				type: 'POST',
				data: {'ajax': 'true', 'content': content, 'id': $id}
				}).done(function(data){
					location.reload();
				});
			}
		</script>";
	}
	echo "
	</div>
	<script>
		function goBack(){
			window.location.href = './threads.php?id=$cat';
		}
	</script>";
}
?>
