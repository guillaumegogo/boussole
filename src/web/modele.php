<?php

//********* affichage des thèmes proposés en fonction de la ville choisie 
/* note : la requête vérifie actuellement s'il y a des professionnels actifs sur la commune indiquée, thème par thème (avec recherche sur toutes les strates géographiques : pays, région, département ou territoire). idéalement il faudrait faire la vérification au niveau des offres actives...*/
function get_themes_by_ville($code_insee){

	global $conn;

	$query = 'SELECT `id_theme` as `id`, `libelle_theme` as `libelle`, `actif_theme` as `actif`, MAX(`c`) as `nb` 
	FROM (
		SELECT DISTINCT `'.DB_PREFIX.'bsl_theme`.`id_theme`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme`, 
		`'.DB_PREFIX.'bsl_theme`.`actif_theme` , COUNT(`'.DB_PREFIX.'bsl_professionnel`.id_professionnel) as `c`
		FROM `'.DB_PREFIX.'bsl_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes` ON `'.DB_PREFIX.'bsl_professionnel_themes`.`id_theme`=`'.DB_PREFIX.'bsl_theme`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`=`'.DB_PREFIX.'bsl_professionnel_themes`.`id_professionnel` AND `'.DB_PREFIX.'bsl_professionnel`.`actif_pro`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` ON `'.DB_PREFIX.'bsl_territoire_villes`.`id_territoire`=`'.DB_PREFIX.'bsl_territoire`.`id_territoire` 
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` as `'.DB_PREFIX.'bsl__departement_region` ON `'.DB_PREFIX.'bsl__departement_region`.`id_region`=`'.DB_PREFIX.'bsl__region`.`id_region` 
		WHERE `id_theme_pere` IS NULL 
		AND (`'.DB_PREFIX.'bsl_professionnel`.competence_geo="national" OR `'.DB_PREFIX.'bsl_territoire_villes`.`code_insee`=? OR `'.DB_PREFIX.'bsl__departement_region`.`id_departement`=SUBSTR(?,1,2) OR `'.DB_PREFIX.'bsl__departement`.`id_departement`=SUBSTR(?,1,2)) 
		GROUP BY `'.DB_PREFIX.'bsl_theme`.`id_theme`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme`, `'.DB_PREFIX.'bsl_theme`.`actif_theme` 
		UNION
		SELECT DISTINCT `'.DB_PREFIX.'bsl_theme`.id_theme, `'.DB_PREFIX.'bsl_theme`.`libelle_theme`, `'.DB_PREFIX.'bsl_theme`.`actif_theme`, 0 as `c`
		FROM `'.DB_PREFIX.'bsl_theme`
		WHERE `id_theme_pere` IS NULL) as `t`
	GROUP BY `id_theme`, `libelle_theme`, `actif_theme`';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sss', $code_insee, $code_insee, $code_insee);
	
	$themes = query_get($stmt);
	return $themes;
}

//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville 
function get_ville($saisie){

	global $conn;

	//test si saisie avec le autocomplete (auquel cas ça se termine par des chiffres)
	if (is_numeric(substr($saisie, -3))) {
		$query = 'SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ", ") AS `codes_postaux` 
			FROM `'.DB_PREFIX.'bsl__ville` 
			WHERE `nom_ville` LIKE ? AND `code_postal` LIKE ?
			GROUP BY `nom_ville`, `code_insee`';
		$stmt = mysqli_prepare($conn, $query);
		$ville = substr($saisie, 0, -6);
		$cp = substr($saisie, -5);
		mysqli_stmt_bind_param($stmt, 'ss', $ville, $cp);

	} else {
		$query = 'SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ", ") AS `codes_postaux` 
			FROM `'.DB_PREFIX.'bsl__ville` 
			WHERE `nom_ville` LIKE ?
			GROUP BY `nom_ville`, `code_insee`';
		$stmt = mysqli_prepare($conn, $query);
		$saisie_insee = format_insee($saisie) . '%';
		mysqli_stmt_bind_param($stmt, 's', $saisie_insee);
	}
	
	$row = query_get($stmt);
	return $row;
}

//************ récupération des éléments de la page du formulaire
function get_formulaire($besoin, $etape){
	
	global $conn;

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_formulaire`.`nb_pages`, `'.DB_PREFIX.'bsl_formulaire__page`.`titre`, 
		`'.DB_PREFIX.'bsl_formulaire__page`.`ordre` AS `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__page`.`aide`, 
		`'.DB_PREFIX.'bsl_formulaire__question`.`id_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`, 
		`'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`, `'.DB_PREFIX.'bsl_formulaire__question`.`taille`, 
		`'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`libelle`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur`, 
		`'.DB_PREFIX.'bsl_formulaire__valeur`.`defaut` FROM `'.DB_PREFIX.'bsl_formulaire` 
		JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page` AND `'.DB_PREFIX.'bsl_formulaire__question`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` ON `'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__question`.`id_reponse`
		JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` ON `'.DB_PREFIX.'bsl_formulaire__valeur`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse` AND `'.DB_PREFIX.'bsl_formulaire__valeur`.`actif`=1
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`actif`=1 AND `'.DB_PREFIX.'bsl_theme`.`libelle_theme`= ? AND `'.DB_PREFIX.'bsl_formulaire__page`.`ordre` = ?
		ORDER BY `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`ordre`';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'si', $besoin, $etape);

	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_formulaire, $nb_pages, $titre, $ordre_page, $aide, $idq, $question, $name, $type, $taille, $obligatoire, $libelle, $valeur, $defaut);
	$tmp_id = 0;
	$tmp_que = '';
	$meta = [];
	$questions = [];
	$reponses = [];
	while (mysqli_stmt_fetch($stmt)) {
		if ($id_formulaire != $tmp_id) { //on récupère les données de la page de formulaire
			$meta = array('id' => $id_formulaire, 'nb' => $nb_pages, 'titre' => $titre, 'etape' => $ordre_page, 'aide' => $aide, 'suite' => ($ordre_page < $nb_pages) ? ($ordre_page + 1) : 'fin');
			$tmp_id = $id_formulaire;
		}
		if ($question != $tmp_que) { //on récupère les questions
			$questions[] = array('id' => $idq, 'que' => $question, 'name' => $name, 'type' => $type, 'tai' => $taille, 'obl' => $obligatoire);
			$tmp_que = $question;
		}
		$reponses[$idq][] = array('name' => $name, 'lib' => $libelle, 'val' => $valeur, 'def' => $defaut);  //on récupère les réponses
	}
	mysqli_stmt_close($stmt);
	
	//on récupère le nom des autres pages pour construire le fil d'ariane
	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire__page`.`titre`, `'.DB_PREFIX.'bsl_formulaire__page`.`ordre`
		FROM `'.DB_PREFIX.'bsl_formulaire__page` 
		JOIN `'.DB_PREFIX.'bsl_formulaire` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
		WHERE `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1 AND `'.DB_PREFIX.'bsl_theme`.`libelle_theme`= ? 
		ORDER BY `ordre`';		
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 's', $besoin);
	$liste_pages = query_get($stmt);
	
	return [$meta, $questions, $reponses, $liste_pages];
}

//************ construction de LA requête
/* note: on cherche les offres actives dont le code_insee :
- est en critère de l'offre (`t`.`villes` LIKE...)
- est dans la sélection de villes du pro
- est dans le territoire / le département / la région du pro
*/
function get_offres_demande($criteres, $types, $besoin, $code_insee){

	global $conn;
	
	$query = 'SELECT `id_offre`, `nom_offre`, `description_offre`, `t`.`id_sous_theme`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme` AS `sous_theme_offre`, `nom_pro`, `ville_offre`, `delai_offre` /*`t`.*, `'.DB_PREFIX.'bsl_theme`.`libelle_theme` AS `sous_theme_offre`, `theme_pere`.`libelle_theme` AS `theme_offre` */
	FROM ( SELECT `'.DB_PREFIX.'bsl_offre`.*,   /* on construit ici la liste des critères */
		GROUP_CONCAT( if(nom_critere= "age_min", valeur_critere, NULL ) SEPARATOR "|") `age_min`, 
		GROUP_CONCAT( if(nom_critere= "age_max", valeur_critere, NULL ) SEPARATOR "|") `age_max`, 
		GROUP_CONCAT( if(nom_critere= "villes", valeur_critere, NULL ) SEPARATOR "|") `villes` ';
	foreach ($criteres as $cle => $valeur) { //on va chercher les critères saisis dans le formulaire
		$c_cle = securite_bdd($conn, $cle);
		$query .= ', GROUP_CONCAT( if(nom_critere= "' . $c_cle . '", valeur_critere, NULL ) SEPARATOR "|") "' . $c_cle . '"';
	}
	$query .= ' FROM `'.DB_PREFIX.'bsl_offre_criteres`
		JOIN `'.DB_PREFIX.'bsl_offre` ON `'.DB_PREFIX.'bsl_offre`.`id_offre`=`'.DB_PREFIX.'bsl_offre_criteres`.`id_offre`
		WHERE `'.DB_PREFIX.'bsl_offre`.`actif_offre` = 1 
		GROUP BY `'.DB_PREFIX.'bsl_offre_criteres`.`id_offre`
		) as `t`
	JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`t`.`id_sous_theme`
	JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_pere` ON `theme_pere`.`id_theme`=`'.DB_PREFIX.'bsl_theme`.`id_theme_pere`
	JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`t`.`id_professionnel` /* s il n y a pas une liste de villes propre à l offre (zone_selection_villes=0), alors il faut aller chercher celles du pro, d où les jointures en dessous ↓ */
	LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_villes` ON `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`=`'.DB_PREFIX.'bsl_professionnel_villes`.`id_professionnel` AND `'.DB_PREFIX.'bsl_professionnel`.`zone_selection_villes`=1 AND `'.DB_PREFIX.'bsl_professionnel_villes`.`code_insee` = ?
	LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `t`.`zone_selection_villes`=0 AND `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
	LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` ON `'.DB_PREFIX.'bsl_territoire_villes`.`id_territoire`=`'.DB_PREFIX.'bsl_territoire`.`id_territoire` AND `'.DB_PREFIX.'bsl_territoire_villes`.`code_insee` = ?
	LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `'.DB_PREFIX.'bsl__departement` as `'.DB_PREFIX.'bsl__departement_region` ON `'.DB_PREFIX.'bsl__departement_region`.`id_region`=`'.DB_PREFIX.'bsl__region`.`id_region`

	WHERE `t`.`debut_offre` <= CURDATE() AND `t`.`fin_offre` >= CURDATE() 
	AND `'.DB_PREFIX.'bsl_professionnel`.`actif_pro` = 1
	AND `theme_pere`.`libelle_theme` = ? 
	/* recherche géographique ! */
	AND ((`t`.`zone_selection_villes`=1 AND `t`.`villes` LIKE ?) /* si l offre a une liste de villes personnalisée */
		OR (`'.DB_PREFIX.'bsl_professionnel`.`zone_selection_villes`=1 AND `'.DB_PREFIX.'bsl_professionnel_villes`.`code_insee` = ?) /* si le pro a une liste de villes personnalisée */
		OR (`t`.`zone_selection_villes`=0 AND ( /* sinon il faut chercher dans la zone de compétence du pro */
			`'.DB_PREFIX.'bsl_professionnel`.`competence_geo` = "national"
			OR `'.DB_PREFIX.'bsl_territoire_villes`.`code_insee` = ?
			OR `'.DB_PREFIX.'bsl__departement`.`id_departement` = SUBSTR(?,1,2) 
			OR `'.DB_PREFIX.'bsl__departement_region`.`id_departement` = SUBSTR(?,1,2)
		)))';
	$terms = array($code_insee, $code_insee, $besoin, '%'.$code_insee.'%', $code_insee, $code_insee, $code_insee, $code_insee);
	$terms_type = "ssssssss";

	//foreach sur les criteres, et en fonction du type on construit la requete...
	foreach ($criteres as $cle => $valeur) {
		$c_cle = securite_bdd($conn, $cle);
		if (isset($types[$cle])) {
			switch ($types[$cle]) {
				case 'select':
				case 'radio':
					$query .= ' AND `t`.`'.$c_cle.'` LIKE ? ';
					$terms[] = '%' . $valeur . '%';
					$terms_type .= "s";
					break;
				case 'multiple':
				case 'checkbox':
					$sql = '';
					foreach ($criteres[$cle] as $selected_option) {
						$sql .= ' `t`.`'.$c_cle.'` LIKE ? OR';
						$terms[] = '%' . $selected_option . '%';
						$terms_type .= "s";
					}
					$query .= ' AND (' . $sql . ' FALSE)';
					break;
			}
		}
	}
	$query .= ' ORDER BY `'.DB_PREFIX.'bsl_theme`.`ordre_theme`, RAND()'; //le RAND permet de ne pas afficher toujours les mêmes offres en premier... en attendant un meilleur critère de tri

if (DEBUG) {
	$print_sql = $query;
	foreach($terms as $term){
		$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
	}
	echo "<!--".$print_sql."-->"; 
}

	$stmt = query_prepare($query,$terms,$terms_type);
	check_mysql_error($conn);

	$sous_themes = [];
	$offres = [];
	$nb_offres = 0;
	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_bind_result($stmt, $id_offre, $nom_offre, $description_offre, $id_sous_theme, $sous_theme_offre, $nom_pro, $ville, $delai);
		
		while (mysqli_stmt_fetch($stmt)) {
			if (!isset($sous_themes[$id_sous_theme])) {
				$sous_themes[$id_sous_theme] = $sous_theme_offre;
			}
			$offres[$id_sous_theme][] = array('id' => $id_offre, 'titre' => $nom_offre, 'description' => $description_offre, 'nom_pro' => $nom_pro, 'ville' => $ville, 'delai' => $delai);
			$nb_offres ++;
		}
	} else {
		throw new Exception("L'application a rencontré un problème technique. Merci de contacter l'administrateur du site avec le message d'erreur suivant : " . mysqli_error($conn));
	}

	mysqli_stmt_close($stmt);
	
	$id_recherche = (!isset($_SESSION['recherche_id'])) ? create_recherche($nb_offres) : null;
	
	return [$sous_themes, $offres, $id_recherche];
}

function get_offre($id){

	global $conn;
	$row = null;
	
	$query = 'SELECT `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, "%d/%m/%Y") AS date_debut, DATE_FORMAT(`fin_offre`, "%d/%m/%Y") AS date_fin, `theme_pere`.libelle_theme AS `theme_offre`, `theme_fils`.libelle_theme AS `sous_theme_offre`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `'.DB_PREFIX.'bsl_offre`.`zone_selection_villes`, `nom_pro`, `visibilite_coordonnees`
		FROM `'.DB_PREFIX.'bsl_offre` 
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`'.DB_PREFIX.'bsl_offre`.id_professionnel 
		JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_fils` ON `theme_fils`.id_theme=`'.DB_PREFIX.'bsl_offre`.id_sous_theme
		JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`theme_fils`.id_theme_pere
		WHERE `actif_offre` = 1 AND `id_offre`= ? ';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
		}
		mysqli_stmt_close($stmt);
	}
	return $row;
}

function create_recherche($nb){

	global $conn;
	
	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_recherche`(`date_recherche`, `code_insee`, `besoin`, `criteres`, `nb_offres`)
		VALUES (NOW(), ?, ?, ?, ?)';
	$criteres = json_encode($_SESSION['critere'], JSON_UNESCAPED_SLASHES); //façon de charger le tableau simplement en base de données
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sssi', $_SESSION['code_insee'], $_SESSION['besoin'], $criteres, $nb);
	check_mysql_error($conn);

	if (mysqli_stmt_execute($stmt)) {
		$id = mysqli_stmt_insert_id($stmt);
		mysqli_stmt_close($stmt);
	}
	return $id;
}

function create_demande($id_offre, $coordonnees, $id_recherche=null){

	global $conn;
	$id = null;
	$token = hash('sha256', $coordonnees . time() . rand(0, 1000000));
	
	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_demande`(`date_demande`, `id_offre`, `contact_jeune`, `id_recherche`, `id_hashe`) 
		VALUES (NOW(), ?, ?, ?, ?)';
	
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'isis', $id_offre, $coordonnees, $id_recherche, $token);
	check_mysql_error($conn);

	if (mysqli_stmt_execute($stmt)) {
		$id = mysqli_stmt_insert_id($stmt);
		mysqli_stmt_close($stmt);
	}
	return [$id, $token];
}