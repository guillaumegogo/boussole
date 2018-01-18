<?php

include('../src/admin/bootstrap.php');

if (isset($_GET["id"]) && !empty($_GET['id']) || !isset($_GET['hash'])) {
	$droit_ecriture = secu_check_level(DROIT_DEMANDE, $_GET['id']);
}

//********* variables
$msg = "";
if (isset($_POST["id_traite"]) && !empty($_POST["id_traite"]) && isset($_POST['commentaire'])) {
	$updated = update_demande((int)$_POST["id_traite"], $_POST["commentaire"]);
	if ($updated) {
		$msg = '<div class="soustitre">La demande a été mise à jour.</div>';
	}
}

if (isset($_GET["id"]) && !empty($_GET['id'])) {
	$demande = get_demande_by_id((int)$_GET['id']);

}else if(isset($_GET['hash'])){ // cas de l'acces direct à la demande depuis le mail
	$demande = get_demande_by_id($_GET['hash'], "hash");
	
	if ($demande) {
		$droit_ecriture = true;	
	} else {
		header('Location:index.php');
	}
}

//view
require 'view/demande_detail.tpl.php';