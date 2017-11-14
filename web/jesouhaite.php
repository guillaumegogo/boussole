<?php

include('../src/web/bootstrap.php');

//********* variables utilisées dans ce fichier
$nb_villes = 0;
$themes = array();
$flag_theme = 0;
$erreur = 0;

if (isset($_POST['ville_selectionnee'])) {
    //********* on efface les valeurs de session pour une recherche propre
    session_unset();
    $_SESSION['recherche'] = $_POST['ville_selectionnee'];
}

//********* l'utilisateur a relancé le formulaire
if(isset($_SESSION['recherche'])) {
	//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville
	$row = get_ville($_SESSION['recherche']);
	$nb_villes = count($row);

	if ($nb_villes == 0) {
        $erreur = 1;
	} else if ($nb_villes > 1) {
		$erreur = 2;
	} else {
		$_SESSION['code_insee'] = $row[0]['code_insee']; // sert à la requête
		$_SESSION['ville_habitee'] = $row[0]['nom_ville'];
		$_SESSION['code_postal'] = $row[0]['codes_postaux'];
	}

	//********* récupération des thèmes disponibles pour le code insee indiqué
	if (isset($_SESSION['code_insee'])) {
	    $themes = get_themes_by_ville($_SESSION['code_insee']);
    }

	//********* a-t-on au moins un thème actif ?
	foreach ($themes as $theme) {
		$flag_theme += ($theme['actif']*$theme['nb']);
	}

	if(!$erreur && (!$flag_theme || !count($themes))) {
	    $erreur = 1;
    }
}
//********* l'utilisateur a choisi un thème -> il est envoyé vers le formulaire
if (isset($_POST['besoin'])) {
	$_SESSION['besoin'] = $_POST['besoin'];
	header('Location: formulaire.php');
	exit();
}

if ($erreur) {
    $_SESSION['erreur'] = $erreur;
    header('Location: index.php');
    exit();
}

//view
require 'view/jesouhaite.tpl.php';