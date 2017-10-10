<?php

include('../src/admin/bootstrap.php');
$perimetre_lecture = secu_check_login(DROIT_CRITERE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['perimetre'] = $_POST['choix_territoire'];
}
if ($perimetre_lecture <= PERIMETRE_ZONE && !$_SESSION['perimetre']) {
	$_SESSION['perimetre'] = secu_get_territoire_id();
}

//******** liste
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = (is_numeric($_SESSION['perimetre'])) ? $_SESSION['perimetre'] : null;
$formulaires = get_liste_formulaires($flag_actif, $territoire_id);

//view
require 'view/formulaire_liste.tpl.php';