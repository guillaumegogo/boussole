<?php

include('../src/web/bootstrap.php');

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['ville_habitee']) || !isset($_SESSION['besoin'])) {
	header('Location: index.php');
	exit();
}

//********* si demande de contact
if (isset($_POST['coordonnees']) && isset($_POST['id_offre'])) {
	
	$_SESSION['coordonnees']=$_POST['coordonnees'];
	
	//*********** création de la demande
	$recherche_associee = (isset($_SESSION['recherche_id']) && (int)$_SESSION['recherche_id']>0 ) ? $_SESSION['recherche_id'] : null;
	$demande = create_demande($_POST['id_offre'], $_POST['coordonnees'], $recherche_associee);
	$id_demande = $demande[0];
	$token = $demande[1];

	//*********** envoi des mails
	if ($id_demande) {
		$row = get_offre($_POST['id_offre']);
		$resultat = envoi_mails_demande( $row['courriel_offre'], $row['nom_offre'], $_POST['coordonnees'], $token);

		if ($resultat){
			$msg_depot ="<img src=\"img/ok_circle.png\" width=\"24px\" style=\"margin-bottom:-0.3em;\"> Ta demande de contact pour l'offre «&nbsp;".$nom_offre."&nbsp;» a bien été enregistrée et un courriel contenant ta recherche à été transmis à l'organisme proposant l'offre de service."
		}else{
			$msg_depot = "<img src=\"img/exclamation.png\" width=\"24px\"> Ta demande de contact pour l'offre «&nbsp;".$row['nom_offre']."&nbsp;» a bien été enregistrée mais aucun courriel complémentaire n'a été envoyé. Tu peux contacter directement le professionnel.";
		}
	} else {
		$msg_depot = "<img src=\"img/exclamation.png\" width=\"24px\"> L'application a rencontré un problème. Ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.";
	}
}

//********* on va chercher les offres et les sous-thèmes
$t = get_offres_demande($_SESSION['critere'], $_SESSION['type'], $_SESSION['besoin'], $_SESSION['code_insee']);
$sous_themes = $t[0];
$offres = $t[1];
//si nouvelle recherche, on génère un id de recherche pour la traçabilité
if (!isset($_SESSION['recherche_id']) && isset($t[2]) && (int)$t[2]>0 ) $_SESSION['recherche_id'] = $t[2]; 

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