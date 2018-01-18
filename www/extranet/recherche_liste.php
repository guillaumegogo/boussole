<?php

include('../src/admin/bootstrap.php');
if (!secu_check_role(ROLE_ADMIN)) {
	header('Location: accueil.php');
}

$recherches = get_liste_recherches();

//view
require 'view/recherche_liste.tpl.php';