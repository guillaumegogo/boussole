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

//********* tous les champs saisis sont remontés en session (sauf "etape" qui n'a pas d'intérêt) : age, sexe, nationalite, jesais, situation, etudes, diplome, permis, handicap, temps_plein, experience, secteur, type_emploi, inscription, etc.
foreach( $_POST as $cle=>$valeur ){
	if($cle!='etape') { 
		$_SESSION['critere'][$cle] = $valeur; 
	}
}

//************ gestion de l'étape en cours (le formulaire est en plusieurs pages)
$etape = 1;
if (isset($_POST['etape'])) { 
	$etape = $_POST['etape'];
}
if ($etape=='fin') {
	header('Location: resultat.php');
}

//************ récupération des éléments de la page du formulaire
$t = get_formulaire($etape);
$meta = $t[0];
$questions = $t[1];
$reponses = $t[2];

//************ on en profite pour récolter une donnée utile pour la page resultat (modele/get_liste_offres)
foreach ($questions as $question) { 
	$_SESSION['type'][$question['name']] = $question['type']; 
}

//view
require 'view/formulaire.tpl.php';