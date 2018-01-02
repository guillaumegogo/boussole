<?php
include('../src/web/bootstrap.php');

$sujets_contact=array("contact_pro"=>"Demande de contact professionnel", "offre_manquante"=>"Offre de service manquante", "pb_technique"=>"Problème technique", "crij"=>"Demande de contact de mon CRIJ", "autre"=>"Autre sujet"); //le contenu de la liste des thèmes du formulaire de contact

if(isset($_POST['envoi_mail'])){
	
	//if ($_SERVER[“HTTP_REFERER”] != "http://www.mondomaine.tld/contact"){ //protection à envisager
	if (isset($_SESSION['web']['test_antispam']) && isset($_POST['test']) && ($_SESSION['web']['test_antispam'] == $_POST['test'])){
		if (isset($_POST['name_contact']) && isset($_POST['subject_contact']) && isset($_POST['email_contact']) && isset($_POST['message_contact'])){
			
			$sujet = $sujets_contact[$_POST['subject_contact']];
			$send = envoi_mail_contact ($_POST['name_contact'], $sujet, $_POST['email_contact'], $_POST['message_contact']);
			
			if ($send){
				$msg = 'Votre message a bien été envoyé';
				
			}else{
				$msg = 'Problème technique...';
			}
		}else{
			$msg="Tous les champs sont obligatoires";
		}
	}else{
		$msg="Ne seriez-vous pas un robot ? Si ce n'est pas le cas, vous avez l'autorisation de nous contacter à boussole [at] jeunesse-sports.gouv.fr";
	}
}

$contenu = null;
$liste_page = ["presentation", "donnees_perso", "aide", "contact", "accessibilite", "engagement", "mentions"];

if(isset($_GET['p']) && in_array($_GET['p'], $liste_page)){
	$contenu = 'text/'.$_GET['p'].'.inc.html';
	
	if($_GET['p'] == "contact"){
		$contenu = 'text/contact.inc.php';
		
		//test de session anti robot pour le formulaire de contact
		$test_antispam = md5(uniqid(microtime(), true));
		$_SESSION['web']['test_antispam'] = $test_antispam;
	}
}

//view
require 'view/apropos.tpl.php';