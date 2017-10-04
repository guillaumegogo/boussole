<?php

include('../src/admin/bootstrap.php');
$perimetre = secu_check_login(DROIT_THEME);

//********* variable
$msg = "";
$libelle_theme_choisi = "";

$id_theme_choisi = 0;
if (isset($_POST['choix_theme'])) 
	$id_theme_choisi = $_POST['choix_theme'];

if (isset($_POST["maj_id_theme"])) {
	$id_theme_choisi = $_POST['maj_id_theme'];
	$result = false;

	//********** mise à jour du theme
	if (isset($_POST["submit_theme"])) {
		$updated=update_theme((int)$_POST['maj_id_theme'], $_POST['libelle_theme'], $_POST['actif']);
		if (isset($updated)) $msg = "Modification bien enregistrée.";
	}
	//********** mise à jour des sous themes
	if (isset($_POST["submit_liste_sous_themes"])) {
		$updated_st=update_sous_themes((int)$_POST['maj_id_theme'], $_POST['sthemes']);
		if (isset($updated_st)) $msg = "Modification bien enregistrée.";
	}
	
	//********** création du theme
	if (isset($_POST["submit_nouveau_sous_theme"])) {
		$created=create_sous_theme($_POST["libelle_nouveau_sous_theme"], (int)$_POST['maj_id_theme']);
		if ($created) $msg = "Modification bien enregistrée.";
	}

	if (!$msg) {
		$msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème perdure.";
	}
}

//********* liste déroulante des thèmes (en haut à droite)
$select_theme = '';
$themes = get_liste_themes();
foreach($themes as $rows) {
	$select_theme .= '<option value="' . $rows['id_theme'] . '" ';
	if ($rows['id_theme'] == $id_theme_choisi) {
		$select_theme .= 'selected';
		$libelle_theme_choisi = $rows['libelle_theme'];
		$libelle_theme_court_choisi = $rows['libelle_theme_court'];
		$actif_theme_choisi = $rows['actif_theme'];
	}
	$select_theme .= '>' . $rows['libelle_theme_court'] . '</option>';
}

//si theme selectionné
$tableau = "";
$i = 0;
if ($id_theme_choisi) {
	$sous_themes = get_liste_sous_themes((int)$id_theme_choisi); // $result_st
}/* else {
	$msg = 'Merci de sélectionner un thème dans la liste.';
}*/

//view
require 'view/theme.tpl.php';