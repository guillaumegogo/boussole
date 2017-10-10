<?php

include('../src/admin/bootstrap.php');
if (!secu_check_role(ROLE_ADMIN)) {
	header('Location: accueil.php');
}

$droits = get_liste_droits();
$traduction = array('<center>-</center>', 'Pro', 'Territoire', 'Toutes');

//view
require 'view/droits.tpl.php';