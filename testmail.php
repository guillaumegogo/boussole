<?php

$emails = ['guillaume.gogo@jeunesse-sports.gouv.fr', 'guillaume.gogo@gmail.com', 'ivan.sutter@laposte.net']; // ['guillaume@yopmail.fr', 'guillaume.gogo@gmail.com','guillaume.gogo@jeunesse-sports.gouv.fr', 'ivan.sutter@sg.social.gouv.fr', 'guillaume@gogo.fr', ];
$cas = [1,2,3]; //1,2,3,4,5

$message = "<html><p>blabla avec html</p><p><a href=\"http://boussole.beta.gouv.fr\">avec un lien</a></html>";
$head = 'MIME-Version: 1.0' . "\r\n";
$head .= 'Content-type: text/html; charset=utf-8' . "\r\n";

$message_nohtml = "blablabla sans html";
$head_nohtml .='Content-Type: text/plain; charset="utf-8"'." "; // ici on envoie le mail au format texte encodé en UTF-8
	
foreach($emails as $dest){
	foreach($cas as $k){
		if($k==1){
	// bblba\src\admin\secu.php
	$subject = mb_encode_mimeheader('TEST 1 : from jeunes.gouv.fr', 'UTF-8');
	$headers = $head . 'From: La Boussole des jeunes <boussole@jeunes.gouv.fr>' . "\r\n";
	$send[1] = mail($dest, $subject, $message, $headers) ? "OK" : "KO";
	/*
	$subject = mb_encode_mimeheader('TEST 6 : from jeunes.gouv.fr', 'UTF-8');
	$headers = $head_nohtml . 'From: La Boussole des jeunes <boussole@jeunes.gouv.fr>' . "\r\n";
	$send[6] = mail($dest, $subject, $message_nohtml, $headers) ? "OK" : "KO";*/
	
		}elseif($k==2){
	// bblba\src\web\functions.php
	$subject = mb_encode_mimeheader('TEST 2 : from boussole.jeunes.gouv.fr', 'UTF-8');
	$headers = $head . 'From: La Boussole des jeunes <noreply@boussole.jeunes.gouv.fr>' . "\r\n";
	$send[2] = mail($dest, $subject, $message, $headers) ? "OK" : "KO";
	
	/*
	$subject = mb_encode_mimeheader('TEST 7 : from boussole.jeunes.gouv.fr', 'UTF-8');
	$headers = $head_nohtml . 'From: La Boussole des jeunes <noreply@boussole.jeunes.gouv.fr>' . "\r\n";
	$send[7] = mail($dest, $subject, $message_nohtml, $headers) ? "OK" : "KO";
	
		}elseif($k==3){
	// test_0201\src\admin\secu.php
	// beta_0201\src\web\functions.php
	$subject = mb_encode_mimeheader('TEST 3 : from jeunesse-sports.gouv.fr avec reply-to et x-mailer', 'UTF-8');
	$headers = $head . 'From: La Boussole des jeunes <boussole@jeunesse-sports.gouv.fr>' . "\r\n".
		'Reply-To: no-reply@jeunesse-sports.gouv.fr'."\r\n" .
		'X-Mailer: PHP/' . phpversion();
	$send[3] = mail($dest, $subject, $message, $headers) ? "OK" : "KO";
	
	$subject = mb_encode_mimeheader('TEST 8 : from jeunesse-sports.gouv.fr avec reply-to et x-mailer', 'UTF-8');
	$headers = $head_nohtml . 'From: La Boussole des jeunes <boussole@jeunesse-sports.gouv.fr>' . "\r\n".
		'Reply-To: no-reply@jeunesse-sports.gouv.fr'."\r\n" .
		'X-Mailer: PHP/' . phpversion();
	$send[8] = mail($dest, $subject, $message_nohtml, $headers) ? "OK" : "KO";
	
		}elseif($k==4){
	// tests ++
    $subject = mb_encode_mimeheader('TEST 4 : from accelance.net ', 'UTF-8');
	$headers = $head . 'From: La Boussole des jeunes <www-data@mtsfp-vm-djepva-boussole.mtsfp.accelance.net>' . "\r\n";
	$send[4] = mail($dest, $subject, $message, $headers) ? "OK" : "KO";
	
    $subject = mb_encode_mimeheader('TEST 9 : from accelance.net ', 'UTF-8');
	$headers = $head_nohtml . 'From: La Boussole des jeunes <www-data@mtsfp-vm-djepva-boussole.mtsfp.accelance.net>' . "\r\n";
	$send[9] = mail($dest, $subject, $message_nohtml, $headers) ? "OK" : "KO";
	
		}elseif($k==5){
	$subject = mb_encode_mimeheader('TEST 5 : from accelance.net avec reply-to', 'UTF-8');
	$headers = $head . 'From: La Boussole des jeunes <www-data@mtsfp-vm-djepva-boussole.mtsfp.accelance.net>' . "\r\n".
		'Reply-To: no-reply@jeunesse-sports.gouv.fr'."\r\n" .
		'X-Mailer: PHP/' . phpversion();
	$send[5] = mail($dest, $subject, $message, $headers) ? "OK" : "KO";
	
	$subject = mb_encode_mimeheader('TEST 10 : from accelance.net avec reply-to', 'UTF-8');
	$headers = $head_nohtml . 'From: La Boussole des jeunes <www-data@mtsfp-vm-djepva-boussole.mtsfp.accelance.net>' . "\r\n".
		'Reply-To: no-reply@jeunesse-sports.gouv.fr'."\r\n" .
		'X-Mailer: PHP/' . phpversion();
	$send[10] = mail($dest, $subject, $message_nohtml, $headers) ? "OK" : "KO";
	break;*/
		}
	}

	echo $dest." : ";
	foreach($send as $k=>$v) echo $k." ".$v." ";
	echo "<br/>";
}