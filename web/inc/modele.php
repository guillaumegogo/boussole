<?php
require('secret/connect.php');

//modele de fonction select
function model(){
    global $conn;
    $query = 'SELECT ... FROM ...';
    $stmt = mysqli_prepare($conn, $query);
    if (mysqli_error($conn)) {
        echo mysqli_error($conn);
        exit;
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $nimetus, $kogus);
    $rows = array();
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = array(
            'id' => $id,
            'xxxxx' => $xxx,
        );
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

//********* affichage des thèmes disponibles en fonction de la ville choisie 
//todo : la requête fait la vérification des thèmes des pros autorisés à travailler sur une zone géographique englobant la zone indiquée : pays, région, département ou territoire. il faudra probablement descendre au niveau des offres pour une meilleure granularité. 
/* on pourrait descendre à la granularité de l'offre, mais la requête serait encore plus complexe :
(...) JOIN `bsl_offre` ON `bsl_offre`.id_professionnel=`bsl_professionnel`.id_professionnel
JOIN `bsl_theme` as theme_offre ON bsl_offre.id_sous_theme=theme_offre.id_theme
WHERE actif_offre=1 AND debut_offre <= CURDATE() AND fin_offre >= CURDATE() (...)*/
function get_themes(){
	global $conn;

	$sql = "SELECT DISTINCT `bsl_theme`.id_theme, `bsl_theme`.`libelle_theme`, `bsl_theme`.`actif_theme`
		FROM `bsl_theme`
		JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.id_theme=`bsl_theme`.id_theme
		JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_professionnel_themes`.id_professionnel
		LEFT JOIN `bsl_territoire` ON `bsl_professionnel`.competence_geo=\"territoire\" AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo` 
		LEFT JOIN `bsl_territoire_villes` ON `bsl_territoire_villes`.`id_territoire`=`bsl_territoire`.`id_territoire`
		LEFT JOIN `bsl__departement` ON `bsl_professionnel`.competence_geo=\"departemental\" AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `bsl__region` ON `bsl_professionnel`.competence_geo=\"regional\" AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `bsl__departement` as `bsl__departement_region` ON `bsl__departement_region`.`id_region`=`bsl__region`.`id_region`
		WHERE `bsl_theme`.actif_theme=1 AND `bsl_professionnel`.actif_pro=1 AND (`bsl_professionnel`.competence_geo=\"national\" OR code_insee=? OR `bsl__departement`.id_departement=SUBSTR(?,1,2) OR `bsl__departement_region`.id_departement=SUBSTR(?,1,2))
		UNION
		SELECT DISTINCT `bsl_theme`.id_theme, `bsl_theme`.`libelle_theme`, `bsl_theme`.`actif_theme`
		FROM `bsl_theme`
		WHERE `id_theme_pere` IS NULL AND `actif_theme`=0";

	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 'sss', $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['code_insee']);
	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_bind_result($stmt, $id_theme, $libelle_theme, $actif_theme);

		while (mysqli_stmt_fetch($stmt)) {
			$themes[] = array('id'=>$id_theme, 'libelle'=>$libelle_theme, 'actif'=>$actif_theme);
		}
	}
	mysqli_stmt_close($stmt);
	return $themes;
}

//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville 
function get_ville($saisie){
	global $conn;

	//test si saisie avec le autocomplete (auquel cas ça se termine par des chiffres)
	if(is_numeric(substr($saisie, -3))){
		$sql = "SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ', ') as `codes_postaux` 
			FROM `bsl__ville` 
			WHERE `nom_ville` LIKE ? AND `code_postal` LIKE ?
			GROUP BY `nom_ville`, `code_insee`";
		$stmt = mysqli_prepare($conn, $sql);
		$ville = substr($saisie, 0, -6);
		$cp = substr($saisie, -5);
		mysqli_stmt_bind_param($stmt, 'ss', $ville, $cp);
	}else{
		$sql = "SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ', ') as `codes_postaux` 
			FROM `bsl__ville` 
			WHERE `nom_ville` LIKE ?
			GROUP BY `nom_ville`, `code_insee`";
		$stmt = mysqli_prepare($conn, $sql);
		$saisie_insee = format_insee($saisie).'%';
		mysqli_stmt_bind_param($stmt, 's', $saisie_insee);
	}

	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_bind_result($stmt, $nom_ville, $code_insee, $codes_postaux);
		while (mysqli_stmt_fetch($stmt)) {
			$row[] = array('nom_ville'=>$nom_ville, 'code_insee'=>$code_insee, 'codes_postaux'=>$codes_postaux);
		}
	}
	mysqli_stmt_close($stmt);
	return $row;
}

//************ récupération des éléments de la page du formulaire
function get_formulaire($etape){
	global $conn;

	$sql = 'SELECT `bsl_formulaire`.`id_formulaire`, `bsl_formulaire`.`nb_pages`, `bsl_formulaire__page`.`titre`, `bsl_formulaire__page`.`ordre` as `ordre_page`, `bsl_formulaire__page`.`aide`, `bsl_formulaire__question`.`libelle` as `libelle_question`, `bsl_formulaire__question`.`html_name`, `bsl_formulaire__question`.`type`, `bsl_formulaire__question`.`taille`, `bsl_formulaire__question`.`obligatoire`, `bsl_formulaire__valeur`.`libelle`, `bsl_formulaire__valeur`.`valeur`, `bsl_formulaire__valeur`.`defaut` FROM `bsl_formulaire` 
	JOIN `bsl_theme` ON `bsl_theme`.`id_theme`=`bsl_formulaire`.`id_theme`
	JOIN `bsl_formulaire__page` ON `bsl_formulaire__page`.`id_formulaire`=`bsl_formulaire`.`id_formulaire` AND `bsl_formulaire__page`.`actif`=1
	JOIN `bsl_formulaire__question` ON `bsl_formulaire__question`.`id_page`=`bsl_formulaire__page`.`id_page` AND `bsl_formulaire__question`.`actif`=1
	JOIN `bsl_formulaire__valeur` ON `bsl_formulaire__valeur`.`id_question`=`bsl_formulaire__question`.`id_question` AND `bsl_formulaire__valeur`.`actif`=1
	WHERE `bsl_formulaire`.`actif`=1 AND `bsl_theme`.`libelle_theme`= ? AND `bsl_formulaire__page`.`ordre` = ?
	ORDER BY `ordre_page`, `bsl_formulaire__question`.`ordre`, `bsl_formulaire__valeur`.`ordre`';
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 'si', $_SESSION['besoin'], $etape);

	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_bind_result($stmt, $id_formulaire, $nb_pages, $titre, $ordre_page, $aide, $question, $name, $type, $taille, $obligatoire, $libelle, $valeur, $defaut);
		$i=1;
		while (mysqli_stmt_fetch($stmt)) {
			if($i){
				$meta = array('id'=>$id_formulaire, 'nb'=>$nb_pages, 'titre'=>$titre, 'etape'=>$ordre_page, 'aide'=>$aide, 'suite'=>($ordre_page<$nb_pages) ? ($ordre_page+1) : 'fin');
				$i--;
			}
			$elements[] = array('que'=>$question, 'name'=>$name, 'type'=>$type, 'tai'=>$taille, 'obl'=>$obligatoire, 'lib'=>$libelle, 'val'=>$valeur, 'def'=>$defaut);
		}
	}
	mysqli_stmt_close($stmt);
	return [$meta, $elements];
}

//************ construction de LA requête
function get_liste_offres(){
	global $conn;

	$sql = "SELECT `id_offre`, `nom_offre`, `description_offre`, `t`.`id_sous_theme`, `bsl_theme`.`libelle_theme` AS `sous_theme_offre`, `nom_pro` /*`t`.*, `bsl_theme`.`libelle_theme` AS `sous_theme_offre`, `theme_pere`.`libelle_theme` AS `theme_offre` */
		FROM ( SELECT `bsl_offre`.*,   /* on construit ici la liste des critères */
			GROUP_CONCAT( if(nom_critere= 'age_min', valeur_critere, NULL ) separator '|') `age_min`, 
			GROUP_CONCAT( if(nom_critere= 'age_max', valeur_critere, NULL ) separator '|') `age_max`, 
			GROUP_CONCAT( if(nom_critere= 'villes', valeur_critere, NULL ) separator '|') `villes`, 
			GROUP_CONCAT( if(nom_critere= 'sexe', valeur_critere, NULL ) separator '|') `sexe`, 
			GROUP_CONCAT( if(nom_critere= 'jesais', valeur_critere, NULL ) separator '|') `jesais`, 
			GROUP_CONCAT( if(nom_critere= 'situation', valeur_critere, NULL ) separator '|') `situation`, 
			GROUP_CONCAT( if(nom_critere= 'nationalite', valeur_critere, NULL ) separator '|') `nationalite`, 
			GROUP_CONCAT( if(nom_critere= 'permis', valeur_critere, NULL ) separator '|') `permis`, 
			GROUP_CONCAT( if(nom_critere= 'handicap', valeur_critere, NULL ) separator '|') `handicap`, 
			GROUP_CONCAT( if(nom_critere= 'experience', valeur_critere, NULL ) separator '|') `experience`, 
			GROUP_CONCAT( if(nom_critere= 'type_emploi', valeur_critere, NULL ) separator '|') `type_emploi`, 
			GROUP_CONCAT( if(nom_critere= 'temps_plein', valeur_critere, NULL ) separator '|') `temps_plein`, 
			GROUP_CONCAT( if(nom_critere= 'inscription', valeur_critere, NULL ) separator '|') `inscription`, 
			GROUP_CONCAT( if(nom_critere= 'etudes', valeur_critere, NULL ) separator '|') `etudes`, 
			GROUP_CONCAT( if(nom_critere= 'diplome', valeur_critere, NULL ) separator '|') `diplome`, 
			GROUP_CONCAT( if(nom_critere= 'secteur', valeur_critere, NULL ) separator '|') `secteur`
		FROM `bsl_offre_criteres`
		JOIN `bsl_offre` ON `bsl_offre`.`id_offre`=`bsl_offre_criteres`.`id_offre`
		WHERE `bsl_offre`.`actif_offre` = 1 
		GROUP BY `bsl_offre_criteres`.`id_offre`
	) as `t`
	JOIN `bsl_theme` ON `bsl_theme`.`id_theme`=`t`.`id_sous_theme`
	JOIN `bsl_theme` AS `theme_pere` ON `theme_pere`.`id_theme`=`bsl_theme`.`id_theme_pere`
	JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`t`.`id_professionnel` /* s'il n'y a pas une liste de villes propre à l'offre (zone_selection_villes=0), alors il faut aller chercher celles du pro, d'où les jointures en dessous ↓ */
	LEFT JOIN `bsl_territoire` ON `t`.`zone_selection_villes`=0 AND `bsl_professionnel`.`competence_geo`='territoire' AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo` 
	LEFT JOIN `bsl_territoire_villes` ON `bsl_territoire_villes`.`id_territoire`=`bsl_territoire`.`id_territoire`
	LEFT JOIN `bsl__departement` ON `bsl_professionnel`.`competence_geo`='departemental' AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `bsl__region` ON `bsl_professionnel`.`competence_geo`='regional' AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `bsl__departement` as `bsl__departement_region` ON `bsl__departement_region`.`id_region`=`bsl__region`.`id_region`

	WHERE `t`.`debut_offre` <= CURDATE() AND `t`.`fin_offre` >= CURDATE() 
	AND `bsl_professionnel`.`actif_pro` = 1
	AND `theme_pere`.`libelle_theme` = ? 
	AND ((`t`.`zone_selection_villes`=1 AND `t`.`villes` LIKE ?) /* soit il y a une liste de villes au niveau de l'offre */
		OR (`t`.`zone_selection_villes`=0 AND ( /* sinon il faut chercher dans la zone de compétence du pro */
			`bsl_professionnel`.`competence_geo` = 'national'
			OR `bsl_territoire_villes`.`code_insee` = ?
			OR `bsl__departement`.`id_departement` = SUBSTR(?,1,2) 
			OR `bsl__departement_region`.`id_departement` = SUBSTR(?,1,2)
		)))
	AND `t`.`age_min` <= ? AND `t`.`age_max` >= ?
	AND `t`.`situation` LIKE ? AND `t`.`etudes` LIKE ? AND `t`.`diplome` LIKE ? AND `t`.`temps_plein` LIKE ? "; 

	$terms = array ( $_SESSION['besoin'], "%".$_SESSION["code_insee"]."%", $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION["age"], $_SESSION["age"], "%".$_SESSION["situation"]."%", "%".$_SESSION["etudes"]."%", "%".$_SESSION["diplome"]."%", "%".$_SESSION["temps_plein"]."%");
	$terms_type = "sssssiissss";

	if (isset($_SESSION["sexe"])) {
		$sql .= " AND `t`.`sexe` LIKE ? ";
		$terms[] = "%".$_SESSION["sexe"]."%";
		$terms_type .= "s";
	}
	if (isset($_SESSION["jesais"])) {
		$sql .= " AND `t`.`jesais` LIKE ? ";
		$terms[] = "%".$_SESSION["jesais"]."%";
		$terms_type .= "s";
	}
	if (isset($_SESSION["nationalite"])) {
		$sql .= " AND `t`.`nationalite` LIKE ? ";
		$terms[] = "%".$_SESSION["nationalite"]."%";
		$terms_type .= "s";
	}
	if (isset($_SESSION["handicap"])) {
		$sql .= " AND `t`.`handicap` LIKE ? ";
		$terms[] = "%".$_SESSION["handicap"]."%";
		$terms_type .= "s";
	}
	if (isset($_SESSION["permis"])) {
		$sql .= " AND `t`.`permis` LIKE ? ";
		$terms[] = "%".$_SESSION["permis"]."%";
		$terms_type .= "s";
	}
	if (isset($_SESSION["experience"])) {
		$sql .= " AND `t`.`experience` LIKE ? ";
		$terms[] = "%".$_SESSION["experience"]."%";
		$terms_type .= "s";
	}
	$boutdesql = "";
	if (isset($_SESSION['secteur'])){
		foreach ($_SESSION['secteur'] as $selected_option) {
			$boutdesql .= " `t`.`secteur` LIKE ? OR";
			$terms[] = "%".$selected_option."%";
			$terms_type .= "s";
		}
		$sql .= " AND (". $boutdesql. " FALSE)";
	}
	$boutdesql = "";
	if (isset($_SESSION['type_emploi'])){
		foreach ($_SESSION['type_emploi'] as $selected_option) {
			$boutdesql .= " `t`.`type_emploi` LIKE ? OR";
			$terms[] = "%".$selected_option."%";
			$terms_type .= "s";
		}
		$sql .= " AND (". $boutdesql. " FALSE)";
	}
	$boutdesql = "";
	if (isset($_SESSION['inscription'])){
		foreach ($_SESSION['inscription'] as $selected_option) {
			$boutdesql .= " `t`.`inscription` LIKE ? OR";
			$terms[] = "%".$selected_option."%";
			$terms_type .= "s";
		}
		$sql .= " AND (". $boutdesql. " FALSE)";
	}
	$sql .= " ORDER BY `bsl_theme`.`ordre_theme`";

	if ($stmt = mysqli_prepare($conn, $sql)) {

		//******** petite manip pour gérer le nombre variable de paramètres dans la requête
		$query_params = array();
		$query_params[] = $terms_type;
		foreach ($terms as $id => $term){
		  $query_params[] = &$terms[$id];
		}
		call_user_func_array(array($stmt,'bind_param'),$query_params);
		//******** fin de la manip...
		
		/******* pour debugage
		$print_sql = $sql;
		foreach($terms as $term){
			$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
		}
		*/

		if (mysqli_stmt_execute($stmt)) {
			mysqli_stmt_bind_result($stmt, $id_offre, $nom_offre, $description_offre, $id_sous_theme, $sous_theme_offre, $nom_pro);
			while (mysqli_stmt_fetch($stmt)) {
				if(isset($sous_themes[$id_sous_theme])) {
					$sous_themes[$id_sous_theme]['nb']++;
				}else{
					$sous_themes[$id_sous_theme] = array('id'=>$id_sous_theme, 'titre'=>$sous_theme_offre, 'nb'=>1);
				}
				$offres[] = array('id'=>$id_offre, 'titre'=>$nom_offre, 'description'=>$description_offre, 'sous_theme'=>$id_sous_theme, 'nom_pro'=>$nom_pro);
			}
		}
	}else{
		echo "L'application a rencontré un problème technique. Merci de contacter l'administrateur du site via le formulaire avec le message d'erreur suivant : " . mysqli_error($conn);
        exit;
	}
	mysqli_stmt_close($stmt);
	return [$sous_themes, $offres];
}

function get_offre($id){
	
	global $conn;
	$sql = "SELECT `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, '%d/%m/%Y') AS date_debut, DATE_FORMAT(`fin_offre`, '%d/%m/%Y') AS date_fin, `theme_pere`.libelle_theme AS `theme_offre`, `theme_fils`.libelle_theme AS `sous_theme_offre`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `zone_selection_villes`, `nom_pro`  
		FROM `bsl_offre` 
		JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_offre`.id_professionnel 
		JOIN `bsl_theme` AS `theme_fils` ON `theme_fils`.id_theme=`bsl_offre`.id_sous_theme
		JOIN `bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`theme_fils`.id_theme_pere
		WHERE `actif_offre` = 1 AND `id_offre`= ? ";
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, 'i', $id);

	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_bind_result($stmt, $row['nom_offre'], $row['description_offre'], $row['date_debut'], $row['date_fin'], $row['theme_offre'], $row['sous_theme_offre'], $row['adresse_offre'], $row['code_postal_offre'], $row['ville_offre'], $row['code_insee_offre'], $row['courriel_offre'], $row['telephone_offre'], $row['site_web_offre'], $row['delai_offre'], $row['zone_selection_villes'], $row['nom_pro']);
		mysqli_stmt_fetch($stmt);

	}
	mysqli_stmt_close($stmt);
	return $row;
}

function create_demande($id_offre, $coord){
	
	global $conn;
	$sql_dmd = "INSERT INTO `bsl_demande`(`id_demande`, `date_demande`, `id_offre`, `contact_jeune`, `code_insee_jeune`, `profil`) VALUES (NULL, NOW(), ?, ?, ?, ?)";
	$stmt = mysqli_prepare($conn, $sql_dmd);
	$liste=liste_criteres(',');
	mysqli_stmt_bind_param($stmt, 'isss', $id_offre, $coord, $_SESSION['code_insee'], $liste);
	//******* section utile pour debugage
	/*$print_sql = $sql_dmd;
	foreach(array($id_offre, $coord, $_SESSION['code_insee'], $liste) as $term){
		$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
	}
	echo $print_sql;*/
	//******************
	mysqli_stmt_execute($stmt);
	$id = mysqli_stmt_insert_id($stmt);
	mysqli_stmt_close($stmt);
	return $id;
}