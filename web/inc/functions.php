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
function affiche_valeur($ele){ //affichage valeur
	$t='';
	switch ($ele['type']) {
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
function cloture_ligne_precedente($type){
	$t='</div></div>';
	switch ($type) {
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

function affiche_formulaire($sujet, $tab){  //à supprimer
	$t ="";
	if($tab[$sujet]["type"]=="radio"){
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type") continue; //pour passer le "type"
			$t .= "<input type=\"radio\" name=\"".$sujet."\" value=\"".$key."\" ";
			if (isset($_SESSION[$sujet]) && $_SESSION[$sujet]==$key) $t .= " checked ";
			$t .= "> ".$value."\n";
		}
	}else if($tab[$sujet]["type"]=="checkbox"){
		$t ="<div style=\"display:inline-table;\">";
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type") continue;
			$t .= "<input type=\"checkbox\" name=\"".$sujet."[]\" value=\"".$key."\" ";
			if (isset($_SESSION[$sujet]) && in_array($key, $_SESSION[$sujet])) $t .= " checked ";
			$t .= "> ".$value."</br>\n";
		}
	}else if($tab[$sujet]["type"]=="select"){
		$t ="<select name=\"".$sujet."\">";
		$defaut = "";
		if(isset($tab[$sujet]["defaut"])) $defaut=$tab[$sujet]["defaut"];
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type") continue;
			if ($key=="defaut") continue;
			$t .= "<option value=\"".$key."\" ";
			if (isset($_SESSION[$sujet])){
				if($_SESSION[$sujet]==$key) $t .= " selected ";
			}else if ($defaut==$key) $t .= " selected ";
			$t .= "> ".$value."</option>\n";
		}
		$t .="</select>";
	}else if($tab[$sujet]["type"]=="multiple"){
		$t ="<select name=\"".$sujet."[]\" size=\"".$tab[$sujet]["size"]."\">";
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type" || $key=="size") continue;
			$t .= "<option value=\"".$key."\" ";
			if (isset($_SESSION[$sujet]) && $_SESSION[$sujet]==$key) $t .= " selected ";
			$t .= "> ".$value."</option>\n";
		}
		$t .="</select>";
	}else if($tab[$sujet]["type"]=="age"){
		$t ="<select name=\"".$sujet."\" class=\"age\">";
		for ($i = $tab[$sujet]["min"]; $i <= $tab[$sujet]["max"]; $i++) {
			$t .= "<option value=\"".$i."\"";
			if (isset($_SESSION['age']) && $i==$_SESSION['age']) $t .= " selected ";
			else if ($tab[$sujet]["defaut"]==$i) $t .= " selected ";
			$t .= ">".$i."</option>\n";
		}
		$t .= "</select> ans";
	}
	return $t;
}