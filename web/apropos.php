<?php
include('../src/web/bootstrap.php');

if(isset($_POST['envoi_mail'])){
	
	$msg = 'Votre message aurait pu être envoyé avec les infos suivantes :<br/>'.$_POST['name_boussole'].' / '.$_POST['email_boussole'].' / '.$_POST['message_boussole'].'<br/> Mais en fait non, car le formulaire n\'est pas fini. &#x1F601;';
	
	/*
	//test de session anti robot
	$test_antispam = md5(uniqid(microtime(), true));
	$_SESSION['test_antispam'] = $test_antispam;

	<input type=“hidden” name=“test” value=”<?= $test_antispam ?>” />

	****

	if ($_SERVER[“HTTP_REFERER”] != "http://www.mondomaine.tld/contact"){
	  return;
	}

	if !(isset($_SESSION[$form.’_testVal’]) && isset($_POST[‘testVal’]) && ($_SESSION[$form.’_testVal’] == $_POST[‘testVal’])){
	  return;
	}*/
}

$contenu = null;
$liste_page = ["presentation", "donnees_perso", "aide", "contact", "accessibilite", "engagement", "mentions"];

if(isset($_GET['p']) && in_array($_GET['p'], $liste_page)){
	$contenu = 'text/'.$_GET['p'].'.html';
}

//view
require 'view/apropos.tpl.php';