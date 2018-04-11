<?php

include('../src/admin/bootstrap.php');
$check = secu_check_login(DROIT_DEMANDE);
$perimetre_lecture = $check['lecture'];

$recherches = get_liste_recherches();

//view
require 'view/recherche_liste.tpl.php';