<?php

include('../src/admin/bootstrap.php');
$droit_ecriture = (isset($_GET['id'])) ? secu_check_level(DROIT_CRITERE, $_GET['id']) : true;

//********* variables
$id_formulaire = null;
$msg = "";

//********** si post du formulaire interne
if (isset($_POST['restaurer']) && isset($_POST["maj_id"])) {
	$restored = archive('formulaire', (int)$_POST["maj_id"], 1);
 
} elseif (isset($_POST['archiver']) && isset($_POST["maj_id"])) {
	$archived = archive('formulaire', (int)$_POST["maj_id"]);
 
} elseif (isset($_POST['enregistrer'])) {

	echo "<!--<pre>"; print_r($_POST); echo "</pre>-->";
	$name_q = null;
	if(isset($_POST['name_q'])) $name_q = $_POST['name_q'];

	if (!$_POST["maj_id"]) { //requête d'ajout
		$t = create_formulaire($_POST['theme'], $_POST['territoire']);
		
		if(!$t[0]){ //si pas créé, on récupère le message
			$msg = $t[1];
			
		}else{
			$id_formulaire = mysqli_insert_id($conn);
			$msg = 'Le formulaire a été initialisé.';
			
			if ($id_formulaire) {
				$updated = update_formulaire($id_formulaire, $_POST['id_p'], $_POST['ordre_p'], $_POST['titre_p'], $_POST['id_q'], $_POST['page_q'], $_POST['ordre_q'], $_POST['titre_q'], $_POST['reponse_q'], $_POST['type_q'], $name_q);
				if ($updated) $msg = 'Le formulaire a été correctement créé.';
			}
		}
		
	} else { //requête de modification
		$id_formulaire = $_POST['maj_id'];
		$updated = update_formulaire($id_formulaire, $_POST['id_p'], $_POST['ordre_p'], $_POST['titre_p'], $_POST['id_q'], $_POST['page_q'], $_POST['ordre_q'], $_POST['titre_q'], $_POST['reponse_q'], $_POST['type_q'], $name_q);
		
		if ($updated) $msg = 'Le formulaire a été mis à jour.';
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

$territoires[] = array('id_territoire'=>'', 'nom_territoire'=>'National');
$territoires = array_merge($territoires, get_territoires(null, 1));

$types = array(''=>'', 'radio'=>'Boutons d\'option', 'select'=>'Liste déroulante', 'checkbox'=>'Cases à cocher', 'multiple'=>'Liste à choix multiples');

$reponses[] = array('id_reponse'=>'', 'libelle'=>'');
$reponses = array_merge($reponses, get_reponses());

$max_pages = 3;
$max_questions_par_page = 5;

//view
require 'view/formulaire_detail.tpl.php';