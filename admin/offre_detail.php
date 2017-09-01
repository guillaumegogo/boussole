<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_OFFRE);

//********* variables
$id_offre = null;
$msg = "";

//********** si post du formulaire interne
if (isset($_POST["maj_id"])) {
	
	if (!$_POST["maj_id"]) { //requête d'ajout
		$created = create_offre($_POST['nom'], $_POST['desc'], $_POST['du'], $_POST['au'], (int)$_POST['pro'], secu_get_current_user_id());
		$id_offre = mysqli_insert_id($conn);
		
		if ($created) {
			$msg = "Modification bien enregistrée.";
		}
	} else { //requête de modification
		$id_offre = $_POST['maj_id'];
		$code_postal = substr($_POST['commune'], -5);
		$ville = substr($_POST['commune'], 0, -6);
		
		$updated = update_offre((int)$id_offre, $_POST['nom'], $_POST['desc'], $_POST['du'], $_POST['au'], $_POST['sous_theme'], $_POST["adresse"], $code_postal, $ville, $_POST['courriel'], $_POST['tel'], $_POST['site'], (int)$_POST['delai'], (int)$_POST['zone'], (int)$_POST['actif'], secu_get_current_user_id());
		
		if ($updated) {
			$msg = "Modification bien enregistrée.";
		}
		if (isset($_POST['maj_criteres']) && $_POST['maj_criteres']) { //mise à jour des critères
			$liste_villes=null;
			if(isset($_POST['list2'])) $liste_villes=$_POST['list2'];
			$updated2 = update_criteres_offre((int)$id_offre, $liste_villes, $_POST['critere'], secu_get_current_user_id());
		}
	}

	if (!$msg) {
		$msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème perdure.";
	}
}

//********** récupération de l'id de l'offre (soit celle en paramètre, soit celle qui vient d'être créée/mise à jour)
if (isset($_GET['id'])) {
	$id_offre = $_GET['id'];
}

//********** affichage de l'offre
if (isset($id_offre)) {
	$row = get_offre_by_id((int)$id_offre);

	if ($row){
		//affichage de la compétence géo du pro
		$geo = "";
		switch ($row['competence_geo']) {
			case "territoire":
				$geo = $row['competence_geo'] . " " . $row['nom_territoire'];
				break;
			case "departemental":
				$geo = $row['competence_geo'] . " " . $row['nom_departement'];
				break;
			case "regional":
				$geo = $row['competence_geo'] . " " . $row['nom_region'];
				break;
			case "national":
				$geo = $row['competence_geo'];
				break;
		}

		//récup du formulaire dynamique
		if (isset($row['id_theme_pere']) && $row['id_theme_pere']) {
			$t = get_criteres($id_offre, $row['id_theme_pere']);
			$questions = $t[0];
			$reponses = $t[1];
		}

		//liste déroulante des thèmes / sous-thèmes du pro
		$select_theme = "";
		$select_sous_theme = "";
		if (!$row['id_theme_pere']) {
			$select_theme = "<option value=\"\">A choisir</option>";
		}
		if (!$row['id_sous_theme']) {
			$select_sous_theme = "<option value=\"\">A choisir</option>";
		}
		$tab_select_soustheme = array();

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
				$tab_select_soustheme[$rowt['id_theme']] = '';
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
				if (isset($tab_select_soustheme[$rowt['id_theme_pere']])) {
					$tab_select_soustheme[$rowt['id_theme_pere']] .= '<option value="' . $rowt['id_theme'] . '">' . $rowt['libelle_theme'] . '</option>';
				}
			}
		}

		//*********** liste des villes accessibles au pro
		$liste_villes_pro = '';
		$villes = get_villes_by_competence_geo($row['competence_geo'], (int)$row['id_competence_geo']);
		foreach($villes as $rowv){
			$liste_villes_pro .= '<option value="' . $rowv['code_insee'] . '">' . $rowv['nom_ville'] . ' ' . $rowv['cp'] . '</option>';
		}

		//*********** liste des villes liées à l'offre
		$liste2 = '';
		if ($row['zone_selection_villes']) {
			$willes = get_villes_by_offre((int)$id_offre);
			foreach($willes as $roww){
				$liste2 .= '<option value="' . $roww['code_insee'] . '">' . $roww['nom_ville'] . ' ' . $roww['code_postal'] . '</option>';
			}
		}
	}

//********** sinon écran de création simple : récupération de la liste des professionnels (avec thème) en fonction des droits du user
} else {
	$liste_pro = "<option value=\"\" >A choisir</option>";
	$result = get_liste_pros_select($_SESSION['territoire_id'], $_SESSION['user_pro_id']);
	if (mysqli_num_rows($result) > 0) {
		while ($rowp = mysqli_fetch_assoc($result)) {
			$liste_pro .= '<option value="' . $rowp['id_professionnel'] . '"';
			if (isset($_SESSION['user_pro_id']) && $rowp['id_professionnel'] == $_SESSION['user_pro_id']) {
				$liste_pro .= ' selected ';
			}
			$liste_pro .= '>' . $rowp['nom_pro'] . '</option>';
		}
	}
}

//view
require 'view/offre_detail.tpl.php';