<?php
$timestamp_debut = microtime(true);

include('../src/admin/bootstrap.php');
$droit_ecriture = (isset($_GET['id'])) ? secu_check_level(DROIT_TERRITOIRE, $_GET['id']) : true;
$msg = '';

if (isset($_POST['restaurer']) && isset($_POST['maj_id_territoire']) && $_POST['maj_id_territoire']) {

	$restored = archive('territoire', (int)$_POST['maj_id_territoire'], 1);
 
} elseif (isset($_POST['archiver']) && isset($_POST['maj_id_territoire']) && $_POST['maj_id_territoire']) {

	$archived = archive('territoire', (int)$_POST['maj_id_territoire']);
 
} elseif (isset($_POST['submit'])) {

	//********** mise à jour/création du territoire
	$last_id = (int)$_POST['maj_id_territoire'];
	if ($_POST['maj_id_territoire']) {
		$updated=update_territoire($last_id, $_POST['libelle_territoire'], $_POST['desc']);
	} else {
		$created=create_territoire($_POST['libelle_territoire'], $_POST['desc']);
		$last_id = mysqli_insert_id($conn);
	}

	//********** mise à jour des villes
	if(isset($_POST['list2'])){
		$result2 = update_villes_territoire($last_id, $_POST['list2']);
	}

	if ((isset($updated)&&$updated==false)||(isset($created)&&$created==false)||(isset($result2)&&$result2==false)) {
		$msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème persiste.";
	} else {
		$msg = 'Modification bien enregistrée.';
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