#!/usr/bin/php
<?php
include ('functions.php');
require_once('vendor/autoload.php');
$users= sendRabbit(array('type'=>'getUsers'));
var_dump($users);
foreach($users as $user){
	$following = sendRabbit(array('type'=>'getSchedule','data'=> $user['username']));
	$shows = "";
	foreach($following as $show){
		if(strtotime($show['airdate']) < strtotime(date('Y-m-d H:i', strtotime('+2 days')))){
			$shows = $shows."<li>".$show['name']." Airing on ".date('l, F jS \a\t g:i A e',strtotime($show['airdate']))."</li>";
		}
	}
	if(!empty($shows)){
	$message = $user['username']."!<br>You have shows airing soon!<br><br><ul>".$shows."</ul>";
	echo PHP_EOL.$message.PHP_EOL;
$transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
	->setUsername('njitvemailer@gmail.com')
	->setPassword('^0*1v4N0Zp')
	;
	$mailer = new Swift_Mailer($transport);
$body = (new Swift_Message('New Shows!'))
	->setFrom(array('Emailer@NJI.TV'=> 'NJITV'))
	->setTo($user['email'])
	->setBody($message, 'text/html')
	;
$result= $mailer->send($body);
}	
}
?>
