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

//******** présentation des questions du formulaire
function affiche_formulaire($sujet, $tab){
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
		foreach($tab[$sujet] as $key => $value){
			if ($key=="type") continue;
			$t .= "<option value=\"".$key."\" ";
			if (isset($_SESSION[$sujet]) && $_SESSION[$sujet]==$key) $t .= " selected ";
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
			else if ($i==$tab[$sujet]["defaut"]) $t .= " selected ";
			$t .= ">".$i."</option>\n";
		}
		$t .= "</select> ans";
	}
	return $t;
}