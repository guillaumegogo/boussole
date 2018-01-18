<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_FORMULAIRE);

//********* variables
$id_reponse = null;
$msg = "";

//********** si post du formulaire interne
if ((isset($_POST['enregistrer']) || isset($_POST['enregistrer-sous'])) && isset($_POST["maj_id"])) {
	
	if (!$_POST["maj_id"] || isset($_POST['enregistrer-sous'])) { //requête d'ajout
		
		$created = create_reponse($_POST['libelle'], $_POST['id_v'], $_POST['libelle_v'], $_POST['valeur_v'], $_POST['ordre_v'], $_POST['actif']);
		$id_reponse = $created;
		if ($created) $msg = "Création bien enregistrée.";

	} else { //requête de modification
		$id_reponse = $_POST['maj_id'];
		$defaut= null;
		$updated = update_reponse($id_reponse, $_POST['libelle'], $_POST['id_v'], $_POST['libelle_v'], $_POST['valeur_v'], $_POST['ordre_v'], $_POST['actif'], $defaut);
		
		if ($updated) {
			$msg = 'La liste de réponses a été mise à jour.';
		}
	}
}

if (!$id_reponse && isset($_GET['id'])) {
	$id_reponse = $_GET['id'];
}

$libelle_reponse = null;
$valeurs = null;

if (isset($id_reponse)) {
	$t = get_reponse_by_id((int)$id_reponse);
	$libelle_reponse = $t[0];
	$valeurs = $t[1];
}

$liste_reponses = get_reponses();

$max_valeurs_par_reponse = 10;
$nb_lignes_a_afficher = (count($valeurs)) ? count($valeurs) : $max_valeurs_par_reponse;

//view
require 'view/formulaire_reponse.tpl.php';