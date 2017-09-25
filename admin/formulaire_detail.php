<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_CRITERE);

//********* variables
$id_formulaire = null;
$msg = "";

//********** si post du formulaire interne
if (isset($_POST['restaurer']) && isset($_POST["maj_id"])) {
	$restored = archive('formulaire', (int)$_POST["maj_id"], 1);
 
} elseif (isset($_POST['archiver']) && isset($_POST["maj_id"])) {
	$archived = archive('formulaire', (int)$_POST["maj_id"]);
 
} elseif (isset($_POST['enregistrer']) && isset($_POST["maj_id"])) {

	echo "<!--<pre>"; print_r($_POST); echo "</pre>-->";
	$name_q = null;
	if(isset($_POST['name_q'])) $name_q = $_POST['name_q'];

	if (!$_POST["maj_id"]) { //requête d'ajout
		$created = create_formulaire($_POST['theme'], $_POST['territoire'], $_POST['id_p'], $_POST['ordre_p'], $_POST['titre_p'], $_POST['id_q'], $_POST['ordre_q'], $_POST['titre_q'], $_POST['reponse_q'], $_POST['type_q'], $name_q, secu_get_current_user_id());
		$id_formulaire = mysqli_insert_id($conn);
		
		if ($created) $msg = "Création bien enregistrée.";

	} else { //requête de modification
		$id_formulaire = $_POST['maj_id'];
		$updated = update_formulaire($id_formulaire, $_POST['id_p'], $_POST['ordre_p'], $_POST['titre_p'], $_POST['id_q'], $_POST['ordre_q'], $_POST['titre_q'], $_POST['reponse_q'], $_POST['type_q'], $name_q, secu_get_current_user_id());
		
		if ($updated) {
			$msg = 'Le formulaire a été mise à jour.';
		}
	}
}

if (isset($_GET['id'])) {
	$id_formulaire = $_GET['id'];
}

$meta = null;
$pages = null;
$questions = null;
$reponses = null;

if (isset($id_formulaire)) {
	$result = get_formulaire_by_id((int)$id_formulaire);
	
	if (isset($result)){
		$meta = $result[0];
		$pages = $result[1];
		$questions = $result[2];
	}
}

$themes = get_liste_themes(1);
$territoires = get_territoires();
array_unshift($territoires, array('id_territoire'=>'', 'nom_territoire'=>'National', 'code_territoire'=>''));
$types = array('radio'=>'Boutons d\'option', 'select'=>'Liste déroulante', 'checkbox'=>'Cases à cocher', 'multiple'=>'Liste à choix multiples');
$reponses = get_reponses();
array_unshift($reponses, array('id_reponse'=>'', 'libelle'=>''));

$max_pages = 3;
$max_questions_par_page = 5;

//view
require 'view/formulaire_detail.tpl.php';