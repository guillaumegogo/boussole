<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_CRITERE);

//********* variables
$msg = "";
if (isset($_POST["id_traite"]) && !empty($_POST["id_traite"]) && isset($_POST['commentaire'])) {
    $updated = update_formulaire((int)$_POST["id_traite"], $_POST["commentaire"], secu_get_current_user_id());
    if ($updated) {
        $msg = '<div class="soustitre">Le formulaire a été mise à jour.</div>';
    }
}

if (isset($_GET["id"]) && !empty($_GET['id'])) {
    $form = get_formulaire_by_id((int)$_GET['id']);
}

//view
require 'view/formulaire_detail.tpl.php';