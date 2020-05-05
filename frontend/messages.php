<?php
include('functions.php');
session_start();
if(empty($_SESSION['user'])){
    header('location: ./index.html');
}else{
    if(isset($_REQUEST['ajax'])){
        $message = $_REQUEST['message'];
        $to = $_REQUEST['to'];
        $user = $_SESSION['user'];
        if(empty($message)){
            echo false;
        }else{
            echo $response = sendRabbit(array('type'=> 'newMessage','data'=> array('message'=> $message, 'to'=>$to, 'from'=> $user)));
        }
        exit();
    }else{
        include('./forums/header.php');
        $friendUsername = $_REQUEST['user'];

        echo '<div class="container mw-75 mt-4 mb-4">';
        echo '<form id="message-form">';
        echo '<label for="message">Message</label>';
        echo '<textarea class="form-control" rows="10" cols="50" id="message" required></textarea><br>';
        echo '<button type="submit" class="btn btn-dark">Send</button>';
        echo '</form>';
        echo '</div>';
        echo '
		<script>
			$("#message-form").on("submit", function(e){
			e.stopPropagation();
			e.preventDefault();
			sendMessage();
			});
			function sendMessage(){
				var message = $("#message").val();
				$.ajax({
				url: "messages.php",
				type: "POST",
				data: {"ajax": "true", "message": message, "to": "' . $friendUsername .'"},
				datatype: "text"
				}).done(function(data){
					if(data === false){
						alert("ERROR: invalid data");
					}else{
						window.location.href = "inbox.php";
					}
				});
			}
		</script>';
    }
}
?>
