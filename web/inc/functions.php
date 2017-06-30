<?php
//********* remplacement des caractères effacés du fichier de l'INSEE
function format_insee($saisie){
	$tab_aremplacer = array("-", "_", "  ", "à", "é", "è", "ï", "ö", "ü", "saint ", "Saint ");
	$tab_rempace = array(" ", " ", " ", "a", "e", "e", "i", "o", "st ", "st ");
	return str_replace($tab_aremplacer, $tab_rempace, $saisie);
}

//********* fonction de présentation (un peu crado) des critères du jeune
function liste_criteres($separateur){
	$txt_criteres=null;
	$tab_criteres_a_afficher = array("ville_habitee", "besoin", "age", "nationalite", "jesais", "situation", "etudes", "diplome", "permis", "handicap", "type_emploi", "temps_plein", "secteur", "experience", "inscription");
	
	foreach($_SESSION as $index=>$valeur){
		if(in_array($index, $tab_criteres_a_afficher)){
			$txt = str_replace("_", " ", $index)." : ";
			if(is_array($valeur)){
				foreach($valeur as $index2=>$valeur2)
					$txt .= $valeur2." /";
				$txt = substr($txt, 0, -1);
			}else{
				$txt .= $valeur;
			}
			$txt_criteres .= $txt.$separateur;
		}
	}
	return $txt_criteres;
}
?>