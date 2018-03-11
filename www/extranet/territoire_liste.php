<?php

include('../src/admin/bootstrap.php');
$check = secu_check_login(DROIT_TERRITOIRE);
$perimetre_lecture = $check['lecture'];
$check_ajout = ($check['ecriture']>= PERIMETRE_ZONE) ? true:false; // bouton ajout ?

//********* territoire sélectionné
$_SESSION['admin']['perimetre'] = null;
if ($perimetre_lecture <= PERIMETRE_ZONE) {
	$_SESSION['admin']['perimetre'] = secu_get_territoire_id();
}
	
//********* affichage liste résultats 
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = (is_numeric($_SESSION['admin']['perimetre'])) ? $_SESSION['admin']['perimetre'] : null;

$territoires = get_territoires($territoire_id, $flag_actif); 

//view
require 'view/territoire_liste.tpl.php';
