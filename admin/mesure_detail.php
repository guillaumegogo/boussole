<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_MESURE);

//********* variables
$id_mesure = null;
$msg = "";
$user_pro_id = null;
if (isset($_SESSION['user_pro_id'])) $user_pro_id=$_SESSION['user_pro_id'];

//********** si post du formulaire interne
if (isset($_POST['restaurer']) && isset($_POST["maj_id"])) {

	$restored = archive('mesure', (int)$_POST["maj_id"], 1);
 
} elseif (isset($_POST['archiver']) && isset($_POST["maj_id"])) {

	$archived = archive('mesure', (int)$_POST["maj_id"]);
 
} elseif (isset($_POST['enregistrer']) && isset($_POST["maj_id"])) {
	
	if (!$_POST["maj_id"]) { //requête d'ajout
		$created = create_mesure($_POST['nom'], html2bbcode($_POST['desc']), $_POST['du'], $_POST['au'], (int)$_POST['pro'], secu_get_current_user_id());
		$id_mesure = mysqli_insert_id($conn);
		
		if ($created) {
			$msg = "Création bien enregistrée.";
		}

	} else { //requête de modification
		$id_mesure = $_POST['maj_id'];
		$code_postal = substr($_POST['commune'], -5);
		$ville = substr($_POST['commune'], 0, -6);
		$liste_villes=null;
		if(isset($_POST['list2'])) $liste_villes=$_POST['list2'];
		
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
	
		$updated = update_mesure((int)$id_mesure, $_POST['nom'], html2bbcode($_POST['desc']), $_POST['du'], $_POST['au'], $_POST['sous_theme'], $_POST["adresse"], $code_postal, $ville, $_POST['courriel'], $_POST['tel'], $_POST['site'], $_POST['competence_geo'], (int)$id_competence_geo, $liste_villes, secu_get_current_user_id());
		
		$updated2 = null;
		if (isset($_POST['maj_criteres']) && $_POST['maj_criteres']) { //mise à jour des critères
			$liste_criteres=null;
			if(isset($_POST['critere'])) $liste_criteres=$_POST['critere'];
			$updated2 = update_criteres_mesure((int)$id_mesure, $liste_criteres, secu_get_current_user_id());
		}
		
		if ($updated[0] || $updated[1] || $updated2 ) {
			$msg = "Modification bien enregistrée.";
		}
	}

	if (!$msg) {
		$msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème perdure.";
	}
}

//********** récupération de l'id de la mesure (soit celle en paramètre, soit celle qui vient d'être créée/mise à jour)
if (isset($_GET['id'])) {
	$id_mesure = $_GET['id'];
}

//********** affichage de la mesure
if (isset($id_mesure)) {
	$row = get_mesure_by_id((int)$id_mesure);

	if ($row){

		//liste déroulante des thèmes / sous-thèmes du pro
		$select_theme = "";
		$select_sous_theme = "";
		if (!$row['id_theme_pere']) {
			$select_theme = "<option value=\"\">A choisir</option>";
		}
		if (!$row['id_sous_theme']) {
			$select_sous_theme = "<option value=\"\">A choisir</option>";
		}
		
		$tab_js_soustheme = array();
		
		$themes = get_themes_by_pro((int)$row['id_professionnel']);
		foreach($themes as $rowt){
			if (!isset($rowt['id_theme_pere'])) {
				if ($rowt['id_professionnel'] == $row['id_professionnel']) {
					$select_theme .= '<option value="' . $rowt['id_theme'] . '" ';
					if ($rowt['id_theme'] == $row['id_theme_pere']) {
						$select_theme .= ' selected ';
					}
					$select_theme .= '>' . $rowt['libelle_theme'] . '</option>';
				}
				$tab_js_soustheme[$rowt['id_theme']] = '';
				//liste des sous-thèmes (par défaut les sous-thèmes du thème-père sélectionné)
			} else {
				if ($rowt['id_theme_pere'] == $row['id_theme_pere']) {
					$select_sous_theme .= '<option value="' . $rowt['id_theme'] . '" ';
					if ($rowt['id_theme'] == $row['id_sous_theme']) {
						$select_sous_theme .= ' selected ';
					}
					$select_sous_theme .= '>' . $rowt['libelle_theme'] . '</option>';
				}
				//tableau des listes pour fonction javascript ci-dessous
				if (isset($tab_js_soustheme[$rowt['id_theme_pere']])) {
					$tab_js_soustheme[$rowt['id_theme_pere']] .= '<option value=\'' . $rowt['id_theme'] . '\'>' . $rowt['libelle_theme'] . '</option>';
				}
			}
		}

		//*********** zone...
		$competences_geo = array('national' => 'National', 'regional' => 'Régional', 'departemental' => 'Départemental', 'communes' => 'Communes');
		$regions = get_liste_regions();
		$departements = get_liste_departements();

		//*********** liste des villes liées à la mesure
		$liste_villes_mesure = null;
		if ($row['competence_geo']=="communes") {
			$liste_villes_mesure = get_villes_by_mesure((int)$id_mesure);
		}
		
		//récup du formulaire (et des réponses !)
		$t = get_criteres_mesure($id_mesure);
		$questions = $t[0];
		$reponses = $t[1];
	}

//********** écran de création simple : récupération de la liste des professionnels en fonction des droits du user
} else {
	$liste_pro = "<option value=\"\" >A choisir</option>";
	$result = get_liste_pros_select("national");
	if (count($result) > 0) {
		foreach($result as $rowp) {
			$liste_pro .= '<option value="' . $rowp['id_professionnel'] . '"';
			if ($rowp['id_professionnel'] == $user_pro_id) {
				$liste_pro .= ' selected ';
			}
			$liste_pro .= '>' . $rowp['nom_pro'] . '</option>';
		}
	}
}

//view
require 'view/mesure_detail.tpl.php';