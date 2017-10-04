<?php

include('../src/admin/bootstrap.php');
$perimetre = secu_check_login(DROIT_PROFESSIONNEL);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['territoire_choisi'] = $_POST['choix_territoire'];
}

//********* affichage liste résultats 
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = secu_get_territoire_id();
$pros = get_liste_pros($flag_actif, $territoire_id); //tous les professionnel actifs, du territoire si choisi

//view
require 'view/professionnel_liste.tpl.php';