<?php
include('../src/admin/bootstrap.php');
if (!secu_check_role(ROLE_ADMIN)) {
	header('Location: accueil.php');
	exit();
}

$rows = get_liste_parametres(); 

//view
require 'view/reference_liste.tpl.php';