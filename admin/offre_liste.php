<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_OFFRE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	secu_set_territoire_id($_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page des offres actives ou désactivées ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

//******** liste des offres de service
$territoire_id = secu_get_territoire_id();
$user_pro_id = secu_get_user_pro_id();
$offres = get_liste_offres($flag_actif, $territoire_id, $user_pro_id);

//view
require 'view/offre_liste.tpl.php';