<?php
session_start();

include('inc/modele.php');
include('inc/functions.php');
include('inc/variables.php');

//********* censé permettre de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire');

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['ville_habitee']) || !isset($_SESSION['besoin'])) {
	header('Location: index.php');
}

//********* on va chercher les offres et les sous-thèmes
$t = get_liste_offres();
$sous_themes = $t[0];
$offres = $t[1];

//********* contenu du template 
$aucune_offre='';
$nb_offres=count($offres);
if ($nb_offres) {
	$aucune_offre = "<a href=\"#\">Aucune offre ne m'intéresse</a>";
	if ($nb_offres>1) {
		$msg=$nb_offres." offres correspondent à ta recherche.";
	}else if ($nb_offres==1) {
		$msg="Une offre correspond à ta recherche.";
	}
}else{
	$msg="Aucune offre ne correspond à ta recherche.";
}

//view
require 'view/resultat.tpl.php';