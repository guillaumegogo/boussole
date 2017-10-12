<?php

include('../src/admin/bootstrap.php');
$perimetre_lecture = secu_check_login(DROIT_TERRITOIRE);

//********* territoire sélectionné
//$_SESSION['perimetre'] = null;
if ($perimetre_lecture <= PERIMETRE_ZONE) {
	$_SESSION['perimetre'] = secu_get_territoire_id();
}
	
//********* affichage liste résultats 
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = (is_numeric($_SESSION['perimetre'])) ? $_SESSION['perimetre'] : null;

$territoires = get_territoires($territoire_id, $flag_actif); 

//view
require 'view/territoire_liste.tpl.php';