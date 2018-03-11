<?php
include('../src/admin/bootstrap.php');
if (!secu_check_role(ROLE_ADMIN)) {
	header('Location: accueil.php');
	exit();
}

$msg = null;
$row = null;

if (isset($_POST['submit'])) {

	//********** ajout de la donnée
	$last_id = (int)$_POST['maj_id_reference'];
	if ($_POST['maj_id_reference']) {
		$updated=update_parametre($last_id, $_POST['liste_reference'], $_POST['libelle_reference']);
	} else {
		$created=create_parametre($_POST['liste_reference'], $_POST['libelle_reference']);
		$last_id = mysqli_insert_id($conn);
	}

	if ((isset($updated)&&$updated==false)||(isset($created)&&$created==false)) {
		$msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème persiste.";
	} else {
		$msg = 'Modification bien enregistrée.';
	}
}

$id_ref = (isset($last_id)) ? $last_id : null;
if (isset($_GET['id'])) {
	$id_ref = (int)$_GET['id'];
}
if (isset($id_ref)) {
	$row = get_parametre_by_id($id_ref); 
}

//view
require 'view/reference_detail.tpl.php';