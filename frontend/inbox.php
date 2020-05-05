<?php
include('functions.php');
if(isset($_REQUEST['ajax'])){
    //
}else{
    session_start();
    $user = $_SESSION['user'];
    $data = sendRabbit(array('type'=> 'getMessages', 'user'=> $user));
    include('./forums/header.php');
    echo '<div class="container mt-4 mb-4 mw-100">';
    if($data == 2){
        echo '<h4 style="text-align: center">ERROR 404: MESSAGE(S) NOT FOUND!</h4>';
    }else{
        echo '
		<h4>Inbox</h4><br>
		<table class="table">
		<thead class="thead-dark">
		<tr>
			<th scope="col">From</th>
			<th scope="col">Message</th>
			<th scope="col"></th>
		</tr>
		';

        foreach($data['rows'] as $row){
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['message'] . "</td>";
            echo "<td><a href='messages.php?user=". $row['username']. "'>Reply</a></td>";
            echo "</tr>";
        }
        echo '</table>';

    }
    echo '</div>';
}
?>
