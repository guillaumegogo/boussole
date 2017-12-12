<?php

include('../src/web/bootstrap.php');

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['code_insee']) || !isset($_SESSION['besoin'])) {
	header('Location: index.php');
	exit();
}

//************ gestion de l'étape en cours (le formulaire est en plusieurs pages)
$etape = 1;
if (isset($_GET['etape'])) {
	$etape = $_GET['etape'];
}
if (isset($_POST['etape'])) {
	$etape = $_POST['etape'];
	
	//********* tous les champs saisis sont remontés en session (sauf "etape" qui n'a pas d'intérêt) : age, sexe, nationalite, jesais, situation, etudes, diplome, permis, handicap, temps_plein, experience, secteur, type_emploi, inscription, etc.
	foreach( $_POST as $cle=>$valeur ){
		if($cle!='etape') {
			$_SESSION['critere'][$cle] = $valeur;
		}
	}
}
if ($etape=='fin') {
	unset($_SESSION['recherche_id']); // supprime l'éventuel recherche_id d'une recherche précédente
	header('Location: resultat.php');
	exit();
}

//************ récupération des éléments de la page du formulaire
$t = get_formulaire($_SESSION['besoin'], $etape, $_SESSION['id_territoire']);
$meta = $t[0];
$questions = $t[1];
$reponses = $t[2];
$liste_pages = $t[3];

//************ on en profite pour récolter une donnée utile pour la page resultat (modele/get_liste_offres)
foreach ($questions as $question) {
	$_SESSION['type'][$question['name']] = $question['type'];
}

//view
require 'view/formulaire.tpl.php';