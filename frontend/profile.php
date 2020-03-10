<?php
include('functions.php');
session_start();
if(isset($_REQUEST['ajax'])){
	$user =$_REQUEST['user'];
	$requestor =$_SESSION['user'];
	if($_REQUEST['action'] == 'add'){
		echo sendRabbit(array('type'=> 'addFriend', 'data'=> array('user'=> $user, 'requestor'=> $requestor)));
	}else if($_REQUEST['action'] == 'remove'){
		echo sendRabbit(array('type'=> 'removeFriend', 'data'=> array('user'=> $user, 'requestor'=> $requestor)));
	}
	exit();
}
include('./forums/header.php');
<<<<<<< Updated upstream
if(empty($_REQUEST['user']) && isset($_SESSION['user'])){
	$user = sanatize($_SESSION['user']);
}else{
	$user = sanatize($_REQUEST['user']);
}
$requestor = sanatize($_SESSION['user']);
=======
$user =$_REQUEST['user'];
$requestor =$_SESSION['user'];
>>>>>>> Stashed changes
$data = sendRabbit(array('type'=> 'getProfile', 'data'=> array('user'=> $user, 'requestor'=> $requestor)));
echo "<div class='container mt-4 mb-4 '>";
if($data == 2){
	echo "<h4 style='text-align: center'>ERROR 404: USER NOT FOUND</h4>";
}else{
	echo "
		<h2 style='text-align: center'>{$user}</h2>
		<hr>";
		
	if($user != $requestor && isset($_SESSION['user'])){
		if($data['isFriend'] == 0){
			echo "<button class='btn btn-dark' onclick=respond('add')>Add Friend</button>";
		}else if($data['isFriend'] == 1){
			echo "<button class='btn btn-dark' onclick=respond('remove')>Remove Friend</button>";
		}
		echo "
		<script>
			function respond(action){
				$.ajax({
				url: 'profile.php',
				type: 'POST',
				data: {'ajax': 'true', 'action': action, 'user': '$user'}
				}).done(function(data){
					if(data == 2 || data == 1){
						alert(data);
						alert('ERROR: invalid request');
					}else{
						location.reload();
					}
				});
			}
		</script>
		";
	}else if($user == $requestor && isset($_SESSION['user'])){
		echo "<button class='btn btn-dark' onclick=\"window.location.href='./friends.php'\">Friends</button>";
	}
	echo "		
		<br><br>
		<table class='table'>
		<thead class='thead-dark'>
		<tr>
			<th style='text-align: center'>Followed Shows</th>
		</tr>";
	foreach($data['shows'] as $show){
		echo "
		<tr>
			<td style='text-align: center'><a href='./shows.php?id={$show['id']}'>{$show['name']}</a></td>
		</tr>
		";
	}
	echo "

		</table>
		<br>
		<table class='table'>
		<thead class='thead-dark'>
		<tr>
			<th style='text-align: center'>Friends</th>
		</tr>";
	foreach($data['friends'] as $friend){
		echo "
		<tr>
			<td style='text-align: center'><a href='./profile.php?user=$friend'>$friend</a></td>
		</tr>
		";
	}
	echo "

		</table>
		<hr>
	";
}

echo"</div>";
?>
