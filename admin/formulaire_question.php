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
    $result = get_question_by_id((int)$_GET['id']);
}
$question = $result[0];
$reponses = $result[1];

$liste_types = array('radio'=>'Boutons d\'option', 'select'=>'Liste déroulante', 'checkbox'=>'Cases à cocher', 'multiple'=>'Liste à choix multiples');
$liste_taille = array('min'=>2, 'max'=>10);
$liste_obligatoire = array(0=>'non', 1=>'oui');

//view
require 'view/formulaire_question.tpl.php';