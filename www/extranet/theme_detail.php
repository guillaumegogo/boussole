<?php
include('../src/admin/bootstrap.php');
$droit_ecriture = (isset($_GET['id'])) ? secu_check_level(DROIT_THEME, $_GET['id']) : true;

$check = secu_check_login(DROIT_THEME);
$perimetre = $check['lecture'];

//********* variable
$id_theme_choisi = null;
$flag_duplicate = false;
$msg = '';

if (isset($_POST["enregistrer"])) {
	
	if (DEBUG) { 
		echo "<!--<pre>"; echo ' '.$droit_ecriture.' '; print_r($_POST); echo "</pre>-->";
	}
	$created = $updated = $updated_st = null;
	
	//********** mise à jour thème/sous themes
	if (isset($_POST["maj_id_theme"])) {
		$id_theme_choisi = (int)$_POST['maj_id_theme'];
		$updated=update_theme($id_theme_choisi, $_POST['libelle_theme'], $_POST['actif']);
		
	//********** création du theme //TODO
	} else {
		$retour = create_theme($_POST['theme'], $_POST['territoire'], $_POST['libelle_theme'], $_POST['actif']);
		$created = $retour[0];
		if(!is_null($retour[1])) $msg = $retour[1];
		if ($created) {
			$id_theme_choisi = mysqli_insert_id($conn);
		}
	}
	
	if ($id_theme_choisi && isset($_POST['sthemes'])) {
		$updated_st=update_sous_themes($_POST['sthemes'], $id_theme_choisi, $_POST['theme']);
	}
	
	if ($created+$updated+$updated_st == true) 
		$msg = "Modification bien enregistrée.";
	else if ($created+$updated+$updated_st == false) 
		$msg = $msg ? $msg : "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème persiste.";
}

if (isset($_GET['act'])) {
	//action duplication de thème
	if($_GET['act']=='dup'){
		$flag_duplicate = true;
		$droit_ecriture = true;
	}
}

if (isset($_GET['id']) && !$id_theme_choisi) {
	$id_theme_choisi = $_GET['id'];
}

if ($id_theme_choisi) {
	$themes = get_theme_et_sous_themes_by_id((int)$id_theme_choisi);
	foreach($themes as $row){
		if(is_null($row['id_theme_pere'])){
			$theme = $row;
		}else{
			$sous_themes[] = $row;
		}
	}
}

$liste_themes = get_liste_parametres('theme');
$duplicated_theme = ($flag_duplicate)?$theme['libelle_theme_court']:null;

if (secu_check_role(ROLE_ANIMATEUR)) {
	$territoires = get_territoires($_SESSION['admin']['territoire_id'],1,$duplicated_theme);
} else {
	$territoires = get_territoires(null,1,$duplicated_theme);
}

//view
require 'view/theme_detail.tpl.php';