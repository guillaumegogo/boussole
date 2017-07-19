<?php
//********* remplacement des caractères effacés du fichier de l'INSEE
function format_insee($saisie){
	$tab_aremplacer = array("-", "_", "  ", "à", "é", "è", "ï", "ö", "ü", "saint ", "Saint ");
	$tab_rempace = array(" ", " ", " ", "a", "e", "e", "i", "o", "st ", "st ");
	return str_replace($tab_aremplacer, $tab_rempace, $saisie);
}

//********* fonction de présentation (un peu crado) des critères du jeune
function liste_criteres($separateur = ","){
	$txt_criteres=null;
	$tab_criteres_a_afficher = array("ville_habitee", "besoin", "age", "sexe", "nationalite", "jesais", "situation", "etudes", "diplome", "permis", "handicap", "type_emploi", "temps_plein", "secteur", "experience", "inscription");
	
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

//******** présentation des questions du formulaire
function ouverture_ligne($ele){ //affichage ligne préalable si le type le demande
	$t='';
	switch ($ele['type']) {
		case 'radio':
		case 'checkbox':
			$t = '<div style="display:inline-table;">';
			break;
		case 'select':
			$t = '<select name="'.$ele['name'].'">'; 
			break;
		case 'multiple':
			$t = '<select multiple name="'.$ele['name'].'" size="'.$ele['tai'].'">';
			break;
	}
	return $t;
}

function affiche_valeur($ele, $type){ //affichage valeur
	$t='';
	switch ($type) {
		case 'radio':
			$t = '<input type="radio" name="'.$ele['name'].'" value="'.$ele['val'].'" ';
			if (isset($_SESSION[$ele['name']])){ if ($_SESSION[$ele['name']]==$ele['val']) $t .= ' checked '; } else if ($ele['def']==1) $t .= ' checked ';
			$t .= '> '.$ele['lib']."\n";
			break;
		case 'checkbox':
			$t = '<input type="checkbox" name="'.$ele['name'].'" value="'.$ele['val'].'" ';
			if (isset($_SESSION[$ele['name']])){ if (in_array($ele['val'], $_SESSION[$ele['name']])) $t .= ' checked '; } else if ($ele['def']==1) $t .= ' checked ';
			$t .= '> '.$ele['lib'].'</br>'."\n";
			break;
		case 'select':
		case 'multiple':
			$t = '<option value="'.$ele['val'].'" ';
			if (isset($_SESSION[$ele['name']])){ if ($_SESSION[$ele['name']]==$ele['val']) $t .= ' selected '; } else if ($ele['def']==1) $t .= ' selected ';
			$t .= '> '.$ele['lib'].'</option>'."\n";
			break;
	}
	return $t;
}

function cloture_ligne($type){
	$t='';
	switch ($type) {
		case 'radio':
		case 'checkbox':
			$t = '</div>'.$t;
			break;
		case 'select':
		case 'multiple':
			$t = '</select>'.$t;
			break;
	}
	return $t;
}