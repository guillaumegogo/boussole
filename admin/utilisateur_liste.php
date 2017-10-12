<?php

include('../src/admin/bootstrap.php');
$perimetre_lecture = secu_check_login(DROIT_UTILISATEUR);

//********* territoire sélectionné
if (isset($_POST['choix_territoire'])) {
	$_SESSION['perimetre'] = $_POST['choix_territoire'];
}
if ($perimetre_lecture <= PERIMETRE_ZONE && $_SESSION['perimetre']!='PRO' ) { 
	$_SESSION['perimetre'] = secu_get_territoire_id();
}

//********* affichage liste résultats 
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == 'non') ? 0 : 1;
$territoire_id = (is_numeric($_SESSION['perimetre'])) ? $_SESSION['perimetre'] : null;

if ($perimetre_lecture == PERIMETRE_PRO || $_SESSION['perimetre']=='PRO') {
	$users = get_liste_users($flag_actif, '', secu_get_user_pro_id()); 
} else if ($perimetre_lecture >= PERIMETRE_ZONE) { 
	$users = get_liste_users($flag_actif, $territoire_id);
}

//view
require 'view/utilisateur_liste.tpl.php';