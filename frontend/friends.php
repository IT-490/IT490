<?php
include('functions.php');
session_start();
if(isset($_REQUEST['ajax'])){
	$requestor = sanatize($_REQUEST['user']);
	$user = sanatize($_SESSION['user']);
	$action = sanatize($_REQUEST['action']);
	echo sendRabbit(array('type'=>'requestResponse', 'data'=> array('requestor'=> $requestor, 'user'=> $user, 'action'=>$action)));
	exit();
}
if(empty($_SESSION['user'])){
	header('location: index.html');
	exit();
}
include('./forums/header.php');
$user = sanatize($_SESSION['user']);
$data = sendRabbit(array('type'=> 'getFriends', 'data'=> $user));
echo "
<div class='container mt-4 mb-4'>
<button class='btn btn-dark' onclick=friends()>Friends</button>
<button class='btn btn-dark' onclick=requests()>Requests</button>
<br><br>
<div id='friends'>
<table class='table'>";
if(empty($data['friends'])){
	echo "
	<tr>
	<td style='text-align: center'>
	No Friends	
	</td>
	</tr>
	";
}else{
	foreach($data['friends'] as $friend){
		echo "
		<tr>
		<td>
		<a href='./profile.php?user=$friend'>$friend</a>
		</td>
		</tr>
		";
	}
}
echo "
</table>
</div>
<div id='requests' style = 'display: none'>
<table class='table'>";
if(empty($data['requests'])){
	echo "
	<tr>
	<td style='text-align: center'>
	No Requests	
	</td>
	</tr>
	";
}else{
	foreach($data['requests'] as $request){
		echo "
		<tr>
		<td>
		<a href='./profile.php?user=$request'>$request</a>
		</td>
		<td>
		<button class='btn btn-dark' onclick=\"request('accept', '$request')\">accept</button>
		<button class='btn btn-dark' onclick=\"request('deny', '$request')\">deny</button>
		</td>
		</tr>
		";
	}
}
echo "
</table>
</div>
<script>
function friends(){
	var x = document.getElementById('friends');
	var y = document.getElementById('requests');
	x.style.display = '';
	y.style.display = 'none';
}

function requests(){
	var x = document.getElementById('friends');
	var y = document.getElementById('requests');
	y.style.display = '';
	x.style.display = 'none';

}

function request(action, user){
	$.ajax({
	url: 'friends.php',
	type: 'POST',
	data: {'ajax': 'true', 'action': action, 'user': user}
	}).done(function(data){
		if(data == 1 || data == 2){
			alert('ERROR: invalid input');
		}else{
			location.reload();
		}
	});
}
</script>
</div>";
?>
