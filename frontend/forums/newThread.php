<?php
include('../functions.php');
session_start();
if(empty($_SESSION['user'])){
	header('location: ./threads.php?id='.$_REQUEST['id']);
}else{
	if(isset($_REQUEST['ajax'])){
		$content = sanatize($_REQUEST['content']);
		$subject = sanatize($_REQUEST['subject']);
		$user = sanatize($_SESSION['user']);
		$show = sanatize($_REQUEST['show']);
		if(empty($subject) || empty($content)){
			echo false;
		}else{
			echo $response = sendRabbit(array('type'=> 'newThread','data'=> array('content'=> $content, 'subject'=>$subject, 'user'=> $user, 'show'=> $show)));
		}	
		exit();
	}else{
		include('header.php');
		$id = sanatize($_REQUEST['id']);
		echo '<div class="container mw-75 mt-4 mb-4">';
		echo '<form id="thread-form">';
		echo '<label for="subject">Subject</label>';
		echo '<input class="form-control" id="subject" type="text" required>';
		echo '<label for="content">Post</label>';
		echo '<textarea class="form-control" rows="10" cols="50" id="content" required></textarea><br>';
		echo '<button type="submit" class="btn btn-dark">Post</button>';
		echo '</form>';
		echo '</div>';
		echo '
		<script>
			$("#thread-form").on("submit", function(e){
			e.stopPropagation();
			e.preventDefault();
			sendData();
			});
			function sendData(){
				var subject = $("#subject").val();
				var content = $("#content").val();
				$.ajax({
				url: "newThread.php",
				type: "POST",
				data: {"ajax": "true", "subject": subject, "content": content, "show": '.$id.'},
				datatype: "text"
				}).done(function(data){
					if(data == false){
						alert("ERROR: invalid data");
					}else{
						window.location.href = "thread.php?cat='.$id.'&id=" + data;
					}
				});
			}
		</script>';
	}
}
?>
