<?php

include('../src/web/bootstrap.php');

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['ville_habitee']) || !isset($_SESSION['besoin'])) {
	header('Location: index.php');
	exit();
}

//********* si demande de contact
if (isset($_POST['coordonnees']) && isset($_POST['id_offre'])) {
	
	//*********** création de la demande
	$id_demande = create_demande($_POST['id_offre'], $_POST['coordonnees']);

	//*********** envoi des mails
	if ($id_demande) {
		$row = get_offre($_POST['id_offre']);
		$resultat = envoi_mails_demande( $row['courriel_offre'], $row['nom_offre'], $_POST['coordonnees'] );

		if (!$resultat){
			$resultat = "<img src=\"img/exclamation.png\" width=\"24px\"> Ta demande de contact pour l'offre «&nbsp;".$row['nom_offre']."&nbsp;» a bien été enregistrée mais aucun courriel complémentaire n'a été envoyé. Tu peux contacter directement le professionnel.";
		}
	} else {
		$resultat = "<img src=\"img/exclamation.png\" width=\"24px\"> L'application a rencontré un problème. Ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.";
	}
}

//********* on va chercher les offres et les sous-thèmes
$t = get_offres_demande($_SESSION['critere'], $_SESSION['type'], $_SESSION['besoin'], $_SESSION['code_insee']);
$sous_themes = $t[0];
$offres = $t[1];

//********* contenu du template 
$aucune_offre = '';
$nb_offres = 0;
foreach($offres as $offres_par_theme){
	$nb_offres += count($offres_par_theme);
}

switch($nb_offres){
	case 0:
		$msg = 'Aucune offre ne correspond à ma recherche.'; break;
	case 1:
		$msg = 'Une offre correspond à ma recherche.'; break;
	default:
		$msg = $nb_offres . ' offres correspondent à ma recherche.';
}

$nb_offres_a_afficher = 4; //nombre d'offres affichées pour chaque sous-thématique

//view
require 'view/resultat.tpl.php';