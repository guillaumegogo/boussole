<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_PROFESSIONNEL);
/* todo...
if (!secu_check_auth(DROIT_PROFESSIONNEL)){ // si on a les droits, on fait juste un test sur le territoire (cas des animateurs territoriaux notamment)
	if($_SESSION['territoire_id']){
		$result = verif_territoire_pro($_SESSION['territoire_id'], $_GET['id']);
		if (mysqli_num_rows($result) == 0) { header('Location: professionnel_liste.php'); }
	}
}else{ //autrement, le seul cas possible est la consultation de ses propres infos
	$_GET['id'] = $_SESSION['user_pro_id'];
}
*/

//********* variables
$last_id = null;
$msg = '';

//si post du formulaire interne
if (isset($_POST['maj_id'])) {

	//récupération du code insee correspondant à la saisie
	$themes = null;
	$zone = 0;
	$liste_villes = null;
	if (isset($_POST['theme'])) $themes=$_POST['theme'];
	if (isset($_POST['check_zone'])) $zone=$_POST['check_zone'];
	if (isset($_POST['list2'])) $liste_villes=$_POST['list2'];
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

	//requête d'ajout
	if (!$_POST['maj_id']) {
		$created = create_pro($_POST['nom'], $_POST['type'], $_POST['desc'], $_POST['adresse'], $code_postal, $ville, $code_insee, $_POST['courriel'], $_POST['tel'], $_POST['site'], (int)$_POST['delai'], $_POST['competence_geo'], (int)$id_competence_geo, secu_get_current_user_id());
		$last_id = mysqli_insert_id($conn);
		if ($created) $msg = 'Création bien enregistrée.';

	//requête de modification
	} else {
		$updated = update_pro((int)$_POST['maj_id'], $_POST['nom'], $_POST['type'], $_POST['desc'], $_POST['adresse'], $code_postal, $ville, $code_insee, $_POST['courriel'], $_POST['tel'], $_POST['site'], $_POST['delai'], $_POST['actif'], $_POST['competence_geo'], $id_competence_geo, $themes, $zone, $liste_villes, secu_get_current_user_id());
		$last_id = $_POST['maj_id'];
		if ($updated) $msg = 'Modification bien enregistrée.';
	}

	if (!$msg) {
		$msg = 'Il y a eu un problème à l\'enregistrement. Contactez l\'administration centrale si le problème perdure.';
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
$themes = get_liste_themes($id_professionnel, 1);

$competences_geo = array('territoire' => 'Territoire');
$regions = null;
$departements = null;
if (secu_check_role(ROLE_ADMIN)) {
	$competences_geo += array('national' => 'National', 'regional' => 'Régional', 'departemental' => 'Départemental');
	$regions = get_liste_regions();
	$departements = get_liste_departements();
}
if (secu_check_role(ROLE_ANIMATEUR)) {
	$territoires = get_territoires($_SESSION['territoire_id']);
} else {
	$territoires = get_territoires();
}

if($pro['zone_selection_villes'] == 0){ //la liste des villes du territoire
	$liste_villes_pro = get_villes_by_competence_geo($pro['competence_geo'], (int)$pro['id_competence_geo']);
}else{ // la liste des villes du pro
	$liste_villes_pro = get_villes_by_pro((int)$id_professionnel);
}

$incoherences_themes = get_incoherences_themes_by_pro((int)$id_professionnel, $themes);
$incoherences_villes = get_incoherences_villes_by_pro((int)$id_professionnel, $liste_villes_pro);

$offres = get_liste_offres(1,null, (int)$id_professionnel);

//view
require 'view/professionnel_detail.tpl.php';