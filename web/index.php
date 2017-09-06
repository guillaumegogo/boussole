<?php

include('../src/web/bootstrap.php');

//********* variables utilisées dans ce fichier
$nb_villes = 0;
$themes = array();

//********* l'utilisateur a relancé le formulaire
if (isset($_POST['ville_selectionnee'])) {
	//********* on efface les valeurs de session pour une recherche propre
	session_unset();

	//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville
	$row = get_ville($_POST['ville_selectionnee']);
	$nb_villes = count($row);

	if ($nb_villes == 0) {
		$message = "Nous ne trouvons pas de ville correspondante. Recommence s'il te plait.";

	} else if ($nb_villes > 1) {
		$message = "Plusieurs villes correspondent à ta saisie. Recommence s'il te plait.";

	} else {
		$_SESSION['code_insee'] = $row[0]['code_insee']; // sert à la requête
		$_SESSION['ville_habitee'] = $row[0]['nom_ville'];
		$_SESSION['code_postal'] = $row[0]['codes_postaux'];
	}

	//********* récupération des thèmes disponibles en fonction des données en session
	$themes = get_themes();
	
	$flag_theme=0;
	foreach ($themes as $theme) { 
		$flag_theme += $theme['actif'];
	}
}

//********* l'utilisateur a choisi un thème -> il est envoyé vers le formulaire
if (isset($_POST['besoin'])) {
	$_SESSION['besoin'] = $_POST['besoin'];
	header('Location: formulaire.php');
	exit();
}

//view
require 'view/index.tpl.php';