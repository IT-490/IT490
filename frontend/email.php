<?php
include ('functions.php');
$message = sendRabit(array('type'=> 'getSchedule','data'=>$user));
$email - sendRabbit('type'=> 'email', 'data'=>$user));
$transport = (new Swift_SmtpTransport('smtp.gmail.com', 465))
	->setUsersname('njitvemailer@gmail.com')
	->setPassword('^0*1v4N0Zp')
	;
$mailer = new Swift_Mailer($transport);
$body = (new Swift_Message('Subject'))
	->setFrom('NJITV')
	->setTo($email)
	->setBody($message)
	;
$result= $mailer->send($body);
?>
