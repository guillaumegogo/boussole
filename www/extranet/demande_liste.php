<?php

include('../src/admin/bootstrap.php');
$check = secu_check_login(DROIT_DEMANDE);
$perimetre_lecture = $check['lecture'];

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['admin']['perimetre'] = $_POST['choix_territoire'];
}
if ($perimetre_lecture <= PERIMETRE_ZONE && $_SESSION['admin']['perimetre']!='PRO' ) { 
	$_SESSION['admin']['perimetre'] = secu_get_territoire_id();
}

//******** liste de demandes
$flag_traite = (isset($_GET['etat']) && $_GET['etat'] == 'traite') ? 1 : 0;
$territoire_id = (is_numeric($_SESSION['admin']['perimetre'])) ? $_SESSION['admin']['perimetre'] : null;

if ($perimetre_lecture == PERIMETRE_PRO || $_SESSION['admin']['perimetre']=='PRO') {
	$demandes = get_liste_demandes($flag_traite, '', secu_get_user_pro_id());
} else if ($perimetre_lecture >= PERIMETRE_ZONE) {
	$demandes = get_liste_demandes($flag_traite, $territoire_id);
}

//view
require 'view/demande_liste.tpl.php';