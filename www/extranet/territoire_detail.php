<?php
$timestamp_debut = microtime(true);

include('../src/admin/bootstrap.php');
$droit_ecriture = (isset($_GET['id'])) ? secu_check_level(DROIT_TERRITOIRE, $_GET['id']) : true;

$msg = '';

//********** mise à jour/création du territoire
if (isset($_POST['submit_meta'])) {
	if ($_POST['maj_id_territoire']) {
		$updated=update_territoire((int)$_POST['maj_id_territoire'], $_POST['libelle_territoire']);
		$last_id = $_POST['maj_id_territoire'];
	} else {
		$created=create_territoire($_POST['libelle_territoire']);
		$last_id = mysqli_insert_id($conn);
	}
}

//********** mise à jour des villes
if (isset($_POST["submit_villes"])) {
	$liste_villes=null;
	if(isset($_POST['list2'])) $liste_villes=$_POST['list2'];
	
	$result2 = update_villes_territoire((int)$_POST['maj_id_territoire'], $liste_villes);

	if (isset($result2)) {
		$msg = 'Modification bien enregistrée.';
	} else {
		$msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème perdure.";
	}
}

//********* territoire sélectionné
$territoire = [];
$id_territoire = (isset($last_id)) ? $last_id : null;
if (isset($_GET['id'])) {
	$id_territoire = $_GET['id'];
}
if (isset($id_territoire)) {
	$territoire = get_territoires((int)$id_territoire)[0]; 
	$villes = get_villes_by_territoire((int)$id_territoire);
}

//view
require 'view/territoire_detail.tpl.php';