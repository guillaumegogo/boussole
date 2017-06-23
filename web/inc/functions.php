<?php
//********* https://openclassrooms.com/courses/securite-php-securiser-les-flux-de-donnees
// fonction à utiliser pour la sécurisation de toutes les variables utilisées dans les requêtes
function securite_bdd($conn, $string){
	
	// On regarde si le type de string est un nombre entier (int)
	if(ctype_digit($string)) {
		$string = intval($string);
	}
	// Pour tous les autres types
	else {
		$string = mysqli_real_escape_string($conn, $string);
		$string = addcslashes($string, '%_');
	}
	
	return $string;
}

//********* remplacement des caractères effacés du fichier de l'INSEE //update : a priori inutile...
function format_insee($saisie){
	$tab_aremplacer = array("-", "_", "  ", "à", "é", "è", "ï", "ö", "ü", "saint ", "Saint ");
	$tab_rempace = array(" ", " ", " ", "a", "e", "e", "i", "o", "st ", "st ");
	return str_replace($tab_aremplacer, $tab_rempace, $saisie);
}
?>