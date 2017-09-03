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
	
	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`, 
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
	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);
	mysqli_stmt_bind_result($stmt, $id_formulaire, $libelle_question, $html_name, $type, $taille, $obligatoire, $libelle, $valeur, $id_o);
	$tmp_que = '';

	$questions = [];
	$reponses = [];
	while (mysqli_stmt_fetch($stmt)) {
		if ($libelle_question != $tmp_que) { //on récupère les questions
			$questions[] = array('libelle' => $libelle_question, 'name' => $html_name, 'type' => $type, 'obligatoire' => $obligatoire);
			$tmp_que = $libelle_question;
		}
		$reponses[$html_name][] = array('libelle' => $libelle, 'valeur' => $valeur, 'selectionne' => ($id_o) ? 'selected' : '');  //on récupère les réponses
	}
	mysqli_stmt_close($stmt);
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

function update_offre($id_offre, $nom, $desc, $date_debut, $date_fin, $sous_theme, $adresse, $code_postal, $ville, $courriel, $tel, $url, $delai, $zone, $actif, $user_id){
	
	global $conn;
	$updated = false;
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

	return $updated;
}

function update_criteres_offre($id, $tab_villes, $tab_criteres, $user_id) { //todo : passer en statement

	global $conn;
	$updated = false;
	
	$reqd = 'DELETE FROM `'.DB_PREFIX.'bsl_offre_criteres` WHERE `id_offre` = ' . $id; 
	mysqli_query($conn, $reqd);

	$req2 = 'INSERT INTO `'.DB_PREFIX.'bsl_offre_criteres` (`id_offre`, `nom_critere`, `valeur_critere`) VALUES ';
	if (isset($tab_villes)) {
		foreach ($tab_villes as $selected_option) {
			$req2 .= '(' . $id . ', "villes", "' . $selected_option . '"), ';
		}
	}
	foreach ($tab_criteres as $name => $tab_critere) {
		foreach ($tab_critere as $key => $selected_option) {
			$req2 .= '(' . $id . ', "' . $name . '", "' . $selected_option . '"), ';
		}
	}
	$req2 = substr($req2, 0, -2); //on enlève le dernier ", "
	$result2 = mysqli_query($conn, $req2);
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
	if ($id) $sql .= ' AND `id_territoire`=' . $id;

	$stmt = mysqli_prepare($conn, $sql);
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

function get_liste_pros_select($id_territoire, $id_user_pro) { //todo : passer en statement

	global $conn;
	$pros = null;
	
	$query = 'SELECT `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`, `nom_pro` 
		FROM `'.DB_PREFIX.'bsl_professionnel` 
		WHERE `actif_pro`=1 '; 
	if (isset($id_territoire) && $id_territoire) {
		$query .= ' AND `competence_geo`="territoire" AND `id_competence_geo`=' . $id_territoire;
	}
	if (isset($id_user_pro)) {
		$query .= ' AND `'.DB_PREFIX.'bsl_professionnel`.id_professionnel = ' . $id_user_pro;
	}
	 //todo : limiter en fonction du user_statut ??
	$pros = mysqli_query($conn, $query);
	
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
	/*********** debug
	echo "<pre>";
	echo $query;
	print_r($params);
	print_r($query_params);
	echo $types."</pre>";
	//*********** debug */
	
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
	mysqli_stmt_execute($stmt);
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
/*echo "<pre>";
print_r($themes);
echo $query_t;
echo $terms_type_t;
print_r($query_params_t);
echo "</pre>";*/

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
	mysqli_stmt_execute($stmt);
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
	
	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme_court`, `'.DB_PREFIX.'bsl_territoire`.`nom_territoire` FROM `'.DB_PREFIX.'bsl_formulaire` 
	JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
	LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_formulaire`.`id_territoire` AND `'.DB_PREFIX.'bsl_territoire`.`actif_territoire`=1 
	WHERE `'.DB_PREFIX.'bsl_formulaire`.`actif`=? ';
	if ($territoire_id) $query .= 'AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=? ';
	$query .= 'ORDER BY `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`';
	$stmt = mysqli_prepare($conn, $query);
	if ($territoire_id)
		mysqli_stmt_bind_param($stmt, 'ii', $flag_actif, $territoire_id);
	else
		mysqli_stmt_bind_param($stmt, 'i', $flag_actif);
	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_formulaire, $libelle, $territoire);
	while (mysqli_stmt_fetch($stmt)) {
		$formulaires[] = array('id' => $id_formulaire, 'libelle' => $libelle, 'territoire' => ($territoire) ? $territoire : 'national');
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
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_assoc($result)) {
		$regions[] = $row;
	}
	return $regions;
}

function get_liste_departements() {

	global $conn;
	$departements = null;
	$query = 'SELECT `id_departement`, `nom_departement` FROM `'.DB_PREFIX.'bsl__departement` WHERE 1 ';
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_assoc($result)) {
		$departements[] = $row;
	}
	return $departements;
}

function get_liste_departements() {

	global $conn;
	$departements = null;
	$query = 'SELECT `id_departement`, `nom_departement` FROM `'.DB_PREFIX.'bsl__departement` WHERE 1 ';
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_assoc($result)) {
		$departements[] = $row;
	}
	return $departements;
}

//************** liste villes 
//à voir si toujours utile - remplacé depuis la v0 par l'appel à un fichier texte
function liste_villes($format){
	
	global $conn;
	
	$sql = 'SELECT DISTINCT nom_ville, code_postal FROM `'.DB_PREFIX.'bsl__ville` ORDER BY nom_ville';
	$result = mysqli_query($conn, $sql);
	$liste = null;
	while($row = mysqli_fetch_assoc($result)) {
		if ($format=="jq") {
			$liste .= "\"".$row['nom_ville']." ".$row['code_postal']."\",";
		}else if ($format=="select") {
			$liste .= "<option value=\"".$row['nom_ville']." ".$row['code_postal']. "\">".$row['nom_ville']." ".$row['code_postal']. "</option>";
		}
	}
	return $liste;
}

/******* bouts de code utile
 * //****** impression d'une requête
 * $print_sql = $query;
 * foreach(array($id_offre, $coord, $_SESSION['code_insee'], $liste) as $term){
 * $print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
 * }
 * echo $print_sql;
 *
 * //****** modele de fonction select
 * function get_machin(){
 * global $conn;
 * $query = 'SELECT ... FROM ...';
 * $stmt = mysqli_prepare($conn, $query);
 * mysqli_stmt_execute($stmt);
 * check_mysql_error($conn);
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