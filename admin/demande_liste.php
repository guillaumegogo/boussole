<?php

include('../src/admin/bootstrap.php');
$perimetre = secu_check_login(DROIT_DEMANDE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['territoire_choisi'] = $_POST['choix_territoire'];
}

//******** liste de demandes
$flag_traite = (isset($_GET['etat']) && $_GET['etat'] == 'traite') ? 1 : 0;
$territoire_id = secu_get_territoire_id();

if ($perimetre == 1) {
	$demandes = get_liste_demandes($flag_traite, $territoire_id, secu_get_user_pro_id());
} else if ($perimetre > 1) {
	$demandes = get_liste_demandes($flag_traite, $territoire_id);
}

//view
require 'view/demande_liste.tpl.php';