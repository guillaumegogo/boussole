<?php

include('../src/admin/bootstrap.php');
$check = secu_check_login(DROIT_THEME);
$perimetre_lecture = $check['lecture'];

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['admin']['perimetre'] = $_POST['choix_territoire'];
}
if ($perimetre_lecture <= PERIMETRE_ZONE && !$_SESSION['admin']['perimetre']) {
	$_SESSION['admin']['perimetre'] = secu_get_territoire_id();
}

//******** liste
//$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = (is_numeric($_SESSION['admin']['perimetre'])) ? $_SESSION['admin']['perimetre'] : null;
$themes = get_themes_by_territoire($territoire_id, "pere");

//view
require 'view/theme_liste.tpl.php';