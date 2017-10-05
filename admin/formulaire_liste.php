<?php

include('../src/admin/bootstrap.php');
$perimetre = secu_check_login(DROIT_CRITERE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	$_SESSION['perimetre'] = $_POST['choix_territoire'];
}

//******** liste
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;
$territoire_id = $_SESSION['perimetre'];
$formulaires = get_liste_formulaires($flag_actif, $territoire_id);

//view
require 'view/formulaire_liste.tpl.php';