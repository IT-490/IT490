<?php
include ('functions.php');
require_once('../vendor/autoload.php');
$users= sendRabbit(array('type'=>'getUsers'));
var_dump($users);
foreach($users as $user){
	$following = sendRabbit(array('type'=>'getSchedule','data'=> $user['username']));
	$shows = "";
	foreach($following as $show){
		if(strtotime($show['airdate']) < strtotime(date('Y-m-d H:i', strtotime('+2 days')))){
			$shows = $shows.$show['name']." Airing on ".date('l, F jS \a\t g:i A e',strtotime($show['airdate']))."\n";
		}
	}
	if(!empty($shows)){
	$message = $user['username']."!\nYou have shows airing soon!\n\n".$shows;
	echo PHP_EOL.$message.PHP_EOL;
$transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
	->setUsername('njitvemailer@gmail.com')
	->setPassword('^0*1v4N0Zp')
	;
	$mailer = new Swift_Mailer($transport);
$body = (new Swift_Message('New Shows!'))
	->setFrom(array('Emailer@NJI.TV'=> 'NJITV'))
	->setTo($user['email'])
	->setBody($message)
	;
$result= $mailer->send($body);
}	
}
?>
