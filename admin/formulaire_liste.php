<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_CRITERE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	secu_set_territoire_id($_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page des formulaires actifs ou inactifs ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

//******** liste de demandes
$territoire_id = secu_get_territoire_id();
$t = get_liste_formulaires($flag_actif, $territoire_id);
$formulaires = $t[0];
$pages = $t[1];

//view
require 'view/formulaire_liste.tpl.php';