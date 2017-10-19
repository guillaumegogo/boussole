<?php

include('../src/admin/bootstrap.php');
$check = secu_check_login(DROIT_PROFESSIONNEL);
$perimetre_lecture = $check['lecture'];
$check_ajout = ($check['ecriture']>= PERIMETRE_ZONE) ? true:false; // bouton ajout ?

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['perimetre'] = $_POST['choix_territoire'];
}
if ($perimetre_lecture <= PERIMETRE_ZONE && $_SESSION['perimetre']!='PRO' ) { 
	$_SESSION['perimetre'] = secu_get_territoire_id();
}

//********* affichage liste résultats 
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = (is_numeric($_SESSION['perimetre'])) ? $_SESSION['perimetre'] : null;

//a priori il ne peut pas y avoir de perimetre_lecture inférieur à PERIMETRE_ZONE.
if ($perimetre_lecture == PERIMETRE_PRO || $_SESSION['perimetre']=='PRO') {
	$pros = get_liste_pros($flag_actif, '', secu_get_user_pro_id());
} else if ($perimetre_lecture >= PERIMETRE_ZONE) { 
	$pros = get_liste_pros($flag_actif, $territoire_id); 
}

//view
require 'view/professionnel_liste.tpl.php';