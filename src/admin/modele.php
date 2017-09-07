<?php

function get_nb_nouvelles_demandes() {

	global $conn;
	$nb = 0;

	$query = 'SELECT count(`id_demande`) AS nb
		FROM `'.DB_PREFIX.'bsl_demande`
		WHERE date_traitement IS NULL';

	$stmt = mysqli_prepare($conn, $query);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			$nb = (int)$row['nb'];
		}
		mysqli_stmt_close($stmt);
	}

	return $nb;
}

function get_criteres($id_offre, $id_theme){

	global $conn;

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, 
		`'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`,
		`'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`,
		`'.DB_PREFIX.'bsl_formulaire__question`.`taille`, `'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire`,
		`'.DB_PREFIX.'bsl_formulaire__valeur`.`libelle`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur`,
		`'.DB_PREFIX.'bsl_offre_criteres`.`id_offre` FROM `'.DB_PREFIX.'bsl_formulaire`
		JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page` AND `'.DB_PREFIX.'bsl_formulaire__question`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` ON `'.DB_PREFIX.'bsl_formulaire__valeur`.`id_question`=`'.DB_PREFIX.'bsl_formulaire__question`.`id_question` AND `'.DB_PREFIX.'bsl_formulaire__valeur`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_offre_criteres` ON `'.DB_PREFIX.'bsl_offre_criteres`.`nom_critere`=`'.DB_PREFIX.'bsl_formulaire__question`.`html_name` AND `'.DB_PREFIX.'bsl_offre_criteres`.`valeur_critere`=`'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur` AND `'.DB_PREFIX.'bsl_offre_criteres`.`id_offre`= ?
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`actif`=1 AND `'.DB_PREFIX.'bsl_theme`.`id_theme`= ?
		ORDER BY `'.DB_PREFIX.'bsl_formulaire__page`.`ordre`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`ordre`';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ii', $id_offre, $id_theme);
	check_mysql_error($conn);
	
	$questions = [];
	$reponses = [];
	$tmp_que = '';
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_assoc($result)) {
			//on récupère les questions
			if ($row['libelle_question'] != $tmp_que) { 
				$questions[] = array('libelle' => $row['libelle_question'], 'name' => $row['html_name'], 'type' => $row['type'], 'obligatoire' => $row['obligatoire']);
				$tmp_que = $row['libelle_question'];
			}
			//on récupère les réponses
			$reponses[$row['html_name']][] = array('libelle' => $row['libelle'], 'valeur' => $row['valeur'], 'selectionne' => ($row['id_offre']) ? 'selected' : ''); 
		}
		mysqli_stmt_close($stmt);
	}
	return [$questions, $reponses];
}

/* Demandes */
function get_demande_by_id($id){

	global $conn;
	$demande = null;

	$query = 'SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, `commentaire`,
		`'.DB_PREFIX.'bsl_offre`.nom_offre, `'.DB_PREFIX.'bsl_professionnel`.nom_pro
		FROM `'.DB_PREFIX.'bsl_demande`
		JOIN `'.DB_PREFIX.'bsl_offre` ON `'.DB_PREFIX.'bsl_offre`.id_offre=`'.DB_PREFIX.'bsl_demande`.id_offre
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_offre`.id_professionnel=`'.DB_PREFIX.'bsl_professionnel`.id_professionnel
		WHERE id_demande = ?';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$demande = mysqli_fetch_assoc($result);
		}
		mysqli_stmt_close($stmt);
	}

	return $demande;
}

function get_liste_demandes($flag_traite, $territoire_id = null, $user_pro_id = null){

	global $conn;
	$demandes = [];
	$params = [];
	$types = '';

	$query = 'SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, `'.DB_PREFIX.'bsl_offre`.nom_offre,
		`'.DB_PREFIX.'bsl_offre`.id_professionnel, `'.DB_PREFIX.'bsl_professionnel`.nom_pro
		FROM `'.DB_PREFIX.'bsl_demande`
		JOIN `'.DB_PREFIX.'bsl_offre` ON `'.DB_PREFIX.'bsl_offre`.id_offre = `'.DB_PREFIX.'bsl_demande`.id_offre
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_offre`.id_professionnel = `'.DB_PREFIX.'bsl_professionnel`.id_professionnel
		WHERE 1 ';
	if ($flag_traite) {
		$query .= "AND date_traitement IS NOT NULL ";
	} else {
		$query .= "AND date_traitement IS NULL ";
	}
	if ((int) $territoire_id > 0) {
		$query .= 'AND `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`= ?';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if ((int) $user_pro_id > 0) {
		$query .= 'AND `'.DB_PREFIX.'bsl_offre`.id_professionnel = ? ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= ' ORDER BY date_demande DESC';

	$stmt = mysqli_prepare($conn, $query);
	if(count($params) > 0) {
		$query_params = [];
		$query_params[] = $types;
		foreach ($params as $id => $term) {
			$query_params[] = &$params[$id];
		}
		call_user_func_array(array($stmt, 'bind_param'), $query_params);
	}
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($demande = mysqli_fetch_assoc($result)) {
			$demandes[] = $demande;
		}
		mysqli_stmt_close($stmt);
	}

	return $demandes;
}

function update_demande($id, $commentaire, $user_id){

	global $conn;
	$updated = false;

	$query = 'UPDATE `'.DB_PREFIX.'bsl_demande`
		SET `date_traitement` = NOW(),
		`commentaire` = ?,
		`user_derniere_modif`= ?
		WHERE `'.DB_PREFIX.'bsl_demande`.`id_demande` = ?';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sii', $commentaire, $user_id, $id);
	check_mysql_error($conn);

	if (mysqli_stmt_execute($stmt)) {
		$updated = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	return $updated;
}

/* Offres */
function create_offre($nom, $desc, $date_debut, $date_fin, $pro_id, $user_id) {

	global $conn;
	$created = false;

	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_offre`(`nom_offre`, `description_offre`, `debut_offre`, `fin_offre`, `id_professionnel`,
		`adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`,
		`telephone_offre`, `site_web_offre`, `delai_offre`, `user_derniere_modif`)
		SELECT ?, ?, ?, ?, ?,`adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`,`courriel_pro`,
		`telephone_pro`, `site_web_pro`, `delai_pro`, ?
		FROM `'.DB_PREFIX.'bsl_professionnel`
		WHERE `'.DB_PREFIX.'bsl_professionnel`.id_professionnel = ? ';

	$stmt = mysqli_prepare($conn, $query);
	$date_d = date('Y-m-d', strtotime(str_replace('/', '-', $date_debut)));
	$date_f = date('Y-m-d', strtotime(str_replace('/', '-', $date_fin)));
	mysqli_stmt_bind_param($stmt, 'ssssiii', $nom, $desc, $date_d, $date_f, $pro_id, $user_id, $pro_id);
	check_mysql_error($conn);

	if (mysqli_stmt_execute($stmt)) {
		$created = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	return $created;
}

function update_offre($id_offre, $nom, $desc, $date_debut, $date_fin, $sous_theme, $adresse, $code_postal, $ville, $courriel, $tel, $url, $delai, $zone, $tab_villes, $actif, $user_id){

	global $conn;
	$updated = false;
	$updated_v = false;
	$code_insee = '';

	$query = 'SELECT code_insee
		FROM `'.DB_PREFIX.'bsl__ville`
		WHERE code_postal= ? AND nom_ville LIKE ?';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ss', $code_postal, $ville);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			$code_insee = $row['code_insee'];
		}
		mysqli_stmt_close($stmt);
	}

	if (substr($url, 0, 3) == 'www') {
		$url = 'http://' . $url;
	}
	$date_d = date('Y-m-d', strtotime(str_replace('/', '-', $date_debut)));
	$date_f = date('Y-m-d', strtotime(str_replace('/', '-', $date_fin)));

	$req = 'UPDATE `'.DB_PREFIX.'bsl_offre`
		SET `nom_offre` = ?, `description_offre`= ?, `debut_offre` = ?, `fin_offre` = ?,
		`id_sous_theme` = ?, `adresse_offre` = ?, `code_postal_offre`= ?, `ville_offre`= ?, `code_insee_offre`= ?,
		`courriel_offre` = ?, `telephone_offre` = ?, `site_web_offre` = ?, `delai_offre` = ?,
		`zone_selection_villes` = ?, `actif_offre` = ?, `user_derniere_modif` = ?
		WHERE `id_offre` = ?';

	$stmt = mysqli_prepare($conn, $req);
	mysqli_stmt_bind_param($stmt, 'ssssisssssssiiiii', $nom, $desc, $date_d, $date_f, $sous_theme, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $url, $delai, $zone, $actif, $user_id, $id_offre);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$updated = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	if (isset($tab_villes)) {
		$params = [];
		$types = '';
		$req2 = 'INSERT INTO `'.DB_PREFIX.'bsl_offre_criteres` (`id_offre`, `nom_critere`, `valeur_critere`) 
			VALUES ';
		foreach ($tab_villes as $selected_option) {
			$req2 .= '( ?, "villes", ? ), ';
			$params[] = (int) $id_offre;
			$params[] = $selected_option;
			$types .= 'is';
		}
		$req2 = substr($req2, 0, -2); //on enlève ", " à la fin de la requête
		$stmt = mysqli_prepare($conn, $req2);
		if(count($params) > 0) {
			$query_params = [];
			$query_params[] = $types;
			foreach ($params as $id => $term) {
				$query_params[] = &$params[$id];
			}
			call_user_func_array(array($stmt, 'bind_param'), $query_params);
		}
		check_mysql_error($conn);
		if (mysqli_stmt_execute($stmt)) {
			$updated_v = mysqli_stmt_affected_rows($stmt) > 0;
			mysqli_stmt_close($stmt);
		}
	}

	return [$updated, updated_v];
}

function update_criteres_offre($id, $tab_criteres, $user_id) { 

	global $conn;
	$updated = false;

	$query1 = 'DELETE FROM `'.DB_PREFIX.'bsl_offre_criteres` WHERE `id_offre` = ?';
	$stmt = mysqli_prepare($conn, $query1);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	check_mysql_error($conn);

	if (isset($tab_criteres)) {
		$query2 = 'INSERT INTO `'.DB_PREFIX.'bsl_offre_criteres` (`id_offre`, `nom_critere`, `valeur_critere`) 
			VALUES ';
		$params = [];
		$types = '';
		foreach ($tab_criteres as $name => $tab_critere) {
			$query2 .= '( ?, ? , ? ), ';
			$params[] = (int) $id;
			$params[] = $name;
			$params[] = $selected_option;
			$types .= 'iss';
		}
		$query2 = substr($query2, 0, -2); //on enlève ", " à la fin de la requête
		$stmt = mysqli_prepare($conn, $query2);
		if(count($params) > 0) {
			$query_params = [];
			$query_params[] = $types;
			foreach ($params as $id => $term) {
				$query_params[] = &$params[$id];
			}
			call_user_func_array(array($stmt, 'bind_param'), $query_params);
		}
		check_mysql_error($conn);
		if (mysqli_stmt_execute($stmt)) {
			$updated = mysqli_stmt_affected_rows($stmt) > 0;
			mysqli_stmt_close($stmt);
		}
	}
	
	return $updated;
}

function get_themes_by_pro($id){

	global $conn;
	$themes = null;

	$query = 'SELECT `'.DB_PREFIX.'bsl_theme`.`id_theme`, `libelle_theme`,`id_professionnel`, `id_theme_pere`
		FROM `'.DB_PREFIX.'bsl_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes`
		ON `'.DB_PREFIX.'bsl_professionnel_themes`.`id_theme`=`'.DB_PREFIX.'bsl_theme`.`id_theme`
		WHERE `actif_theme` = 1 AND (id_professionnel IS NULL OR id_professionnel= ?)';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($theme = mysqli_fetch_assoc($result)) {
			$themes[] = $theme;
		}
		mysqli_stmt_close($stmt);
	}
	return $themes;
}

function get_villes_by_competence_geo($competence, $id) {

	global $conn;
	$villes = null;

	$query = 'SELECT `'.DB_PREFIX.'bsl__ville`.`code_insee`,
		MIN(`'.DB_PREFIX.'bsl__ville`.`code_postal`) AS cp, `'.DB_PREFIX.'bsl__ville`.`nom_ville`
		FROM `'.DB_PREFIX.'bsl__ville` ';
	switch ($competence) {
		case "territoire":
			$query .= ' JOIN `'.DB_PREFIX.'bsl_territoire_villes` ON `'.DB_PREFIX.'bsl_territoire_villes`.code_insee=`'.DB_PREFIX.'bsl__ville`.code_insee
			WHERE id_territoire= ? ';
			break;
		case "departemental":
			$query .= ' WHERE SUBSTR(`'.DB_PREFIX.'bsl__ville`.code_insee,1,2)= ? ';
			break;
		case "regional":
			$query .= ' JOIN `'.DB_PREFIX.'bsl__departement` ON SUBSTR(`'.DB_PREFIX.'bsl__ville`.code_insee,1,2)=`'.DB_PREFIX.'bsl__departement`.id_departement AND id_region= ? ';
			break;
		case "national":
			break;
	}
	$query .= 'GROUP BY `'.DB_PREFIX.'bsl__ville`.`code_insee`, `'.DB_PREFIX.'bsl__ville`.`nom_ville` ORDER BY nom_ville';

	$stmt = mysqli_prepare($conn, $query);
	if ($competence != "national"){
		mysqli_stmt_bind_param($stmt, 'i', $id);
	}
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($ville = mysqli_fetch_assoc($result)) {
			$villes[] = $ville;
		}
		mysqli_stmt_close($stmt);
	}
	return $villes;
}

function get_villes_by_offre($id) {

	global $conn;
	$villes = null;

	$query = 'SELECT * FROM `'.DB_PREFIX.'bsl_offre_criteres`
		JOIN `'.DB_PREFIX.'bsl__ville` ON valeur_critere=code_insee
		WHERE `nom_critere` LIKE "villes" AND id_offre= ?
		ORDER BY nom_ville';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($ville = mysqli_fetch_assoc($result)) {
			$villes[] = $ville;
		}
		mysqli_stmt_close($stmt);
	}
	return $villes;
}

function get_territoires($id = null) {

	global $conn;
	$t = null;

	$sql = 'SELECT `id_territoire`, `nom_territoire`, `code_territoire`
		FROM `'.DB_PREFIX.'bsl_territoire`
		WHERE `actif_territoire` = 1 AND `nom_territoire` != "" ';
	if (isset($id) && $id) {
		$sql .= ' AND `id_territoire`= ?';
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, 'i', $id);
	}else{
		$stmt = mysqli_prepare($conn, $sql);
	}
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_assoc($result)) {
			$t[] = $row;
		}
		mysqli_stmt_close($stmt);
	}
	return $t;
}

function get_liste_pros_select($territoire_id=null, $user_pro_id=null) {

	global $conn;
	$pros = null;
	$params = [];
	$types = '';

	$query = 'SELECT `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`, `nom_pro`
		FROM `'.DB_PREFIX.'bsl_professionnel`
		WHERE `actif_pro`=1 ';
	if (isset($territoire_id) && $territoire_id) {
		$query .= ' AND `competence_geo`="territoire" AND `id_competence_geo`= ?';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if (isset($user_pro_id)) {
		$query .= ' AND `'.DB_PREFIX.'bsl_professionnel`.id_professionnel = = ?';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$stmt = mysqli_prepare($conn, $query);

	if(count($params) > 0){
		$query_params = [];
		$query_params[] = $types;
		foreach ($params as $id => $term) {
			$query_params[] = &$params[$id];
		}
		call_user_func_array(array($stmt, 'bind_param'), $query_params);
	}
	check_mysql_error($conn);

	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_assoc($result)) {
			$pros[] = $row;
		}
		mysqli_stmt_close($stmt);
	}
	
	return $pros;
}

function get_offre_by_id($id){

	global $conn;
	$offre = null;

	$query = 'SELECT `id_offre`, `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, "%d/%m/%Y") AS date_debut,
		DATE_FORMAT(`fin_offre`, "%d/%m/%Y") AS date_fin, `id_sous_theme`, `adresse_offre`, `code_postal_offre`, `ville_offre`,
		`courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `zone_selection_villes`, `actif_offre`,
		`'.DB_PREFIX.'bsl_professionnel`.id_professionnel, `nom_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`,
		competence_geo, id_theme_pere, nom_departement, nom_region, nom_territoire, id_competence_geo
		FROM `'.DB_PREFIX.'bsl_offre`
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`'.DB_PREFIX.'bsl_offre`.id_professionnel
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.id_theme=`'.DB_PREFIX.'bsl_offre`.id_sous_theme
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		WHERE id_offre= ?';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$offre = mysqli_fetch_assoc($result);
		}
		mysqli_stmt_close($stmt);
	}

	return $offre;
}

/* Offre */
function get_liste_offres($flag, $territoire_id, $user_pro_id) {

	global $conn;
	$offres = [];

	$query = 'SELECT id_offre, nom_offre, DATE_FORMAT(`debut_offre`, "%d/%m/%Y") AS date_debut,
		DATE_FORMAT(`fin_offre`, "%d/%m/%Y") AS date_fin, `theme_pere`.libelle_theme_court, zone_selection_villes,
		nom_pro, `competence_geo`, `id_competence_geo`, nom_departement, nom_region, nom_territoire
		FROM `'.DB_PREFIX.'bsl_offre`
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`'.DB_PREFIX.'bsl_offre`.`id_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.id_theme=`'.DB_PREFIX.'bsl_offre`.`id_sous_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`'.DB_PREFIX.'bsl_theme`.`id_theme_pere`
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		WHERE actif_offre= ? ';
	$params[] = (int) $flag;
	$types = 'i';

	if (isset($territoire_id) && $territoire_id) {
		$query .= 'AND `competence_geo`="territoire" AND `id_competence_geo`= ?';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if (isset($user_pro_id)) {
		$query .= 'AND `'.DB_PREFIX.'bsl_professionnel`.id_professionnel = ?';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$stmt = mysqli_prepare($conn, $query);

	$query_params = [];
	$query_params[] = $types;
	foreach ($params as $id => $term) {
		$query_params[] = &$params[$id];
	}
	call_user_func_array(array($stmt, 'bind_param'), $query_params);
	check_mysql_error($conn);

	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($offre = mysqli_fetch_assoc($result)) {
			$offres[] = $offre;
		}
		mysqli_stmt_close($stmt);
	}

	return $offres;
}

/* Pros */
function get_liste_pros($flag, $territoire_id) { //tous les professionnel du territoire

	global $conn;
	$pros = [];

	$query = 'SELECT `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,
		GROUP_CONCAT(libelle_theme_court SEPARATOR ", ") AS themes, competence_geo,  nom_departement, nom_region, nom_territoire
		FROM `'.DB_PREFIX.'bsl_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes` ON `'.DB_PREFIX.'bsl_professionnel_themes`.`id_professionnel`=`'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_professionnel_themes`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		WHERE `actif_pro`= ? ';
	if ($territoire_id) {
		$query .= 'AND `competence_geo`="territoire" AND `id_competence_geo`= ?';
	}
	$query .= ' GROUP BY `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,competence_geo';
	$stmt = mysqli_prepare($conn, $query);
	if ($territoire_id)
		mysqli_stmt_bind_param($stmt, 'ii', $flag, $territoire_id);
	else
		mysqli_stmt_bind_param($stmt, 'i', $flag);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($pro = mysqli_fetch_assoc($result)) {
			$pros[] = $pro;
		}
		mysqli_stmt_close($stmt);
	}

	return $pros;
}

function get_pro_by_id($id){

	global $conn;
	$pro = null;

	$query = 'SELECT * FROM `'.DB_PREFIX.'bsl_professionnel`
		WHERE id_professionnel= ?';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$pro = mysqli_fetch_assoc($result);
		}
		mysqli_stmt_close($stmt);
	}

	return $pro;
}

function create_pro($nom, $type, $desc, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $site, $delai, $competence_geo, $competence_geo_id, $user_id) {

	global $conn;
	$created = false;

	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_professionnel`
		(`nom_pro`, `type_pro`, `description_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`,
		`courriel_pro`, `telephone_pro`, `site_web_pro`, `delai_pro`, `competence_geo`, `id_competence_geo`, `user_derniere_modif`)
		VALUES ( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? )';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ssssssssssisii', $nom, $type, $desc, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $site, $delai, $competence_geo, $competence_geo_id, $user_id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$created = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	return $created;
}

function update_pro($pro_id, $nom, $type, $desc, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $site, $delai, $actif, $competence_geo, $competence_geo_id, $themes, $user_id){

	global $conn;

	//mise à jour des champs principaux
	$updated = false;
	$updated_t = false;
	$query = 'UPDATE `'.DB_PREFIX.'bsl_professionnel`
		SET `nom_pro` = ?, `type_pro` = ?, `description_pro` = ?, `adresse_pro` = ?, `code_postal_pro` = ?, `ville_pro` = ?, `code_insee_pro` = ?, `courriel_pro` = ?, `telephone_pro` = ?, `site_web_pro` = ?, `delai_pro` = ?, `actif_pro` = ?, `user_derniere_modif` = ? ';

	$terms = array($nom, $type, $desc, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $site, $delai, $actif, $user_id);
	$terms_type = "ssssssssssiii";
	if ($competence_geo) {
		$query .= ', `competence_geo` = ?';
		$terms[] = $competence_geo;
		$terms_type .= "s";
	}
	if ($competence_geo_id) {
		$query .= ', `id_competence_geo` = ? ';
		$terms[] = $competence_geo_id;
		$terms_type .= "i";
	}
	$query .= ' WHERE `id_professionnel` = ?';
	$terms[] = $pro_id;
	$terms_type .= "i";

	$stmt = mysqli_prepare($conn, $query);
	$query_params = [];
	$query_params[] = $terms_type;
	foreach ($terms as $id => $term) {
		$query_params[] = &$terms[$id];
	}
	call_user_func_array(array($stmt, 'bind_param'), $query_params);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$updated = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	//mise à jour des thèmes (ce sont des checkboxes : on vide et on réimporte)
	$query_d = 'DELETE FROM `'.DB_PREFIX.'bsl_professionnel_themes` WHERE `id_professionnel` = ? ';
	$stmt = mysqli_prepare($conn, $query_d);
	mysqli_stmt_bind_param($stmt, 'i', $pro_id);
	check_mysql_error($conn);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);

	if (isset($themes)){
		$query_t = 'INSERT INTO `'.DB_PREFIX.'bsl_professionnel_themes`(`id_professionnel`, `id_theme`) VALUES ';
		$terms_t = array();
		$terms_type_t = null;
		foreach ($themes as $theme_id) {
			$query_t .= '(?, ?), ';
			$terms_t[] = $pro_id;
			$terms_t[] = $theme_id;
			$terms_type_t .= "ii";
		}
		$query_t = substr($query_t, 0, -2); // tweak pour enlever " '" à la fin de la requête
		$stmt = mysqli_prepare($conn, $query_t);
		$query_params_t = [];
		$query_params_t[] = $terms_type_t;
		foreach ($terms_t as $id => $term) {
			$query_params_t[] = &$terms_t[$id];
		}
		call_user_func_array(array($stmt, 'bind_param'), $query_params_t);

		check_mysql_error($conn);
		if (mysqli_stmt_execute($stmt)) {
			$updated_t = mysqli_stmt_affected_rows($stmt) > 0;
			mysqli_stmt_close($stmt);
		}
	}

	return ($updated+$updated_t);
}

/* Utilisateurs */
function get_liste_users($flag, $territoire_id) { //tous les utilisateurs du territoire

	global $conn;
	$users = [];

	$query = 'SELECT `id_utilisateur`, `'.DB_PREFIX.'bsl_utilisateur`.`id_statut`, `nom_utilisateur`,
		`email`, `libelle_statut`, `nom_pro`, `nom_territoire`
		FROM `'.DB_PREFIX.'bsl_utilisateur`
		JOIN `'.DB_PREFIX.'bsl__statut` ON `'.DB_PREFIX.'bsl__statut`.`id_statut`=`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_utilisateur`.`id_metier`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`=`'.DB_PREFIX.'bsl_utilisateur`.`id_metier`
		WHERE `actif_utilisateur`= ?';
	if ($territoire_id) {
		$query .= ' AND (`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`=2 AND `id_metier`= ?)
			OR (`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`=3 AND `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire"
				AND `id_competence_geo`= ?)';
	}
	$query .= ' ORDER BY `'.DB_PREFIX.'bsl_utilisateur`.`id_statut` ASC,`id_metier` ASC';

	$stmt = mysqli_prepare($conn, $query);

	if ($territoire_id)
		mysqli_stmt_bind_param($stmt, 'iii', $flag, $territoire_id, $territoire_id);
	else
		mysqli_stmt_bind_param($stmt, 'i', $flag);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($user = mysqli_fetch_assoc($result)) {
			$users[] = $user;
		}
		mysqli_stmt_close($stmt);
	}

	return $users;
}

/* Formulaire */

function get_liste_formulaires($flag_actif = 1, $territoire_id = null) {

	global $conn;
	$formulaires = [];

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme_court`, `'.DB_PREFIX.'bsl_territoire`.`nom_territoire`, `nb_pages` 
		FROM `'.DB_PREFIX.'bsl_formulaire`
		JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_formulaire`.`id_territoire` AND `'.DB_PREFIX.'bsl_territoire`.`actif_territoire`=1
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`actif`=? ';
	if ($territoire_id) 
		$query .= 'AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=? ';
	$query .= 'ORDER BY `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`';
	$stmt = mysqli_prepare($conn, $query);
	if ($territoire_id)
		mysqli_stmt_bind_param($stmt, 'ii', $flag_actif, $territoire_id);
	else
		mysqli_stmt_bind_param($stmt, 'i', $flag_actif);
	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_formulaire, $libelle, $territoire, $nb_pages);
	while (mysqli_stmt_fetch($stmt)) {
		$formulaires[] = array('id' => $id_formulaire, 'libelle' => $libelle, 'territoire' => ($territoire) ? $territoire : 'national', 'nb_pages' => $nb_pages);
	}
	mysqli_stmt_close($stmt);
	return $formulaires;
}

function get_formulaire_by_id($id){

	global $conn;
	$tmp_id = 0;
	$tmp_p = 0;
	$meta = [];
	$pages = [];
	$questions = [];

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`,`'.DB_PREFIX.'bsl_theme`.`libelle_theme_court`, `'.DB_PREFIX.'bsl_territoire`.`nom_territoire`, `'.DB_PREFIX.'bsl_formulaire`.`nb_pages`, `'.DB_PREFIX.'bsl_formulaire__page`.`id_page`, `'.DB_PREFIX.'bsl_formulaire__page`.`titre`, `'.DB_PREFIX.'bsl_formulaire__page`.`ordre` AS `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__page`.`aide`, `'.DB_PREFIX.'bsl_formulaire__question`.`id_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre` AS `ordre_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`, `'.DB_PREFIX.'bsl_formulaire__question`.`taille`, `'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire` FROM `'.DB_PREFIX.'bsl_formulaire`
		JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_formulaire`.`id_territoire` AND `'.DB_PREFIX.'bsl_territoire`.`actif_territoire`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page` AND `'.DB_PREFIX.'bsl_formulaire__question`.`actif`=1
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` = ?
		ORDER BY `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre`';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_formulaire, $libelle_theme_court, $nom_territoire, $nb_pages, $id_page, $titre, $ordre_page, $aide, $id_question, $ordre_question, $libelle_question, $html_name, $type, $taille, $obligatoire);
	while (mysqli_stmt_fetch($stmt)) {
		if ($id_formulaire != $tmp_id) { //données du formulaire
			$meta = array('id' => $id_formulaire, 'theme' => $libelle_theme_court, 'territoire' => ($nom_territoire) ? $nom_territoire : 'national', 'nb' => $nb_pages);
			$tmp_id = $id_formulaire;
		}
		if ($id_page != $tmp_p) { //données des pages du formulaire
			$pages[] = array('id' => $id_page, 'titre' => $titre, 'ordre' => $ordre_page, 'aide' => $aide);
			$tmp_p = $id_page;
		}
		$questions[$id_page][] = array('id' => $id_question, 'ordre' => $ordre_question, 'libelle' => $libelle_question, 'name' => $html_name, 'type' => $type, 'taille' => $taille, 'obligatoire' => $obligatoire);
	}
	mysqli_stmt_close($stmt);
	return [$meta, $pages, $questions];
}

function get_question_by_id($id) {

	global $conn;
	$tmp_q = 0;
	$question = [];
	$reponses = [];

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire__question`.`id_question`, `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre` AS `ordre_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`, `'.DB_PREFIX.'bsl_formulaire__question`.`taille`, `'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`libelle`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`ordre` AS `ordre_valeur`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`defaut` FROM `'.DB_PREFIX.'bsl_formulaire__question`
		JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` ON `'.DB_PREFIX.'bsl_formulaire__valeur`.`id_question`=`'.DB_PREFIX.'bsl_formulaire__question`.`id_question` AND `'.DB_PREFIX.'bsl_formulaire__valeur`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page`
		WHERE `'.DB_PREFIX.'bsl_formulaire__question`.`id_question` = ?
		ORDER BY `'.DB_PREFIX.'bsl_formulaire__valeur`.`ordre`';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_question, $id_formulaire, $ordre_question, $libelle_question, $html_name, $type, $taille, $obligatoire, $libelle, $valeur, $ordre_valeur, $defaut);
	while (mysqli_stmt_fetch($stmt)) {
		if ($id_question != $tmp_q) {
			$question = array('id' => $id_question, 'id_formulaire' => $id_formulaire, 'ordre' => $ordre_question, 'libelle' => $libelle_question, 'name' => $html_name, 'type' => $type, 'taille' => $taille, 'obligatoire' => $obligatoire);
			$tmp_q = $id_question;
		}
		$reponses[] = array('libelle' => $libelle, 'valeur' => $valeur, 'ordre' => $ordre_valeur, 'defaut' => $defaut);
	}
	mysqli_stmt_close($stmt);
	return [$question, $reponses];
}

function get_code_insee($code_postal, $ville){

	global $conn;
	$code_insee = null;

	$query = 'SELECT code_insee FROM `'.DB_PREFIX.'bsl__ville` WHERE code_postal=? AND nom_ville LIKE ? ';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ss', $code_postal, $ville);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			$code_insee = $row['code_insee'];
		}
		mysqli_stmt_close($stmt);
	}

	return $code_insee;
}

function get_liste_regions() {

	global $conn;
	$regions = null;
	$query = 'SELECT `id_region`, `nom_region` FROM `'.DB_PREFIX.'bsl__region` WHERE 1 ';
	$result = mysqli_query($conn, $query); // [OK]
	while($row = mysqli_fetch_assoc($result)) {
		$regions[] = $row;
	}
	return $regions;
}

function get_liste_departements() {

	global $conn;
	$departements = null;
	$query = 'SELECT `id_departement`, `nom_departement` FROM `'.DB_PREFIX.'bsl__departement` WHERE 1 ';
	$result = mysqli_query($conn, $query); // [OK]
	while($row = mysqli_fetch_assoc($result)) {
		$departements[] = $row;
	}
	return $departements;
}

function get_liste_themes($pro_id = null, $actif = null) {

	global $conn;
	$themes = null;
	$params = [];
	$types = '';

	if(isset($pro_id)) {
		$query = 'SELECT `'.DB_PREFIX.'bsl_theme`.`id_theme`, `libelle_theme`, `libelle_theme_court`, `actif_theme`, `id_professionnel` 
			FROM `'.DB_PREFIX.'bsl_theme` 
			LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes`
			ON `'.DB_PREFIX.'bsl_professionnel_themes`.`id_theme`=`'.DB_PREFIX.'bsl_theme`.`id_theme`
			AND `'.DB_PREFIX.'bsl_professionnel_themes`.`id_professionnel`= ? ';
		$params[] = (int) $pro_id;
		$types .= 'i';
	}else{
		$query = 'SELECT `'.DB_PREFIX.'bsl_theme`.`id_theme`, `libelle_theme`, `libelle_theme_court`, `actif_theme` 
			FROM `'.DB_PREFIX.'bsl_theme` ';
	}
	$query .= 'WHERE `id_theme_pere` IS NULL ';
	if(isset($actif)) {
		$query .= 'AND `actif_theme`= ? ';
		$params[] = (int) $actif;
		$types .= 'i';
	}
	$stmt = mysqli_prepare($conn, $query);
	if(count($params) > 0) {
		$query_params = [];
		$query_params[] = $types;
		foreach ($params as $id => $term) {
			$query_params[] = &$params[$id];
		}
		call_user_func_array(array($stmt, 'bind_param'), $query_params);
	}

	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($theme = mysqli_fetch_assoc($result)) {
			$themes[] = $theme;
		}
		mysqli_stmt_close($stmt);
	}

	return $themes;
}

function get_liste_sous_themes($theme_pere) {

	global $conn;
	$themes = null;
	if (isset($theme_pere)) {
		$query = 'SELECT `id_theme`, `libelle_theme`, `ordre_theme`, `actif_theme` 
			FROM `'.DB_PREFIX.'bsl_theme`
			WHERE `id_theme_pere`= ?  
			ORDER BY actif_theme DESC, ordre_theme';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'i', $theme_pere);
	}else{
		$query = 'SELECT `id_theme`, `libelle_theme`, `ordre_theme`, `actif_theme` 
			FROM `'.DB_PREFIX.'bsl_theme`
			ORDER BY actif_theme DESC, ordre_theme';
		$stmt = mysqli_prepare($conn, $query);
	}
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($theme = mysqli_fetch_assoc($result)) {
			$themes[] = $theme;
		}
		mysqli_stmt_close($stmt);
	}

	return $themes;
}

/* Territoires */
function update_territoire($id, $libelle){

	global $conn;
	$updated = false;

	$query = 'UPDATE `'.DB_PREFIX.'bsl_territoire` SET `nom_territoire`= ? WHERE `id_territoire`= ?';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'si', $libelle, $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$updated = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}
	return $updated;
}

function create_territoire($libelle) {

	global $conn;
	$created = false;
	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_territoire`(`nom_territoire`) VALUES ( ? )';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 's', $libelle);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$created = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}
	return $created;
}

function update_villes_territoire($id, $tab_villes) {

	global $conn;
	$updated = false;

	//********* on efface
	$query_d = 'DELETE FROM `'.DB_PREFIX.'bsl_territoire_villes` WHERE `id_territoire` = ?';
	$stmt = mysqli_prepare($conn, $query_d);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt));
	mysqli_stmt_close($stmt);

	//********* puis on met à jour (chaque code insee ne peut être lié qu'une fois à un territoire)
	if(isset($tab_villes) && $tab_villes) {
		$params = [];
		$types = '';
		$tab_code_insee = array();
		
		$query_i = 'INSERT INTO `'.DB_PREFIX.'bsl_territoire_villes` (`id_territoire`, `code_insee`) VALUES ';
		foreach ($tab_villes as $selected_option) {
			if (!in_array($selected_option, $tab_code_insee)) {
				$query_i .= '( ?, ?), ';
				$params[] = (int) $id;
				$params[] = $selected_option;
				$types .= 'is';
				$tab_code_insee[] = $selected_option;
			}
		}
		$query_i = substr($query_i, 0, -2); //on enlève le dernier ", "
		$stmt = mysqli_prepare($conn, $query_i);
		if(count($params) > 0) {
			$query_params = [];
			$query_params[] = $types;
			foreach ($params as $id => $term) {
				$query_params[] = &$params[$id];
			}
			call_user_func_array(array($stmt, 'bind_param'), $query_params);
		}
		check_mysql_error($conn);
		
		if (mysqli_stmt_execute($stmt)) {
			$updated = mysqli_stmt_affected_rows($stmt) > 0;
			mysqli_stmt_close($stmt);
		}
	}

	return $updated;
}

function get_villes_by_territoire($id) {

	global $conn;
	$villes = null;
	$query = 'SELECT `'.DB_PREFIX.'bsl__ville`.`code_insee`, `'.DB_PREFIX.'bsl__ville`.`code_postal`, 
		`'.DB_PREFIX.'bsl__ville`.`nom_ville` 
		FROM `'.DB_PREFIX.'bsl__ville` JOIN `'.DB_PREFIX.'bsl_territoire_villes` ON `'.DB_PREFIX.'bsl_territoire_villes`.`code_insee`=`'.DB_PREFIX.'bsl__ville`.`code_insee` 
		WHERE `id_territoire`= ? 
		ORDER BY nom_ville';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($ville = mysqli_fetch_assoc($result)) {
			$villes[] = $ville;
		}
		mysqli_stmt_close($stmt);
	}
	return $villes;
}

/* Thèmes */
function update_theme($id, $libelle, $actif){

	global $conn;
	$updated = false;

	$query = 'UPDATE `'.DB_PREFIX.'bsl_theme` SET `libelle_theme`= ? , `actif_theme`= ? WHERE `id_theme`= ?';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sii', $libelle, $actif, $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$updated = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}
	return $updated;
}

function update_sous_themes($id, $sous_themes){

	global $conn;
	$updated = 0;
	
	if(isset($sous_themes)){
		foreach ($sous_themes as $foo) {
			$query = 'UPDATE `'.DB_PREFIX.'bsl_theme` 
				SET `libelle_theme`= ? , `ordre_theme`= ? , `actif_theme`= ? 
				WHERE `id_theme`= ?';
			$stmt = mysqli_prepare($conn, $query);
			mysqli_stmt_bind_param($stmt, 'siii', $foo[1], $foo[2], $foo[3], $foo[0]);
			check_mysql_error($conn);
			if (mysqli_stmt_execute($stmt)) {
				$updated += mysqli_stmt_affected_rows($stmt) > 0;
				mysqli_stmt_close($stmt);
			}
		}
	}
	return $updated;
}

function create_sous_theme($libelle, $id_theme) {

	global $conn;
	$created = false;
	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_theme` (`libelle_theme`, `id_theme_pere`, `actif_theme`) VALUES ( ?, ?, 0)';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'si', $libelle, $id_theme);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$created = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}
	return $created;
}

/* Utilisateurs */
function create_user($nom_utilisateur, $courriel, $mdp, $statut, $attache) {

	global $conn;
	$created = false;

	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_utilisateur`
		(`nom_utilisateur`, `email`, `motdepasse`, `date_inscription`, `id_statut`, `id_metier`) 
		VALUES (? , ? , ? ,NOW(), ? , ?)';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sssss', $nom_utilisateur, $courriel, $mdp, $statut, $attache);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$created = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	return $created;
}

function update_user($id, $nom, $courriel, $actif){

	global $conn;
	$updated = false;
	
	$query = 'UPDATE `'.DB_PREFIX.'bsl_utilisateur` SET `nom_utilisateur` = ?, `email` = ?, `actif_utilisateur` = ? WHERE `id_utilisateur` = ?';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ssii', $nom, $courriel, $actif, $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$updated = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	return $updated;
}

function update_motdepasse($id, $motdepasseactuel, $nouveaumotdepasse, $actif){

	global $conn;
	$updated = false;
	
	$query = 'SELECT `motdepasse` FROM `'.DB_PREFIX.'bsl_utilisateur` WHERE `id_utilisateur`= ?';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		$row = mysqli_fetch_assoc($result);
		
		if (password_verify(SALT_BOUSSOLE . $motdepasseactuel, $nouveaumotdepasse)) {
			$query = 'UPDATE `'.DB_PREFIX.'bsl_utilisateur` 
				SET `motdepasse` = ? 
				WHERE `id_utilisateur` = ?';
			//pas de modif du statut autorisée. sinon il faudrait ajouter : `id_statut` = \"".$_POST["statut"]."\"
			$stmt2 = mysqli_prepare($conn, $query);
			$hache = secu_password_hash($nouveaumotdepasse);
			mysqli_stmt_bind_param($stmt2, 'si', $hache, $id);
			check_mysql_error($conn);
			
			if (mysqli_stmt_execute($stmt2)) {
				$updated = mysqli_stmt_affected_rows($stmt) > 0;
				mysqli_stmt_close($stmt);
				$msg = 'Mot de passe modifié.';
			} else {
				$msg = $message_erreur_bd;
			}
		} else {//mdp actuel correct
			$msg = "Le mot de passe indiqué n'est pas le bon.";
		}
	} else {
		$msg = "Pas d'utilisateur connu.";
	}
	mysqli_stmt_close($stmt);

	return [$updated,$msg];
}

function get_user_by_id($id){

	global $conn;
	$user = null;

	$query = 'SELECT `'.DB_PREFIX.'bsl_utilisateur`.`id_statut`, `nom_utilisateur`, `email`, `date_inscription`, `actif_utilisateur`, `id_professionnel`, `nom_pro`, `id_territoire` , `nom_territoire`
		FROM `'.DB_PREFIX.'bsl_utilisateur` 
		JOIN `'.DB_PREFIX.'bsl__statut` ON `'.DB_PREFIX.'bsl__statut`.`id_statut`=`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_utilisateur`.`id_metier`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`=`'.DB_PREFIX.'bsl_utilisateur`.`id_metier`
		WHERE `id_utilisateur`= ?';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$user = mysqli_fetch_assoc($result);
		}
		mysqli_stmt_close($stmt);
	}

	return $user;
}





//************** à voir si toujours utile...
//liste villes (remplacé depuis la v0 par l'appel à un fichier texte)
function liste_villes($format){

	global $conn;

	$sql = 'SELECT DISTINCT nom_ville, code_postal FROM `'.DB_PREFIX.'bsl__ville` ORDER BY nom_ville';
	$result = mysqli_query($conn, $sql); // [OK]
	$liste = null;
	while($row = mysqli_fetch_assoc($result)) {
		if ($format=="jq") {
			$liste .= "\"".$row['nom_ville']." ".$row['code_postal']."\",";
		}else if ($format=="select") {
			$liste .= "<option value=\"".$row['nom_ville']." ".$row['code_postal']. "\">".$row['nom_ville']." ".$row['code_postal']. "</option>";
		}
	}
	return $liste;
	
	//********* autre requête anciennement dans territoire.php, remplacée par l'appel à un fichier pour des questions de perf
	/*$sql = "SELECT DISTINCT nom_ville, code_postal, code_insee FROM `'.DB_PREFIX.'bsl__ville`
		WHERE `code_insee` NOT IN (SELECT DISTINCT code_insee FROM `'.DB_PREFIX.'bsl_territoire_villes` WHERE `id_territoire`=".$_SESSION['territoire_id'].") ORDER BY nom_ville";
	$result = mysqli_query($conn, $sql); // [non utilisé]
	$liste1 = "";
	while($row = mysqli_fetch_assoc($result)) {
		$liste1 .= "<option value=\"".$row['code_insee']."\">".$row['nom_ville']." ".$row['code_postal']. "</option>";
	}*/
}

function verif_territoire_pro($territoire_id, $id) {

	global $conn;
	//on vérifie si le pro a la competence geo sur ce territoire
	$sql = 'SELECT competence_geo, id_competence_geo FROM `'.DB_PREFIX.'bsl_professionnel`
	WHERE competence_geo="territoire" AND id_competence_geo='.$territoire_id.' AND id_professionnel='.$id;
	$result = mysqli_query($conn, $sql); // [non utilisé]
	return $result;
}

function verif_territoire_user($territoire_id, $id) {

	global $conn;
	//on vérifie si le pro a la competence geo sur ce territoire
	$sql = 'SELECT competence_geo, id_competence_geo FROM `'.DB_PREFIX.'bsl_utilisateur`
	WHERE competence_geo="territoire" AND id_competence_geo='.$territoire_id.' AND id_utilisateur='.$id;
	$result = mysqli_query($conn, $sql); // [non utilisé]
	return $result;
}

/******* bouts de code utile
//****** impression d'une requête
$print_sql = $query;
foreach(array($nom_utilisateur, $courriel, $mdp, $statut, $attache) as $term){
	$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
}
echo "<pre>".$print_sql."</pre>"; 
 *
 *
 * //****** modele de fonction select
 * function get_machin(){
 * global $conn;
 * $query = 'SELECT ... FROM ...';
 * $stmt = mysqli_prepare($conn, $query);
 * check_mysql_error($conn);
 * mysqli_stmt_execute($stmt);
 * mysqli_stmt_bind_result($stmt, $id, $nimetus, $kogus);
 * $rows = array();
 * while (mysqli_stmt_fetch($stmt)) {
 * $rows[] = array(
 * 'id' => $id,
 * 'xxxxx' => $xxx,
 * );
 * }
 * mysqli_stmt_close($stmt);
 * return $rows;
 * }
 */