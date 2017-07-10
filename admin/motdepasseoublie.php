<?php
//********* pour repartir sur une nouvelle session propre...
session_start();
session_unset(); 

//********* include...
require('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');
$msg='';
$delai_depasse=0;
$vue='normal';
$id_utilisateur=0;

//post du formulaire
if (isset($_POST['login'])) {

	$vue='';
	$msg='Si votre adresse de courriel est bien connue de nos services, un message vient d\'y être envoyé. Si ce n\'est pas le cas, contactez votre administrateur.';
	
	$token=password_hash($_POST['login']+time(), PASSWORD_DEFAULT);
	$sql = 'UPDATE `bsl_utilisateur` SET `reinitialisation_mdp`= ? ,`date_demande_reinitialisation`= NOW() WHERE `email`= ?'; 
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 'ss', $token, $_POST['login']);	
	if (mysqli_stmt_execute($stmt)) {
		if (mysqli_stmt_affected_rows($stmt) > 0) {
			$to = $_POST['login'];
			$subject = 'Réinitialisation de votre mot de passe';
			$message = "<html><p>Vous avez demandé la réinitialisation de votre mot de passe.</p> "
			. "<p>Pour saisir votre nouveau mot de passe, merci de cliquer sur ce lien : <a href=\"".$url_admin."/motdepasseoublie.php?cle=".$token."\">".$url_admin."/motdepasseoublie.php?cle=".$token."</a></p>"
			. "<p>Merci d'utiliser le lien dans les trois jours, après quoi il ne sera plus valide.</html>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=charset=utf-8' . "\r\n";
			$headers .= 'From: La Boussole des jeunes <boussole@jeunes.gouv.fr>' . "\r\n";
			$headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
			$envoi_mail = mail($to, $subject, $message, $headers);
			if(!$envoi_mail) {
				$msg='Le message n\'a pas pu être envoyé, veuillez réessayer ultérieurement. Si le problème persiste, contactez votre administrateur.';
			}
		}
	}else {
		$msg = $message_erreur_bd;
	}
}

//activation du lien depuis le mail
if (isset($_GET['cle'])) {
	
	$vue='reinit';
	$sql = 'SELECT `id_utilisateur`,`date_demande_reinitialisation` FROM `bsl_utilisateur` WHERE `reinitialisation_mdp`= ?'; 
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 's', $_GET['cle']);
	
	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_store_result($stmt);
		if (mysqli_stmt_num_rows($stmt) > 0) {
			mysqli_stmt_bind_result($stmt, $id_utilisateur, $date_demande_reinitialisation);
			mysqli_stmt_fetch($stmt);
			if (strtotime($date_demande_reinitialisation) < time()-(3600*24*3)){  //3 jours
				$delai_depasse=1;
				$msg='Le lien indiqué est périmé, merci de refaire une demande';
			}
		}
	}
}

//nouveau mot de passe
if (isset($_POST['maj_id'])) {

	$vue='reinit';
	$id_utilisateur=$_POST['maj_id'];
	if ($_POST["nouveaumotdepasse"]==$_POST["nouveaumotdepasse2"]){
		$sql = "UPDATE `bsl_utilisateur` SET `motdepasse` = ?, reinitialisation_mdp = NULL WHERE `id_utilisateur` = ?"; 
		$stmt = mysqli_prepare($conn, $sql);
		$m=password_hash($_POST["nouveaumotdepasse"], PASSWORD_DEFAULT);
		mysqli_stmt_bind_param($stmt, 'si', $m, $id_utilisateur);
		
		if (mysqli_stmt_execute($stmt)) {
			if (mysqli_stmt_affected_rows($stmt) > 0) {
				$msg = 'Mot de passe modifié.';
				$last_id=$_POST["maj_id"];
			}else{
				$msg='Aucun compte n\'a été modifié (?)';
			}
		}else{
			$msg=$message_erreur_bd;
		}
	}else{
		$msg = 'Les nouveaux mots de passe saisis ne correspondent pas.';
	}
}

//view
require 'view/motdepasseoublie.tpl.php';