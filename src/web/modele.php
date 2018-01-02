<?php

//********* affichage des thèmes proposés en fonction de la ville choisie 
/* note : la requête vérifie actuellement s'il y a des professionnels actifs sur la commune indiquée, thème par thème (avec recherche sur toutes les strates géographiques : pays, région, département ou territoire). idéalement il faudrait faire la vérification au niveau des offres actives...*/
function get_themes_by_ville($code_insee){

	global $conn;

	$query = 'SELECT `id_theme` as `id`, `libelle_theme` as `libelle`, `actif_theme` as `actif`, MAX(`c`) as `nb` 
	FROM (
		SELECT DISTINCT `t`.`id_theme`, `t`.`libelle_theme`, 
		`t`.`actif_theme` , COUNT(`p`.id_professionnel) as `c`
		FROM `'.DB_PREFIX.'bsl_theme` AS `t`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes` AS `pt` ON `pt`.`id_theme`=`t`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `p`.`id_professionnel`=`pt`.`id_professionnel` AND `p`.`actif_pro`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `p`.`competence_geo`="territoire" AND `tr`.`id_territoire`=`p`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` AS `tv` ON `tv`.`id_territoire`=`tr`.`id_territoire` 
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` AS `dep` ON `p`.`competence_geo`="departemental" AND `dep`.`id_departement`=`p`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl__region` AS `reg` ON `p`.`competence_geo`="regional" AND `reg`.`id_region`=`p`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` as `depreg` ON `depreg`.`id_region`=`reg`.`id_region` 
		WHERE `id_theme_pere` IS NULL 
		AND (`p`.competence_geo="national" OR `tv`.`code_insee`=? OR `depreg`.`id_departement`=SUBSTR(?,1,2) OR `dep`.`id_departement`=SUBSTR(?,1,2)) 
		GROUP BY `t`.`id_theme`, `t`.`libelle_theme`, `t`.`actif_theme` 
		UNION
		SELECT DISTINCT `t2`.id_theme, `t2`.`libelle_theme`, `t2`.`actif_theme`, 0 as `c`
		FROM `'.DB_PREFIX.'bsl_theme` AS `t2`
		WHERE `id_theme_pere` IS NULL) as `s`
	GROUP BY `id_theme`, `libelle_theme`, `actif_theme`';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sss', $code_insee, $code_insee, $code_insee);
	/*
$print_sql = $query;
foreach(array($code_insee, $code_insee, $code_insee) as $term){
	$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
}
echo "<pre>".$print_sql."</pre>"; */
	
	$themes = query_get($stmt);
	return $themes;
}

//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville 
function get_ville($saisie){

	global $conn;

	//test si saisie avec le autocomplete (auquel cas ça se termine par des chiffres)
	if (is_numeric(substr($saisie, -3))) {
		$query = 'SELECT `nom_ville`, `v`.`code_insee`, GROUP_CONCAT(DISTINCT `v`.`code_postal` SEPARATOR ", ") AS `codes_postaux`, `tr`.`id_territoire`, `nom_territoire`
			FROM `'.DB_PREFIX.'bsl__ville` as `v`
			LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` AS `tv` ON `tv`.`code_insee` = `v`.`code_insee`
			LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `tv`.`id_territoire`=`tr`.`id_territoire` AND `actif_territoire`=1
			WHERE `nom_ville` LIKE ? AND `code_postal` LIKE ?
			GROUP BY `nom_ville`, `v`.`code_insee`';
		$stmt = mysqli_prepare($conn, $query);
		$ville = substr($saisie, 0, -6);
		$cp = substr($saisie, -5);
		mysqli_stmt_bind_param($stmt, 'ss', $ville, $cp);
		
	
$print_sql = $query;
foreach(array($ville, $cp) as $term){
	$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
}
echo "<!--<pre>".$print_sql."</pre>-->";

	} else {
		$query = 'SELECT `nom_ville`, `v`.`code_insee`, GROUP_CONCAT(DISTINCT `v`.`code_postal` SEPARATOR ", ") AS `codes_postaux`, `tr`.`id_territoire`, `nom_territoire` 
			FROM `'.DB_PREFIX.'bsl__ville` as `v`
			LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` AS `tv` ON `tv`.`code_insee` = `v`.`code_insee`
			LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `tv`.`id_territoire`=`tr`.`id_territoire` AND `actif_territoire`=1
			WHERE `nom_ville` LIKE ? 
			GROUP BY `nom_ville`, `v`.`code_insee`';
		$stmt = mysqli_prepare($conn, $query);
		$saisie_insee = format_insee($saisie) . '%';
		mysqli_stmt_bind_param($stmt, 's', $saisie_insee);
	}
	
	$row = query_get($stmt);
	return $row;
}

//************ récupération des éléments de la page du formulaire 
function get_formulaire($besoin, $etape, $id_territoire=0){
	
	global $conn;

	$query = 'SELECT `f`.`id_formulaire`, `f`.`nb_pages`, `fp`.`titre`, `fp`.`ordre` AS `ordre_page`, `fp`.`aide`, `fq`.`id_question`, `fq`.`libelle` AS `libelle_question`, `fq`.`html_name`, `fq`.`type`, `fq`.`taille`, `fq`.`obligatoire`, `fv`.`libelle`, `fv`.`valeur`, `fv`.`defaut` 
		FROM `'.DB_PREFIX.'bsl_formulaire` AS `f`
		JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.`id_theme`=`f`.`id_theme`
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` AS `fp` ON `fp`.`id_formulaire`=`f`.`id_formulaire` AND `fp`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` AS `fq` ON `fq`.`id_page`=`fp`.`id_page` AND `fq`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` AS `fr` ON `fr`.`id_reponse`=`fq`.`id_reponse`
		JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` AS `fv` ON `fv`.`id_reponse`=`fr`.`id_reponse` AND `fv`.`actif`=1
		WHERE `f`.`actif`=1 AND `t`.`libelle_theme`= ? AND `fp`.`ordre` = ? AND (`f`.`id_territoire`=? OR `f`.`id_territoire`=0)
		ORDER BY `f`.`id_territoire` DESC, `ordre_page`, `fq`.`ordre`, `fv`.`ordre`';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sii', $besoin, $etape, $id_territoire);
	
$print_sql = $query;
foreach(array($besoin, $etape, $id_territoire) as $term){
	$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
}
echo "<!--<pre>".$print_sql."</pre>-->";

	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_formulaire, $nb_pages, $titre, $ordre_page, $aide, $idq, $question, $name, $type, $taille, $obligatoire, $libelle, $valeur, $defaut);
	$tmp_id = null;
	$tmp_que = null;
	$meta = [];
	$questions = [];
	$reponses = [];
	while (mysqli_stmt_fetch($stmt)) {
		if (is_null($tmp_id)) { //on récupère pour commencer les données de la page de formulaire
			$meta = array('id' => $id_formulaire, 'nb' => $nb_pages, 'titre' => $titre, 'etape' => $ordre_page, 'aide' => $aide, 'suite' => ($ordre_page < $nb_pages) ? ($ordre_page + 1) : 'fin');
			$tmp_id = $id_formulaire;
		}
		if($id_formulaire == $tmp_id) { //puis on récupère les questions et réponses du formulaire en question
			if ($question != $tmp_que) {
				$questions[] = array('id' => $idq, 'que' => $question, 'name' => $name, 'type' => $type, 'tai' => $taille, 'obl' => $obligatoire);
				$tmp_que = $question;
			}
			$reponses[$idq][] = array('name' => $name, 'lib' => $libelle, 'val' => $valeur, 'def' => $defaut);  //on récupère les réponses
		}else{
			break; //au cas où 2 formulaires (territoire et national), on arrête après la lecture du formulaire territorial
		}
	}
	mysqli_stmt_close($stmt);
	
	//on récupère le nom des autres pages pour construire le fil d'ariane
	$query = 'SELECT `fp`.`titre`, `fp`.`ordre`
		FROM `'.DB_PREFIX.'bsl_formulaire__page` AS `fp`
		JOIN `'.DB_PREFIX.'bsl_formulaire` AS `f` ON `fp`.`id_formulaire`=`f`.`id_formulaire` AND `f`.`actif`=1 
		JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.`id_theme`=`f`.`id_theme`
		WHERE `fp`.`actif`=1 AND `t`.`libelle_theme`= ? AND (`f`.`id_territoire`=? OR `f`.`id_territoire`=0)
		ORDER BY `f`.`id_territoire` DESC, `ordre`';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'si', $besoin, $id_territoire);
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
	
	$query = 'SELECT `id_offre`, `nom_offre`, `description_offre`, `t`.`id_sous_theme`, `theme`.`libelle_theme` AS `sous_theme_offre`, `nom_pro`, `ville_offre`, `delai_offre` /*`t`.*, `theme`.`libelle_theme` AS `sous_theme_offre`, `theme_pere`.`libelle_theme` AS `theme_offre` */
	FROM ( SELECT `o`.*,   /* on construit ici la liste des critères */
		GROUP_CONCAT( if(nom_critere= "age_min", valeur_critere, NULL ) SEPARATOR "|") `age_min`, 
		GROUP_CONCAT( if(nom_critere= "age_max", valeur_critere, NULL ) SEPARATOR "|") `age_max`, 
		GROUP_CONCAT( if(nom_critere= "villes", valeur_critere, NULL ) SEPARATOR "|") `villes` ';
	foreach ($criteres as $cle => $valeur) { //on va chercher les critères saisis dans le formulaire
		$c_cle = securite_bdd($conn, $cle);
		$query .= ', GROUP_CONCAT( if(nom_critere= "' . $c_cle . '", valeur_critere, NULL ) SEPARATOR "|") "' . $c_cle . '"';
	}
	$query .= ' FROM `'.DB_PREFIX.'bsl_offre_criteres` AS `oc`
		JOIN `'.DB_PREFIX.'bsl_offre` AS `o` ON `o`.`id_offre`=`oc`.`id_offre`
		WHERE `o`.`actif_offre` = 1 
		GROUP BY `oc`.`id_offre`
		) as `t`
	JOIN `'.DB_PREFIX.'bsl_theme` AS `theme` ON `theme`.`id_theme`=`t`.`id_sous_theme`
	JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_pere` ON `theme_pere`.`id_theme`=`theme`.`id_theme_pere`
	JOIN `'.DB_PREFIX.'bsl_professionnel` AS `pro` ON `pro`.id_professionnel=`t`.`id_professionnel` /* s il n y a pas une liste de villes propre à l offre (zone_selection_villes=0), alors il faut aller chercher celles du pro, d où les jointures en dessous ↓ */
	LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_villes` AS `pv` ON `pro`.`id_professionnel`=`pv`.`id_professionnel` AND `pro`.`zone_selection_villes`=1 AND `pv`.`code_insee` = ?
	LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `t`.`zone_selection_villes`=0 AND `pro`.`competence_geo`="territoire" AND `tr`.`id_territoire`=`pro`.`id_competence_geo` 
	LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` AS `tv` ON `tv`.`id_territoire`=`tr`.`id_territoire` AND `tv`.`code_insee` = ?
	LEFT JOIN `'.DB_PREFIX.'bsl__departement` AS `dep` ON `pro`.`competence_geo`="departemental" AND `dep`.`id_departement`=`pro`.`id_competence_geo`
	LEFT JOIN `'.DB_PREFIX.'bsl__region` AS `reg` ON `pro`.`competence_geo`="regional" AND `reg`.`id_region`=`pro`.`id_competence_geo`
	LEFT JOIN `'.DB_PREFIX.'bsl__departement` as `depreg` ON `depreg`.`id_region`=`reg`.`id_region`

	WHERE `t`.`debut_offre` <= CURDATE() AND `t`.`fin_offre` >= CURDATE() 
	AND `pro`.`actif_pro` = 1
	AND `theme_pere`.`libelle_theme` = ? 
	/* recherche géographique ! */
	AND ((`t`.`zone_selection_villes`=1 AND `t`.`villes` LIKE ?) /* si l offre a une liste de villes personnalisée */
		OR (`pro`.`zone_selection_villes`=1 AND `pv`.`code_insee` = ?) /* si le pro a une liste de villes personnalisée */
		OR (`t`.`zone_selection_villes`=0 AND ( /* sinon il faut chercher dans la zone de compétence du pro */
			`pro`.`competence_geo` = "national"
			OR `tv`.`code_insee` = ?
			OR `dep`.`id_departement` = SUBSTR(?,1,2) 
			OR `depreg`.`id_departement` = SUBSTR(?,1,2)
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
	$query .= ' ORDER BY `theme`.`ordre_theme`, RAND()'; //le RAND permet de ne pas afficher toujours les mêmes offres en premier... en attendant un meilleur critère de tri

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
	
	$id_recherche = (!isset($_SESSION['web']['recherche_id'])) ? create_recherche($nb_offres) : null;
	
	return [$sous_themes, $offres, $id_recherche];
}

function get_offre($id){

	global $conn;
	$row = null;
	
	$query = 'SELECT `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, "%d/%m/%Y") AS date_debut, DATE_FORMAT(`fin_offre`, "%d/%m/%Y") AS date_fin, `theme_pere`.libelle_theme AS `theme_offre`, `theme_fils`.libelle_theme AS `sous_theme_offre`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `o`.`zone_selection_villes`, `nom_pro`, `visibilite_coordonnees`
		FROM `'.DB_PREFIX.'bsl_offre` AS `o`
		JOIN `'.DB_PREFIX.'bsl_professionnel` AS `pro` ON `pro`.id_professionnel=`o`.id_professionnel 
		JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_fils` ON `theme_fils`.id_theme=`o`.id_sous_theme
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
	$criteres = json_encode($_SESSION['web']['critere'], JSON_UNESCAPED_SLASHES); //façon de charger le tableau simplement en base de données
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sssi', $_SESSION['web']['code_insee'], $_SESSION['web']['besoin'], $criteres, $nb);
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