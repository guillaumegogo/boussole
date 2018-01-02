<?php

include('../src/admin/bootstrap.php');
$check = secu_check_login(DROIT_OFFRE);
$perimetre_lecture = $check['lecture'];

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['admin']['perimetre'] = $_POST['choix_territoire'];
}
if ($perimetre_lecture <= PERIMETRE_ZONE && $_SESSION['admin']['perimetre']!='PRO' ) { 
	$_SESSION['admin']['perimetre'] = secu_get_territoire_id();
}

//******** liste des offres de service
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = (is_numeric($_SESSION['admin']['perimetre'])) ? $_SESSION['admin']['perimetre'] : null;

if ($perimetre_lecture == PERIMETRE_PRO || $_SESSION['admin']['perimetre']=='PRO') {
	$offres = get_liste_offres($flag_actif, '', secu_get_user_pro_id());
} else if ($perimetre_lecture >= PERIMETRE_ZONE) {
	$offres = get_liste_offres($flag_actif, $territoire_id);
}

//view
require 'view/offre_liste.tpl.php';