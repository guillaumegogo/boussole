<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_MESURE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	secu_set_territoire_id($_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page actives ou désactivées ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

//******** liste des mesures
$mesures = get_liste_mesures($flag_actif);

//view
require 'view/mesure_liste.tpl.php';