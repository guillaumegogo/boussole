<?php

include('../src/web/bootstrap.php');

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['web']['ville_habitee']) || !isset($_SESSION['web']['besoin'])) {
	header('Location: index.php');
	exit();
}

//********* on va chercher les offres et les sous-thèmes
$t = get_offres_demande($_SESSION['web']['critere'], $_SESSION['web']['type'], $_SESSION['web']['besoin'], $_SESSION['web']['code_insee']);
$sous_themes = $t[0];
$offres = $t[1];
//si nouvelle recherche, on génère un id de recherche pour la traçabilité
if (!isset($_SESSION['web']['recherche_id']) && isset($t[2]) && (int)$t[2]>0 ) $_SESSION['web']['recherche_id'] = $t[2]; 

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