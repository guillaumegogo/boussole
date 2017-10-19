<?php

include('../src/admin/bootstrap.php');
$check = secu_check_login(DROIT_MESURE);
$perimetre = $check['lecture'];

//********* territoire sélectionné
/*if (isset($_POST["choix_territoire"])) {
	$_SESSION['perimetre'] = $_POST['choix_territoire'];
}*/

//******** liste des mesures
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

$t = get_formulaire_mesure();
$questions = $t[1];
$reponses = $t[2];

//******** liste des critères
$criteres = null;
if (isset($_POST['criteres'])) $criteres = $_POST['criteres'];
$mesures = get_liste_mesures($flag_actif, $criteres);

//view
require 'view/mesure_liste.tpl.php';