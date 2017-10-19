<?php
//********* remplacement des caractères effacés du fichier de l'INSEE
function format_insee($saisie){
	$tab_aremplacer = array("-", "_", "  ", "à", "é", "è", "ï", "ö", "ü", "saint ", "Saint ");
	$tab_rempace = array(" ", " ", " ", "a", "e", "e", "i", "o", "st ", "st ");
	return str_replace($tab_aremplacer, $tab_rempace, $saisie);
}

function pretty_json_print($json, $longueur=null){

	$table = json_decode($json, true);
	
	if(is_array($table)){
		$return = "<ul>";	
		foreach ($table as $key=>$value){
			if($value){
				$key_txt = str_replace("_", " ", xssafe($key));
				if(!is_array($value)){
					$return .= "<li>".$key_txt." : ". xssafe($value) ."</li>";
				}else{
					$value_txt = xssafe(implode(", ", $value));
					if(isset($longueur) && $longueur>0 && strlen($value_txt)>$longueur){
						$value_txt = "<abbr title=\"".$value_txt."\">".substr($value_txt,0,$longueur)."…</abbr>";
					}				
					$return .= "<li>".$key_txt." : ". $value_txt ."</li>";
				}
			}
		};
		$return .= "</ul>";
	}else{
		$return = $json;
	}
	echo $return;
}

/*
$timestamp_debut = microtime(true);
...
$timestamp_fin = microtime(true);
$difference_ms = $timestamp_fin - $timestamp_debut;
echo 'Exécution du script : ' . substr($difference_ms,0,6) . ' secondes.';
*/