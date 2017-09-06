<?php
$timestamp_debut = microtime(true);

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_TERRITOIRE);

//********* variables
$msg = "";

//********* territoire sélectionné
if (isset($_POST['choix_territoire'])) {
	$_SESSION['territoire_id'] = securite_bdd($conn, $_POST['choix_territoire']);
}

//********** mise à jour/création du territoire
if (isset($_POST["submit_meta"])) {
	if ($_POST["maj_id_territoire"]) {
		$updated=update_territoire((int)$_POST['maj_id_territoire'], $_POST['libelle_territoire']);
		$_SESSION['territoire_id'] = $_POST['maj_id_territoire'];
	} else {
		$created=create_territoire($_POST['libelle_territoire']);
		$_SESSION['territoire_id'] = mysqli_insert_id($conn);
	}
}

//********** mise à jour des villes
if (isset($_POST["submit_villes"])) {
	$liste_villes=null;
	if(isset($_POST['list2'])) $liste_villes=$_POST['list2'];
	
	$result2 = update_villes_territoire((int)$_POST['maj_id_territoire'], $liste_villes);

	if ($result2) {
		$msg = 'Modification bien enregistrée.';
	} else {
		$msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème perdure.";
	}
}

//si territoire sélectionné -> on va chercher les listes de villes du territoire
if (isset($_SESSION['territoire_id']) && $_SESSION['territoire_id']) {
	$liste_villes_territoire = '';
	$villes = get_villes_by_territoire((int)$_SESSION['territoire_id']);
	if (isset($villes)){
		foreach($villes as $row){
			$liste_villes_territoire .= '<option value="' . $row['code_insee'] . '">' . $row['nom_ville'] . ' ' . $row['code_postal'] . '</option>';
		}
	}
}

include('../src/admin/select_territoires.inc.php');

//view
require 'view/territoire.tpl.php';