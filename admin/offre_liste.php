<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_OFFRE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['territoire_choisi'] = $_POST['choix_territoire'];
}

//******** liste des offres de service
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = secu_get_territoire_id();
$user_pro_id = secu_get_user_pro_id();
$offres = get_liste_offres($flag_actif, $territoire_id, $user_pro_id);

//view
require 'view/offre_liste.tpl.php';