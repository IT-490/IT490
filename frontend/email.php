<?php
include ('functions.php');
$message = sendRabit(array('type'=> 'getSchedule','data'=>$user));
$email - sendRabbit('type'=> 'email', 'data'=>$user));
mail($email, "show schedule", $message );

?>
