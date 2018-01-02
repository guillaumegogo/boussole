<?php

include('../src/admin/bootstrap.php');
$droit_ecriture = (isset($_GET['id'])) ? secu_check_level(DROIT_PROFESSIONNEL, $_GET['id']) : true;

//********* variables
$last_id = null;
$msg = '';

//si post du formulaire interne
if (isset($_POST['restaurer']) && isset($_POST["maj_id"]) && $_POST["maj_id"]) {

	$restored = archive('pro', (int)$_POST["maj_id"], 1);
 
} elseif (isset($_POST['archiver']) && isset($_POST["maj_id"]) && $_POST["maj_id"]) {

	$archived = archive('pro', (int)$_POST["maj_id"]);
 
} elseif (isset($_POST['enregistrer'])) {

	//récupération du code insee correspondant à la saisie
	$themes = null;
	$zone = 0;
	$liste_villes = null;
	$check_editeur = null;
	if (isset($_POST['theme'])) $themes=$_POST['theme'];
	if (isset($_POST['check_zone'])) $zone=$_POST['check_zone'];
	if (isset($_POST['list2'])) $liste_villes=$_POST['list2'];
	if (isset($_POST['check_editeur'])) $check_editeur=$_POST['check_editeur'];
	$code_postal = substr($_POST['commune'], -5);
	$ville = substr($_POST['commune'], 0, -6);
	$code_insee = get_code_insee($code_postal, $ville);

	//si choix d'une compétence région/département/territoire, récupération de l'id correspondant (région/département/territoire)
	$id_competence_geo = "NULL";
	if (isset($_POST['competence_geo'])) {
		if ($_POST['competence_geo'] == 'regional' && $_POST['liste_regions']) {
			$id_competence_geo = $_POST['liste_regions'];
		} else if ($_POST['competence_geo'] == 'departemental' && $_POST['liste_departements']) {
			$id_competence_geo = $_POST['liste_departements'];
		} else if ($_POST['competence_geo'] == 'territoire' && $_POST['liste_territoires']) {
			$id_competence_geo = $_POST['liste_territoires'];
		}
	}
	
	$visibilite = (isset($_POST['visibilite'])) ? 1:0;

	//requête d'ajout
	if (!$_POST["maj_id"]) {
		$created = create_pro($_POST['nom'], $_POST['type_id'], $_POST['statut_id'], html2bbcode($_POST['desc']), $_POST['adresse'], $code_postal, $ville, $code_insee, $_POST['courriel'], $_POST['tel'], (int)$visibilite, $_POST['courriel_ref'], $_POST['tel_ref'], $_POST['site'], (int)$_POST['delai'], $_POST['competence_geo'], (int)$id_competence_geo, (int)$check_editeur, $themes, $zone, $liste_villes);
		
		if ($created[0]){
			$last_id = $created[1];
			$msg = "Création bien enregistrée.";
		}else{
			$msg = $created[1];
		}

	//requête de modification
	} else {
		$updated = update_pro((int)$_POST['maj_id'], $_POST['nom'], $_POST['type_id'], $_POST['statut_id'], html2bbcode($_POST['desc']), $_POST['adresse'], $code_postal, $ville, $code_insee, $_POST['courriel'], $_POST['tel'], (int)$visibilite, $_POST['courriel_ref'], $_POST['tel_ref'], $_POST['site'], $_POST['delai'], $_POST['competence_geo'], $id_competence_geo, (int)$check_editeur, $themes, $zone, $liste_villes);
		$last_id = $_POST['maj_id'];
		if (isset($updated)) $msg = 'Modification bien enregistrée.';
	}

	if (!$msg) {
		$msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème perdure.";
	}
}

//*********** affichage du professionnel demandé ou nouvellement créé
$pro = [];
$id_professionnel = $last_id;
if (isset($_GET['id'])) {
	$id_professionnel = $_GET['id'];
}
if (isset($id_professionnel)) {
	$pro = get_pro_by_id((int)$id_professionnel); 
}

//********** listes : thèmes et compétences géographiques (régions, départements et/ou territoires )
$types = get_liste_parametres('type_pro');
$statuts = get_liste_parametres('statut');
$themes = get_liste_themes(1, $id_professionnel);

$competences_geo = array('territoire' => 'Territoire');
$regions = null;
$departements = null;
$territoires = null;
if (secu_check_role(ROLE_ADMIN)) {
	$competences_geo += array('national' => 'National', 'regional' => 'Régional', 'departemental' => 'Départemental');
	$regions = get_liste_regions();
	$departements = get_liste_departements();
}
if (secu_check_role(ROLE_ANIMATEUR)) {
	$territoires = get_territoires($_SESSION['admin']['territoire_id'],1);
} else {
	$territoires = get_territoires(null,1);
}

//les villes accessibles au pro (si on veut toutes les villes de France on utilise include('../src/admin/villes_options_insee.inc'); )
//$villes = get_villes_by_territoire((int)$id_territoire);
$villes_accessibles = null;
if(isset($pro['competence_geo']) && isset($pro['id_competence_geo'])){
	$villes_accessibles = get_villes_by_competence_geo($pro['competence_geo'], (int)$pro['id_competence_geo']);
}

$liste_villes_pro=null;
if(isset($pro['zone_selection_villes']) && $pro['zone_selection_villes'] == 1){ 
	$liste_villes_pro = get_villes_by_pro((int)$id_professionnel); // la liste personnalisée des villes du pro
}
/*
else if(isset($pro['competence_geo']) && $pro['competence_geo'] && $pro['id_competence_geo']){ 
	$liste_villes_pro =  //la liste des villes du territoire
}
*/

$incoherences_themes = get_incoherences_themes_by_pro((int)$id_professionnel, $themes);
$incoherences_villes = get_incoherences_villes_by_pro((int)$id_professionnel, $liste_villes_pro);

$offres = ($id_professionnel) ? get_liste_offres(1,null, (int)$id_professionnel) : null;

//view
require 'view/professionnel_detail.tpl.php';
// else require 'view/professionnel_detail_r.tpl.php';