<?php

include('../src/admin/bootstrap.php');
$perimetre_lecture = secu_check_login(DROIT_DEMANDE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['perimetre'] = $_POST['choix_territoire'];
}
if ($perimetre_lecture <= PERIMETRE_ZONE && !$_SESSION['perimetre']) {
	$_SESSION['perimetre'] = secu_get_territoire_id();
}

//******** liste de demandes
$flag_traite = (isset($_GET['etat']) && $_GET['etat'] == 'traite') ? 1 : 0;
$territoire_id = (is_numeric($_SESSION['perimetre'])) ? $_SESSION['perimetre'] : null;

if ($perimetre_lecture == PERIMETRE_PRO || $_SESSION['perimetre']=='PRO') {
	$demandes = get_liste_demandes($flag_traite, '', secu_get_user_pro_id());
} else if ($perimetre_lecture >= PERIMETRE_ZONE) {
	$demandes = get_liste_demandes($flag_traite, $territoire_id);
}

//view
require 'view/demande_liste.tpl.php';