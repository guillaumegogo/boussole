<?php
//********* remplacement des caractères effacés du fichier de l'INSEE
function format_insee($saisie){
	$tab_aremplacer = array("-", "_", "  ", "à", "é", "è", "ï", "ö", "ü", "saint ", "Saint ");
	$tab_rempace = array(" ", " ", " ", "a", "e", "e", "i", "o", "st ", "st ");
	return str_replace($tab_aremplacer, $tab_rempace, $saisie);
}

//************** liste villes (à voir si utile - pour l'instant remplacé par des fichiers texte pour de meilleures perfs)
function liste_villes($conn,$format){
	$sql = "SELECT DISTINCT nom_ville, code_postal FROM `bsl__ville` ORDER BY nom_ville";
	$result = mysqli_query($conn, $sql);
	$liste = null;
	while($row = mysqli_fetch_assoc($result)) {
		if ($format=="jq") {
			$liste .= "\"".$row['nom_ville']." ".$row['code_postal']."\",";
		}else if ($format=="select") {
			$liste .= "<option value=\"".$row['nom_ville']." ".$row['code_postal']. "\">".$row['nom_ville']." ".$row['code_postal']. "</option>";
		}
	}
	return $liste;
}

/*
$timestamp_debut = microtime(true);
...
$timestamp_fin = microtime(true);
$difference_ms = $timestamp_fin - $timestamp_debut;
echo 'Exécution du script : ' . substr($difference_ms,0,6) . ' secondes.';
*/