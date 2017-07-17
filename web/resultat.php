<?php
session_start();

require('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* censé permettre de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire');

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['ville_habitee']) || !isset($_SESSION['besoin'])) {
	header('Location: index.php');
} else {
	$message = "J'habite à <b>".$_SESSION['ville_habitee']."</b> et je souhaite <b>".strtolower ($_SESSION['besoin'])."</b>.";
}

//********* variables utilisées dans ce fichier
$aucune_offre="";
$offres = array();
$offres[] = get_offres();

//********* contenu du template 
$nb_offres=count($offres);
if ($nb_offres) {
	$titre_criteres = "<p onclick='masqueCriteres()'>".$message."<span id=\"fleche_criteres\">&#9661;</span></p>";
	$aucune_offre = "<a href=\"#\">Aucune offre ne m'intéresse</a>";
	if ($nb_offres>1) {
		$msg=$nb_offres." offres correspondent à ta recherche.";
	}else if ($nb_offres==1) {
		$msg="Une offre correspond à ta recherche.";
	}
}else{
	$titre_criteres = "<p>".$message."</p>";
	$msg="Aucune offre ne correspond à ta recherche.";
}

//view
require 'view/resultat.tpl.php';