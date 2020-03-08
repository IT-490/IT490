<?php
include ('functions.php');
$users= sendRabbit(array('type'=>'getUsers'));
foreach($users as $user){
	$following = sendRabbit(array('type'=>'getSchedule','data'=> $user));
	$shows = "";
	foreach($follwing as $show){
		$shows = $shows.$show[show]."Airing on".$show[airdate]."\n";
	}
	$message = $user."!\n You have shows airing soon!\n\n".$shows;

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
}
?>
