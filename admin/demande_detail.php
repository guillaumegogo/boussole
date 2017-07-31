<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_DEMANDE);

//********* variables
$msg = "";
if (isset($_POST["id_traite"]) && !empty($_POST["id_traite"]) && isset($_POST['commentaire'])) {
    $updated = update_demande((int)$_POST["id_traite"], $_POST["commentaire"], secu_get_current_user_id());
    if ($updated) {
        $msg = '<div class="soustitre">La demande a été mise à jour.</div>';
    }
}

if (isset($_GET["id"]) && !empty($_GET['id'])) {
    $demande = get_demande_by_id((int)$_GET['id']);
}

//view
require '../src/admin/view/demande_detail.tpl.php';