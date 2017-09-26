<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_CRITERE);

//********* variables
$id_reponse = null;
$msg = "";

//********** si post du formulaire interne
if (isset($_POST['enregistrer']) && isset($_POST["maj_id"])) {
/*	
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
	}*/
}

if (isset($_GET['id'])) {
	$id_reponse = $_GET['id'];
}

$libelle_reponse = null;
$valeurs = null;

if (isset($id_reponse)) {
	$t = get_reponse_by_id((int)$id_reponse);
	$libelle_reponse = $t[0];
	$valeurs = $t[1];
}

$max_valeurs_par_reponse = 10;
$nb_lignes_a_afficher = (count($valeurs)) ? count($valeurs) : $max_valeurs_par_reponse;

//view
require 'view/formulaire_reponse.tpl.php';