<?php

include('../src/admin/bootstrap.php');

session_unset();

$msg = '';
$vue = 'normal';
$token = null;

//post du formulaire
if (isset($_POST['login']) && !empty($_POST['login'])) {
	$vue = '';
	$msg = 'Si votre adresse de courriel est bien connue de nos services, un message vient d\'y être envoyé. Si ce n\'est pas le cas, contactez votre administrateur.';
	secu_send_reset_email($_POST['login']);
}

//activation du lien depuis le mail
if (isset($_GET['t']) && !empty($_GET['t'])) {
	$vue = 'reinit';
	$token = $_GET['t'];
	if (!secu_check_reset_token($token)) {
		$token = null;
		$msg = 'Le lien indiqué est périmé, merci de refaire une demande';
	}
}

//nouveau mot de passe
//TODO ajouter longueur minimale pour password
if (isset($_POST['token']) && !empty($_POST['token']) && secu_check_reset_token($_POST['token'])) {
	$vue = 'reinit';
	if ($_POST["nouveaumotdepasse"] === $_POST["nouveaumotdepasse2"] && strlen($_POST["nouveaumotdepasse"]) >= PASSWD_MIN_LENGTH) {
		secu_reset_password($_POST["nouveaumotdepasse"], $_POST['token']);
		$msg = 'Mot de passe réinitialisé.';
		$token = null;
	} else {
		$msg = 'Les mots de passe saisis doivent correspondre et faire au moins '.PASSWD_MIN_LENGTH.' caractères.';
		$token = $_POST['token'];
	}
}

//view
require 'view/motdepasseoublie.tpl.php';