<?php
session_start();

include('inc/modele.php');
include('inc/functions.php');
include('inc/variables.php');

//********* censé permettre de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire');

//********* variables utilisées dans ce fichier
$nb_villes = 0;
$themes = array();

//********* l'utilisateur a relancé le formulaire
if (isset($_POST['ville_selectionnee'])) {
	//********* on efface les valeurs de session pour une recherche propre
	session_unset();  

	//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville 
	$row = get_ville($_POST['ville_selectionnee']);
	$nb_villes=count($row);
	
	if($nb_villes==0){
		$message = "Nous ne trouvons pas de ville correspondante. Recommence s'il te plait.";
	
	}else if($nb_villes>1){
		$message = "Plusieurs villes correspondent à ta saisie. Recommence s'il te plait.";
	
	}else {
		$_SESSION['ville_habitee'] = $row[0]['nom_ville'];
		$_SESSION['code_insee'] = $row[0]['code_insee'];
		$_SESSION['code_postal'] = $row[0]['codes_postaux'];
	}

	//********* récupération des thèmes disponibles en fonction des données en session 
	$themes = get_themes();
}

//view
require 'view/index.tpl.php';
