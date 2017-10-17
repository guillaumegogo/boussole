<?php

function get_nb_nouvelles_demandes() {

	global $conn;
	$nb = 0;

	$query = 'SELECT count(`id_demande`) AS nb
		FROM `'.DB_PREFIX.'bsl_demande`
		JOIN `'.DB_PREFIX.'bsl_offre` ON `'.DB_PREFIX.'bsl_offre`.id_offre=`'.DB_PREFIX.'bsl_demande`.id_offre ';
	
	if($pro_id = secu_get_user_pro_id()) {
		$query .= 'AND `'.DB_PREFIX.'bsl_offre`.id_professionnel = ? 
		WHERE date_traitement IS NULL ';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'i', $pro_id);
	
	}else if($territoire_id = secu_get_territoire_id()) {
		$query .= 'JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`'.DB_PREFIX.'bsl_offre`.id_professionnel 
		AND `'.DB_PREFIX.'bsl_professionnel`.competence_geo = "territoire" AND `'.DB_PREFIX.'bsl_professionnel`.id_competence_geo = ? 
		WHERE date_traitement IS NULL ';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'i', $territoire_id);
	
	}else {
		$query .= 'WHERE date_traitement IS NULL ';
		$stmt = mysqli_prepare($conn, $query);	
	}
		
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

function get_criteres_offre($id_offre, $id_theme){

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
		JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse`
		JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` ON `'.DB_PREFIX.'bsl_formulaire__valeur`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse` AND `'.DB_PREFIX.'bsl_formulaire__valeur`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_offre_criteres` ON `'.DB_PREFIX.'bsl_offre_criteres`.`nom_critere`=`'.DB_PREFIX.'bsl_formulaire__question`.`html_name` AND `'.DB_PREFIX.'bsl_offre_criteres`.`valeur_critere`=`'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur` AND `'.DB_PREFIX.'bsl_offre_criteres`.`id_offre`= ?
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`type`="offre" AND `'.DB_PREFIX.'bsl_formulaire`.`actif`=1 AND `'.DB_PREFIX.'bsl_theme`.`id_theme`= ?
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

function get_criteres_mesure($id_mesure){

	global $conn;

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, 
		`'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`,
		`'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`,
		`'.DB_PREFIX.'bsl_formulaire__question`.`taille`, `'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire`,
		`'.DB_PREFIX.'bsl_formulaire__valeur`.`libelle`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur`,
		`'.DB_PREFIX.'bsl_mesure_criteres`.`id_mesure` 
		FROM `'.DB_PREFIX.'bsl_formulaire`
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page` AND `'.DB_PREFIX.'bsl_formulaire__question`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse`
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` ON `'.DB_PREFIX.'bsl_formulaire__valeur`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse` AND `'.DB_PREFIX.'bsl_formulaire__valeur`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_mesure_criteres` ON `'.DB_PREFIX.'bsl_mesure_criteres`.`nom_critere`=`'.DB_PREFIX.'bsl_formulaire__question`.`html_name` AND `'.DB_PREFIX.'bsl_mesure_criteres`.`valeur_critere`=`'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur` AND `'.DB_PREFIX.'bsl_mesure_criteres`.`id_mesure`= ?
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`type`="mesure" AND `'.DB_PREFIX.'bsl_formulaire`.`actif`=1 
		ORDER BY `'.DB_PREFIX.'bsl_formulaire__page`.`ordre`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`ordre`';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id_mesure);
	check_mysql_error($conn);
	
	$questions = [];
	$reponses = [];
	$tmp_que = '';
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_assoc($result)) {
			if ($row['libelle_question'] != $tmp_que) { 
				$questions[] = array('libelle' => $row['libelle_question'], 'name' => $row['html_name'], 'type' => $row['type'], 'obligatoire' => $row['obligatoire']);
				$tmp_que = $row['libelle_question'];
			}
			$reponses[$row['html_name']][] = array('libelle' => $row['libelle'], 'valeur' => $row['valeur'], 'selectionne' => ($row['id_mesure']) ? 'selected' : ''); 
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

function get_liste_demandes($flag_traite = 1, $territoire_id = null, $user_pro_id = null){

	$demandes = null;
	$params = [];
	$types = '';
	$query = 'SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, `'.DB_PREFIX.'bsl_offre`.nom_offre,
		`'.DB_PREFIX.'bsl_offre`.id_professionnel, `'.DB_PREFIX.'bsl_professionnel`.nom_pro
		FROM `'.DB_PREFIX.'bsl_demande`
		JOIN `'.DB_PREFIX.'bsl_offre` ON `'.DB_PREFIX.'bsl_offre`.id_offre = `'.DB_PREFIX.'bsl_demande`.id_offre
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_offre`.id_professionnel = `'.DB_PREFIX.'bsl_professionnel`.id_professionnel
		WHERE date_traitement IS '.(($flag_traite) ? 'NOT' : '').' NULL ';
	if ((int) $territoire_id > 0) {
		$query .= 'AND `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`= ? ';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if ((int) $user_pro_id > 0) {
		$query .= 'AND `'.DB_PREFIX.'bsl_offre`.id_professionnel = ? ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= ' ORDER BY date_demande DESC';
	
	$stmt = query_prepare($query,$params,$types);
	$demandes = query_get($stmt);
	return $demandes;
}

function update_demande($id, $commentaire){

	global $conn;
	$updated = false;
	$user_id=secu_get_current_user_id();

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
function create_offre($nom, $desc, $date_debut, $date_fin, $pro_id ) {

	global $conn;
	$created = false;
	$user_id= secu_get_current_user_id();

	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_offre`(`nom_offre`, `description_offre`, `debut_offre`, `fin_offre`, `id_professionnel`,
		`adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`,
		`telephone_offre`, `site_web_offre`, `delai_offre`, `creation_date`, `creation_user_id`)
		SELECT ?, ?, ?, ?, ?,`adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`,`courriel_pro`,
		`telephone_pro`, `site_web_pro`, `delai_pro`, NOW(), ?
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

function update_offre($id_offre, $nom, $desc, $date_debut, $date_fin, $sous_theme, $adresse, $code_postal, $ville, $courriel, $tel, $url, $delai, $zone, $tab_villes){

	global $conn;
	$user_id= secu_get_current_user_id();
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
		`zone_selection_villes` = ?, `last_edit_date` = NOW(), `last_edit_user_id` = ?
		WHERE `id_offre` = ?';

	$stmt = mysqli_prepare($conn, $req);
	mysqli_stmt_bind_param($stmt, 'ssssisssssssiiii', $nom, $desc, $date_d, $date_f, $sous_theme, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $url, $delai, $zone, $user_id, $id_offre);
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
		$stmt = query_prepare($req2,$params,$types);
		$updated_v = query_do($stmt);
	}

	return [$updated, $updated_v];
}

function update_criteres_offre($id, $tab_criteres) { 

	global $conn;
	$user_id= secu_get_current_user_id();
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
			foreach ($tab_critere as $key => $selected_option) {
				$query2 .= '( ?, ? , ? ), ';
				$params[] = (int) $id;
				$params[] = $name;
				$params[] = $selected_option;
				$types .= 'iss';
			}
		}
		$query2 = substr($query2, 0, -2); //on enlève ", " à la fin de la requête
		$stmt = query_prepare($query2,$params,$types);
		$updated = query_do($stmt);
	}
	
	if($updated){
		$query3 = 'UPDATE `'.DB_PREFIX.'bsl_offre`
			SET `last_edit_date` = NOW(), `last_edit_user_id` = ?
			WHERE `id_offre` = ?';

		$stmt = mysqli_prepare($conn, $query3);
		mysqli_stmt_bind_param($stmt, 'ii', $user_id, $id);
		check_mysql_error($conn);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
	
	return $updated;
}

/* mesure */

function create_mesure($nom, $desc, $date_debut, $date_fin, $pro_id) {

	global $conn;
	$created = false;
	$user_id = secu_get_current_user_id();

	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_mesure`(`nom_mesure`, `description_mesure`, `debut_mesure`, `fin_mesure`, `id_professionnel`,
		`adresse_mesure`, `code_postal_mesure`, `ville_mesure`, `code_insee_mesure`, `courriel_mesure`,
		`telephone_mesure`, `site_web_mesure`, `competence_geo`, `id_competence_geo`, `user_derniere_modif`)
		SELECT ?, ?, ?, ?, ?,`adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`,`courriel_pro`,
		`telephone_pro`, `site_web_pro`, `competence_geo`, `id_competence_geo`, ?
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

function update_mesure($id_mesure, $nom, $desc, $date_debut, $date_fin, $sous_theme, $adresse, $code_postal, $ville, $courriel, $tel, $url, $competence_geo, $competence_geo_id, $tab_villes){

	global $conn;
	$updated = false;
	$updated_v = false;
	$code_insee = '';
	$user_id = secu_get_current_user_id();

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

	$req = 'UPDATE `'.DB_PREFIX.'bsl_mesure`
		SET `nom_mesure` = ?, `description_mesure`= ?, `debut_mesure` = ?, `fin_mesure` = ?,
		`id_sous_theme` = ?, `adresse_mesure` = ?, `code_postal_mesure`= ?, `ville_mesure`= ?, 
		`code_insee_mesure`= ?, `courriel_mesure` = ?, `telephone_mesure` = ?, `site_web_mesure` = ?, 
		`competence_geo`= ?,`id_competence_geo`= ?, `user_derniere_modif` = ?
		WHERE `id_mesure` = ?';
		
	$stmt = mysqli_prepare($conn, $req);
	mysqli_stmt_bind_param($stmt, 'ssssisissssssiii', $nom, $desc, $date_d, $date_f, $sous_theme, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $url, $competence_geo, $competence_geo_id, $user_id, $id_mesure);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$updated = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	if (isset($tab_villes)) {
		$params = [];
		$types = '';
		$req2 = 'INSERT INTO `'.DB_PREFIX.'bsl_mesure_criteres` (`id_mesure`, `nom_critere`, `valeur_critere`) 
			VALUES ';
		foreach ($tab_villes as $selected_option) {
			$req2 .= '( ?, "villes", ? ), ';
			$params[] = (int) $id_mesure;
			$params[] = $selected_option;
			$types .= 'is';
		}
		$req2 = substr($req2, 0, -2); //on enlève ", " à la fin de la requête
		$stmt = query_prepare($req2,$params,$types);
		$updated_v = query_do($stmt);
	}

	return [$updated, $updated_v];
}

function update_criteres_mesure($id, $tab_criteres) { 

	global $conn;
	$updated = false;
	$user_id=secu_get_current_user_id();

	$query1 = 'DELETE FROM `'.DB_PREFIX.'bsl_mesure_criteres` WHERE `id_mesure` = ? AND `nom_critere` NOT LIKE "villes" ';
	$stmt = mysqli_prepare($conn, $query1);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	check_mysql_error($conn);

	if (isset($tab_criteres)) {
		$query2 = 'INSERT INTO `'.DB_PREFIX.'bsl_mesure_criteres` (`id_mesure`, `nom_critere`, `valeur_critere`) 
			VALUES ';
		$params = [];
		$types = '';
		foreach ($tab_criteres as $name => $tab_critere) {
			foreach ($tab_critere as $key => $selected_option) {
				$query2 .= '( ?, ? , ? ), ';
				$params[] = (int) $id;
				$params[] = $name;
				$params[] = $selected_option;
				$types .= 'iss';
			}
		}
		$query2 = substr($query2, 0, -2); //on enlève ", " à la fin de la requête
		$stmt = query_prepare($query2,$params,$types);
		$updated = query_do($stmt);
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
	$row = null;

	$query = 'SELECT `'.DB_PREFIX.'bsl__ville`.`code_insee`,
		MIN(`'.DB_PREFIX.'bsl__ville`.`code_postal`) AS `code_postal`, `'.DB_PREFIX.'bsl__ville`.`nom_ville`
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
			$row[] = $ville;
		}
		mysqli_stmt_close($stmt);
	}
	return $row;
}

function get_villes_by_offre($id) {

	global $conn;
	$row = null;

	$query = 'SELECT `nom_ville`, `code_insee`, `code_postal` 
		FROM `'.DB_PREFIX.'bsl__ville`
		JOIN `'.DB_PREFIX.'bsl_offre_criteres` ON `nom_critere` LIKE "villes" AND `valeur_critere`=`code_insee`
		WHERE id_offre= ?
		ORDER BY nom_ville';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($ville = mysqli_fetch_assoc($result)) {
			$row[] = $ville;
		}
		mysqli_stmt_close($stmt);
	}
	return $row;
}

function get_villes_by_mesure($id) {

	global $conn;
	$row = null;

	$query = 'SELECT `nom_ville`, `code_insee`, `code_postal` 
		FROM `'.DB_PREFIX.'bsl__ville`
		JOIN `'.DB_PREFIX.'bsl_mesure_criteres` ON `nom_critere` LIKE "villes" AND `valeur_critere`=`code_insee`
		WHERE id_mesure= ?
		ORDER BY nom_ville';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($ville = mysqli_fetch_assoc($result)) {
			$row[] = $ville;
		}
		mysqli_stmt_close($stmt);
	}
	return $row;
}

function get_villes_by_pro($id) {

	global $conn;
	$row = null;

	$query = 'SELECT `nom_ville`, `'.DB_PREFIX.'bsl__ville`.`code_insee`, `code_postal` 
		FROM `'.DB_PREFIX.'bsl__ville`
		JOIN `'.DB_PREFIX.'bsl_professionnel_villes` ON `'.DB_PREFIX.'bsl_professionnel_villes`.`code_insee`=`'.DB_PREFIX.'bsl__ville`.`code_insee`
		WHERE id_professionnel= ?
		ORDER BY nom_ville';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($ville = mysqli_fetch_assoc($result)) {
			$row[] = $ville;
		}
		mysqli_stmt_close($stmt);
	}
	return $row;
}

function get_territoires($id = null, $actif = null) {

	global $conn;
	$t = null;

	$query = 'SELECT `'.DB_PREFIX.'bsl_territoire`.`id_territoire`, `nom_territoire`, `actif_territoire` as `actif`, COUNT(`code_insee`) as `c`, GROUP_CONCAT(DISTINCT(LEFT(`code_insee`,2)) SEPARATOR ", ") AS `dep`
		FROM `'.DB_PREFIX.'bsl_territoire`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` ON `'.DB_PREFIX.'bsl_territoire_villes`.`id_territoire`=`'.DB_PREFIX.'bsl_territoire`.`id_territoire`
		WHERE `nom_territoire` != "" ';
	$params = array();
	$types = '';
	if (isset($actif)) {
		$query .= ' AND `actif_territoire` = ? ';
		$params[] = (int) $actif;
		$types .= 'i';
	}
	if (isset($id) && $id > 0) {
		$query .= ' AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`= ? ';
		$params[] = (int) $id;
		$types .= 'i';
	}
	$query .= 'GROUP BY `id_territoire`, `nom_territoire` 
		ORDER BY `nom_territoire` ASC';
	$stmt = query_prepare($query,$params,$types);
	$t = query_get($stmt);
	
	return $t;
}

function get_liste_pros_select($type="pro",$zone=null, $zone_id=null, $user_pro_id=null) {

	$pros = null;
	$params = [];
	$types = '';

	$query = 'SELECT `id_professionnel`, `nom_pro`
		FROM `'.DB_PREFIX.'bsl_professionnel`
		WHERE `actif_pro` = 1 ';
	if($type == "éditeur") {
		$query .= ' AND `editeur`= ?';
		$params[] = 1;
		$types .= 'i';
	}
	if(isset($zone) && $zone) {
		$query .= ' AND `competence_geo`= ?';
		$params[] = $zone;
		$types .= 's';
	}
	if(isset($zone_id) && $zone_id) {
		$query .= ' AND `id_competence_geo`= ?';
		$params[] = (int) $zone_id;
		$types .= 'i';
	}
	if (isset($user_pro_id)) {
		$query .= ' AND id_professionnel = = ?';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$stmt = query_prepare($query,$params,$types);
	$pros = query_get($stmt);
	return $pros;
}

function get_offre_by_id($id){

	global $conn;
	$offre = null;

	$query = 'SELECT `id_offre`, `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, "%d/%m/%Y") AS date_debut,
		DATE_FORMAT(`fin_offre`, "%d/%m/%Y") AS date_fin, `id_sous_theme`, `adresse_offre`, `code_postal_offre`, `ville_offre`,
		`courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `'.DB_PREFIX.'bsl_offre`.`zone_selection_villes` as `zone_offre`, `actif_offre`, `'.DB_PREFIX.'bsl_professionnel`.id_professionnel, `nom_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`,
		`competence_geo`, t1.`libelle_theme` as `libelle_sous_theme`, t1.`id_theme_pere`, t2.`libelle_theme` as `libelle_theme_pere`, `nom_departement`, `nom_region`, `nom_territoire`, `id_competence_geo`, 
		`'.DB_PREFIX.'bsl_professionnel`.`zone_selection_villes` as `zone_pro` 
		FROM `'.DB_PREFIX.'bsl_offre`
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`'.DB_PREFIX.'bsl_offre`.id_professionnel
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` as `t1` ON `t1`.id_theme=`'.DB_PREFIX.'bsl_offre`.id_sous_theme
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` as `t2` ON `t2`.id_theme=`t1`.id_theme_pere 
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
function get_liste_offres($flag = 1, $territoire_id = null, $user_pro_id = null) {
	
	$offres = null;
	
	$query = 'SELECT `id_offre`, `nom_offre`, DATE_FORMAT(`debut_offre`, "%d/%m/%Y") AS `date_debut`,
		DATE_FORMAT(`fin_offre`, "%d/%m/%Y") AS `date_fin`, `theme_pere`.`libelle_theme_court`, 
		`'.DB_PREFIX.'bsl_offre`.`zone_selection_villes`, `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`, 
		`nom_pro`, `competence_geo`, `id_competence_geo`, `nom_departement`, `nom_region`, `nom_territoire`
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

	if (isset($territoire_id) && $territoire_id > 0) {
		$query .= 'AND `competence_geo`="territoire" AND `id_competence_geo`= ? ';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if (isset($user_pro_id) && (int) $user_pro_id > 0){
		$query .= 'AND `'.DB_PREFIX.'bsl_professionnel`.id_professionnel = ? ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= 'ORDER BY `id_offre` DESC';
	$stmt = query_prepare($query,$params,$types);
	$offres = query_get($stmt);
	
	return $offres;
}

/* Mesures */
function get_liste_mesures($flag = 1, $tab_criteres = null) {

	global $conn;
	$t = [];
	$params = [];
	$types = '';
	
	$query = 'SELECT DISTINCT `'.DB_PREFIX.'bsl_mesure`.id_mesure, nom_mesure, DATE_FORMAT(`debut_mesure`, "%d/%m/%Y") AS date_debut,
		DATE_FORMAT(`fin_mesure`, "%d/%m/%Y") AS date_fin, `theme_pere`.libelle_theme_court, `'.DB_PREFIX.'bsl_mesure`.zone_selection_villes,
		nom_pro, `'.DB_PREFIX.'bsl_mesure`.`competence_geo`, `'.DB_PREFIX.'bsl_mesure`.`id_competence_geo`, nom_departement, nom_region, nom_territoire
		FROM `'.DB_PREFIX.'bsl_mesure`
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`'.DB_PREFIX.'bsl_mesure`.`id_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.id_theme=`'.DB_PREFIX.'bsl_mesure`.`id_sous_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`'.DB_PREFIX.'bsl_theme`.`id_theme_pere`
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
		';
		
	if(isset($tab_criteres) && count($tab_criteres)){
		$query .= 'JOIN `'.DB_PREFIX.'bsl_mesure_criteres` ON `'.DB_PREFIX.'bsl_mesure_criteres`.id_mesure=`'.DB_PREFIX.'bsl_mesure`.`id_mesure` ';
		foreach ($tab_criteres as $name => $selected_option) {
			if($selected_option != ''){
				$query .= ' AND `'.DB_PREFIX.'bsl_mesure_criteres`.nom_critere= ? AND `'.DB_PREFIX.'bsl_mesure_criteres`.valeur_critere= ? ';
				$params[] = $name;
				$params[] = $selected_option;
				$types .= 'ss';
			}
		}
	}
	$query .= 'WHERE actif_mesure= ? 
	ORDER BY `'.DB_PREFIX.'bsl_mesure`.id_mesure DESC';
	$params[] = $flag;
	$types .= 'i';	

	$stmt = query_prepare($query,$params,$types);
	$t = query_get($stmt);
	return $t;
}

function get_mesure_by_id($id){

	global $conn;
	$mesure = null;

	$query = 'SELECT `id_mesure`, `nom_mesure`, `description_mesure`, DATE_FORMAT(`debut_mesure`, "%d/%m/%Y") AS date_debut,
		DATE_FORMAT(`fin_mesure`, "%d/%m/%Y") AS date_fin, `id_sous_theme`, `adresse_mesure`, `code_postal_mesure`, `ville_mesure`,
		`courriel_mesure`, `telephone_mesure`, `site_web_mesure`, `'.DB_PREFIX.'bsl_mesure`.`competence_geo`, `'.DB_PREFIX.'bsl_mesure`.`id_competence_geo`, /*`'.DB_PREFIX.'bsl_mesure`.`zone_selection_villes` as `zone_mesure`,*/ `actif_mesure`, `'.DB_PREFIX.'bsl_professionnel`.id_professionnel, `nom_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `'.DB_PREFIX.'bsl_professionnel`.`competence_geo` as `competence_geo_pro`, `id_theme_pere`, `nom_departement`, `nom_region`, `nom_territoire`, `'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` as `id_competence_geo_pro`, `'.DB_PREFIX.'bsl_professionnel`.`zone_selection_villes` as `zone_pro` 
		FROM `'.DB_PREFIX.'bsl_mesure`
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`'.DB_PREFIX.'bsl_mesure`.id_professionnel
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.id_theme=`'.DB_PREFIX.'bsl_mesure`.id_sous_theme
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_mesure`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_mesure`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_mesure`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_mesure`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_mesure`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_mesure`.`id_competence_geo`
		WHERE id_mesure= ?';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) === 1) {
			$mesure = mysqli_fetch_assoc($result);
		}
		mysqli_stmt_close($stmt);
	}
	return $mesure;
}

/* Pros */
function get_liste_pros($flag, $territoire_id, $user_pro_id = null) { //tous les professionnel du territoire

	global $conn;
	$pros = [];

	$query = 'SELECT `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`, `nom_pro`, `type_pro`, `param_type`.`libelle` as `type`, `ville_pro`, `code_postal_pro`, GROUP_CONCAT(libelle_theme_court SEPARATOR ", ") AS `themes`, `competence_geo`, `zone_selection_villes`, `nom_departement`, `nom_region`, `nom_territoire`
		FROM `'.DB_PREFIX.'bsl_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes` ON `'.DB_PREFIX.'bsl_professionnel_themes`.`id_professionnel`=`'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_professionnel_themes`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__parametres` as `param_type` ON `param_type`.`id`=`'.DB_PREFIX.'bsl_professionnel`.`type_id` AND `param_type`.`liste`="type_pro"
		WHERE `actif_pro`= ? ';
	$params[] = (int) $flag;
	$types = 'i';

	if (isset($territoire_id) && $territoire_id > 0) {
		$query .= 'AND `competence_geo`="territoire" AND `id_competence_geo`= ? ';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if (isset($user_pro_id) && (int) $user_pro_id > 0){
		$query .= 'AND `'.DB_PREFIX.'bsl_professionnel`.id_professionnel = ? ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= ' GROUP BY `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,competence_geo
	ORDER BY `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel` DESC';

	$stmt = query_prepare($query,$params,$types);
	$pros = query_get($stmt);
	
	return $pros;
}

function get_pro_by_id($id){

	global $conn;
	$pro = null;

	$query = 'SELECT `id_professionnel`, `nom_pro`, `type_pro`, `type_id`, `param_type`.`libelle` as `type`, `statut_id`, `description_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`, `courriel_pro`, `telephone_pro`, `courriel_referent_boussole`, `telephone_referent_boussole`, `site_web_pro`, `visibilite_coordonnees`, `delai_pro`, `competence_geo`, `id_competence_geo`, `zone_selection_villes`, `editeur`, `actif_pro`, `user_derniere_modif` 
		FROM `'.DB_PREFIX.'bsl_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl__parametres` as `param_type` ON `param_type`.`id`=`'.DB_PREFIX.'bsl_professionnel`.`type_id` AND `param_type`.`liste`="type_pro"
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

function create_pro($nom, $type, $statut, $desc, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $visibilite, $courriel_ref, $tel_ref, $site, $delai, $competence_geo, $competence_geo_id, $editeur, $themes, $zone, $liste_villes) {

	global $conn;
	$created = false;
	$user_id= secu_get_current_user_id();

	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_professionnel`
		(`nom_pro`, `type_id`, `statut_id`, `description_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`,
		`courriel_pro`, `telephone_pro`, `visibilite_coordonnees`, `courriel_referent_boussole`, `telephone_referent_boussole`, `site_web_pro`, `delai_pro`, `competence_geo`, `id_competence_geo`, `editeur`, `creation_date`, `creation_user_id`)
		VALUES ( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , NOW(), ? )';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'siisssssssisssisiii', $nom, $type, $statut, $desc, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $visibilite, $courriel_ref, $tel_ref, $site, $delai, $competence_geo, $competence_geo_id, $editeur, $user_id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$created = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
		
		$last_id = mysqli_insert_id($conn);		
		$created2 = update_listes_pro($last_id, $themes, $zone, $liste_villes);
	}
	
	return $last_id;
}

function update_pro($pro_id, $nom, $type, $statut, $desc, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $visibilite, $courriel_ref, $tel_ref, $site, $delai, $competence_geo, $competence_geo_id, $editeur, $themes, $zone, $liste_villes){

	$user_id= secu_get_current_user_id();
	
	//mise à jour des champs principaux
	$query = 'UPDATE `'.DB_PREFIX.'bsl_professionnel`
		SET `nom_pro` = ?, `type_id` = ?, `statut_id` = ?, `description_pro` = ?, `adresse_pro` = ?, `code_postal_pro` = ?, `ville_pro` = ?, `code_insee_pro` = ?, `courriel_pro` = ?, `telephone_pro` = ?, `visibilite_coordonnees` = ?, `courriel_referent_boussole` = ?, `telephone_referent_boussole` = ?, `site_web_pro` = ?, `delai_pro` = ?, `zone_selection_villes` = ?, `editeur` = ? , `last_edit_date` = NOW(), `last_edit_user_id` = ? ';

	$terms = array($nom, $type, $statut, $desc, $adresse, $code_postal, $ville, $code_insee, $courriel, $tel, $visibilite, $courriel_ref, $tel_ref, $site, $delai, $zone, $editeur, $user_id);
	$terms_type = "siisssssssisssiiii";
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
	$stmt = query_prepare($query,$terms,$terms_type);
	$updated = query_do($stmt);
	
	$updated_listes = update_listes_pro($pro_id, $themes, $zone, $liste_villes);
	
	return ($updated+$updated_listes);
}

function update_listes_pro($pro_id, $themes, $zone, $liste_villes){

//mise à jour des thèmes (ce sont des checkboxes : on enlève les thèmes décochés puis on importe le différentiel)
	$query_dt = 'DELETE FROM `'.DB_PREFIX.'bsl_professionnel_themes` WHERE `id_professionnel` = ? ';
	$terms_dt[] = $pro_id;
	$terms_type_dt = 'i';
	if (isset($themes)){
		$query_dt .= 'AND `id_theme` NOT IN (';
		foreach ($themes as $theme_id) {
			$query_dt .= '?,';
			$terms_dt[] = $theme_id;
			$terms_type_dt .= 'i';
		}
		$query_dt = substr($query_dt, 0, -1).')';
	}
	$stmt = query_prepare($query_dt,$terms_dt,$terms_type_dt);
	$updated_dt = query_do($stmt);
	
	$updated_it = null;
	if (isset($themes)){
		$query_t = 'INSERT IGNORE INTO `'.DB_PREFIX.'bsl_professionnel_themes`(`id_professionnel`, `id_theme`) VALUES ';
		$terms_t = array();
		$terms_type_t = null;
		foreach ($themes as $theme_id) {
			$query_t .= '(?, ?), ';
			$terms_t[] = $pro_id;
			$terms_t[] = $theme_id;
			$terms_type_t .= "ii";
		}
		$query_t = substr($query_t, 0, -2); //on enleve " '" à la fin de la requête
		$stmt = query_prepare($query_t,$terms_t,$terms_type_t);
		$updated_it = query_do($stmt);
	}

	//mise à jour des villes (1 checkbox et une liste multiple : on supprime les villes désélectionnées et on importe les villes différentielles)
	$query_dv = 'DELETE FROM `'.DB_PREFIX.'bsl_professionnel_villes` WHERE `id_professionnel` = ? ';
	$terms_dv[] = $pro_id;
	$terms_type_dv = 'i';
	if (isset($liste_villes)){
		$query_dv .= 'AND `code_insee` NOT IN (';
		foreach ($liste_villes as $code_insee_ville) {
			$query_dv .= '?,';
			$terms_dv[] = $code_insee_ville;
			$terms_type_dv .= 's';
		}
		$query_dv = substr($query_dv, 0, -1).')';
	}
	$stmt = query_prepare($query_dv,$terms_dv,$terms_type_dv);
	$updated_dv = query_do($stmt);
	
	$updated_iv = null;
	if ($zone && isset($liste_villes)){
		$query_iv = 'INSERT IGNORE INTO `'.DB_PREFIX.'bsl_professionnel_villes`(`id_professionnel`, `code_insee`) VALUES ';
		$terms_iv = array();
		$terms_type_iv = null;
		foreach ($liste_villes as $code_insee_ville) {
			$query_iv .= '(?, ?), ';
			$terms_iv[] = $pro_id;
			$terms_iv[] = $code_insee_ville;
			$terms_type_iv .= "is";
		}
		$query_iv = substr($query_iv, 0, -2);
		$stmt = query_prepare($query_iv,$terms_iv,$terms_type_iv);
		$updated_iv = query_do($stmt);
	}
	
	return ($updated_it+$updated_iv+$updated_dt+$updated_dv);
}

function get_incoherences_themes_by_pro($pro_id, $themes){

	//on cherche les offres de service du pro pour les thèmes qui ne seraient plus gérés
	$query_ot = 'SELECT `id_offre`, `nom_offre` FROM `'.DB_PREFIX.'bsl_offre` 
		JOIN `'.DB_PREFIX.'bsl_theme` ON id_sous_theme=id_theme 
		WHERE `actif_offre`=1 AND id_professionnel = ? ';
	$terms_ot[] = $pro_id;
	$terms_type_ot = 'i';
	if (isset($themes)){
		$tmp_ot = 'AND id_theme_pere NOT IN ( ';
		foreach ($themes as $rowt) {
			if(isset($rowt['id_professionnel']) && $rowt['id_professionnel']) {
				$tmp_ot .= '?,';
				$terms_ot[] = $rowt['id_theme'];
				$terms_type_ot .= 'i';
			}
		}
		if(strlen($terms_type_ot)>1) $query_ot = $query_ot.substr($tmp_ot, 0, -1).')';
	}
	
	$stmt = query_prepare($query_ot,$terms_ot,$terms_type_ot);
	$themes_incoherents = query_get($stmt);
	return $themes_incoherents;

}

function get_incoherences_villes_by_pro($pro_id, $row){

	//on cherche les offres de service du pro hors zone géographique
	$query_ov = 'SELECT `'.DB_PREFIX.'bsl_offre`.`id_offre`, `nom_offre` FROM `'.DB_PREFIX.'bsl_offre` 
		JOIN `'.DB_PREFIX.'bsl_offre_criteres` 
		ON `'.DB_PREFIX.'bsl_offre`.`id_offre`=`'.DB_PREFIX.'bsl_offre_criteres`.`id_offre` AND `nom_critere` LIKE "villes"
		WHERE `actif_offre`=1 AND id_professionnel = ? ';
	$terms_ov[] = $pro_id;
	$terms_type_ov = 'i';
	if(isset($row)){
		$query_ov .= 'AND `valeur_critere` NOT IN (';
		foreach ($row as $rowv) {
			$query_ov .= '?,';
			$terms_ov[] = $rowv['code_insee'];
			$terms_type_ov .= 's';
		}
		$query_ov = substr($query_ov, 0, -1).')';
	}
	
	$stmt = query_prepare($query_ov,$terms_ov,$terms_type_ov);
	$row_incoherentes = query_get($stmt);
	return $row_incoherentes;
}

/* Utilisateurs */
function get_liste_users($flag, $territoire_id, $user_pro_id = null) { //tous les utilisateurs du territoire

	global $conn;
	$users = [];

	$query = 'SELECT `id_utilisateur`, `'.DB_PREFIX.'bsl_utilisateur`.`id_statut`, `nom_utilisateur`,
		`email`, `libelle_statut`, `nom_pro`, `nom_territoire`
		FROM `'.DB_PREFIX.'bsl_utilisateur`
		JOIN `'.DB_PREFIX.'bsl__droits` ON `'.DB_PREFIX.'bsl__droits`.`id_statut`=`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_utilisateur`.`id_metier`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`=`'.DB_PREFIX.'bsl_utilisateur`.`id_metier`
		WHERE `actif_utilisateur`= ? ';
	$params[] = (int) $flag;
	$types = 'i';

	if (isset($territoire_id) && $territoire_id > 0) {
		$query .= 'AND (`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`=2 AND `id_metier`= ?)
			OR (`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`=3 AND `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire"
				AND `id_competence_geo`= ?) ';
		$params[] = (int) $territoire_id;
		$params[] = (int) $territoire_id;
		$types .= 'ii';
	}
	if (isset($user_pro_id) && (int) $user_pro_id > 0){
		$query .= 'AND `'.DB_PREFIX.'bsl_professionnel`.id_professionnel = ? AND `'.DB_PREFIX.'bsl_utilisateur`.`id_statut`=3 ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= ' ORDER BY `'.DB_PREFIX.'bsl_utilisateur`.`id_statut` ASC,`id_metier` ASC';
	$stmt = query_prepare($query,$params,$types);
	$users = query_get($stmt);
	
	return $users;
}

/* Formulaire */

function get_liste_formulaires($flag_actif = 1, $territoire_id = null) {

	global $conn;
	$formulaires= null;
	$types='';

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` as `id`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme_court` as `theme`, `'.DB_PREFIX.'bsl_territoire`.`nom_territoire`, count(DISTINCT `'.DB_PREFIX.'bsl_formulaire__page`.`id_page`) as `nb_pages`,  count(DISTINCT `'.DB_PREFIX.'bsl_formulaire__question`.`id_question`) as `nb_questions`
		FROM `'.DB_PREFIX.'bsl_formulaire`
		JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_formulaire`.`id_territoire` /*AND `'.DB_PREFIX.'bsl_territoire`.`actif_territoire`=1*/ ';
	if (isset($territoire_id) && $territoire_id > 0) {
		$query .= 'AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=? ';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	$query .= 'LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__question` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page` AND `'.DB_PREFIX.'bsl_formulaire__question`.`actif`=1
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`actif`=? ';
	$params[] = (int) $flag_actif;
	$types .= 'i';
	
	$query .= 'GROUP BY `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme_court`, `'.DB_PREFIX.'bsl_territoire`.`nom_territoire`
		ORDER BY `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_formulaire__page`.`id_page`';

	$stmt = query_prepare($query,$params,$types);
	$formulaires = query_get($stmt);
	
	return $formulaires;
}

function get_formulaire_by_id($id){

	global $conn;
	$tmp_id = 0;
	$tmp_p = 0;
	$meta = [];
	$pages = [];
	$questions = [];

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`,`'.DB_PREFIX.'bsl_theme`.`libelle_theme_court`, `'.DB_PREFIX.'bsl_territoire`.`nom_territoire`, `'.DB_PREFIX.'bsl_formulaire`.`nb_pages`, `'.DB_PREFIX.'bsl_formulaire`.`actif`, `'.DB_PREFIX.'bsl_formulaire__page`.`id_page`, `'.DB_PREFIX.'bsl_formulaire__page`.`titre`, `'.DB_PREFIX.'bsl_formulaire__page`.`ordre` AS `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__page`.`aide`, `'.DB_PREFIX.'bsl_formulaire__question`.`id_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre` AS `ordre_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`, `'.DB_PREFIX.'bsl_formulaire__question`.`taille`, `'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire`, `'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse`, `'.DB_PREFIX.'bsl_formulaire__reponse`.`libelle` AS `reponse`
		FROM `'.DB_PREFIX.'bsl_formulaire`
		JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_formulaire`.`id_territoire` AND `'.DB_PREFIX.'bsl_territoire`.`actif_territoire`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__question` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page` AND `'.DB_PREFIX.'bsl_formulaire__question`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` ON `'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__question`.`id_reponse`
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` = ?
		ORDER BY `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre`';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_formulaire, $libelle_theme_court, $nom_territoire, $nb_pages, $actif, $id_page, $titre, $ordre_page, $aide, $id_question, $ordre_question, $libelle_question, $html_name, $type, $taille, $obligatoire, $id_reponse, $reponse);
	while (mysqli_stmt_fetch($stmt)) {
		if ($id_formulaire != $tmp_id) { //données du formulaire
			$meta = array('id' => $id_formulaire, 'theme' => $libelle_theme_court, 'territoire' => ($nom_territoire) ? $nom_territoire : 'national', 'actif' => $actif, 'nb' => $nb_pages);
			$tmp_id = $id_formulaire;
		}
		if ($id_page != $tmp_p) { //données des pages du formulaire
			$pages[] = array('id' => $id_page, 'titre' => $titre, 'ordre' => $ordre_page, 'aide' => $aide);
			$tmp_p = $id_page;
		}
		$questions[$id_page][] = array('id' => $id_question, 'ordre' => $ordre_question, 'libelle' => $libelle_question, 'name' => $html_name, 'type' => $type, 'taille' => $taille, 'obligatoire' => $obligatoire, 'id_reponse' => $id_reponse, 'reponse' => $reponse);
	}
	mysqli_stmt_close($stmt);
	return [$meta, $pages, $questions];
}

function get_reponse_by_id($id){

	global $conn;
	$valeurs = [];
	$libelle = null;

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire__reponse`.`libelle` as `libelle_reponse`, `id_valeur`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`libelle` as `libelle_valeur`, `valeur`, `ordre`, `defaut`, `actif`
		FROM `'.DB_PREFIX.'bsl_formulaire__reponse` 
		JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` ON `'.DB_PREFIX.'bsl_formulaire__valeur`.`id_reponse`= `'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse`
		WHERE `'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse`= ?
		ORDER BY `ordre` ASC';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_assoc($result)) {
			if(!$libelle) $libelle=$row['libelle_reponse'];
			$valeurs[] = array('id_valeur' => $row['id_valeur'], 'libelle_valeur' => $row['libelle_valeur'], 'valeur' => $row['valeur'], 'ordre' => $row['ordre'], 'defaut' => $row['defaut'], 'actif' => $row['actif']);
		}
		mysqli_stmt_close($stmt);
	}
	return [$libelle, $valeurs];
}


/* autres */

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

function get_liste_themes($actif = null, $pro_id = null) {

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
	$stmt = query_prepare($query,$params,$types);
	$themes = query_get($stmt);

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

function get_reponses() {

	global $conn;
	$reponses = null;
	$query = 'SELECT `id_reponse`, `libelle` 
		FROM `'.DB_PREFIX.'bsl_formulaire__reponse` WHERE 1 
		ORDER BY `libelle` ASC';
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_assoc($result)) {
		$reponses[] = $row;
	}
	return $reponses;
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
		$stmt = query_prepare($query_i,$params,$types);
		$updated = query_do($stmt);
	}
	return $updated;
}

function get_villes_by_territoire($id) {

	global $conn;
	$row = null;
	$query = 'SELECT `'.DB_PREFIX.'bsl__ville`.`code_insee`, `'.DB_PREFIX.'bsl__ville`.`code_postal`, 
		`'.DB_PREFIX.'bsl__ville`.`nom_ville` 
		FROM `'.DB_PREFIX.'bsl__ville` 
		JOIN `'.DB_PREFIX.'bsl_territoire_villes` ON `'.DB_PREFIX.'bsl_territoire_villes`.`code_insee`=`'.DB_PREFIX.'bsl__ville`.`code_insee` 
		WHERE `id_territoire`= ? 
		ORDER BY nom_ville';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($ville = mysqli_fetch_assoc($result)) {
			$row[] = $ville;
		}
		mysqli_stmt_close($stmt);
	}
	return $row;
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
	$user_id= secu_get_current_user_id();

	$query = 'INSERT INTO `'.DB_PREFIX.'bsl_utilisateur`
		(`nom_utilisateur`, `email`, `motdepasse`, `date_inscription`, `id_statut`, `id_metier`, `creation_user_id`)
		VALUES (? , ? , ? , NOW(), ? , ?, ?)';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'sssssi', $nom_utilisateur, $courriel, $mdp, $statut, $attache, $user_id);
	
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$created = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	return $created;
}

function update_user($id, $nom, $courriel){

	global $conn;
	$updated = false;
	$user_id= secu_get_current_user_id();
	
	$query = 'UPDATE `'.DB_PREFIX.'bsl_utilisateur` 
		SET `nom_utilisateur` = ?, `email` = ?, `last_edit_date` = NOW(), `last_edit_user_id` = ? 
		WHERE `id_utilisateur` = ?';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ssii', $nom, $courriel, $id, $user_id);
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
				SET `motdepasse` = ?, `last_edit_date` = NOW(), `last_edit_user_id` = ? 
				WHERE `id_utilisateur` = ?';
			//pas de modif du statut autorisée. sinon il faudrait ajouter : `id_statut` = \"".$_POST["statut"]."\"
			$stmt2 = mysqli_prepare($conn, $query);
			$hache = secu_password_hash($nouveaumotdepasse);
			mysqli_stmt_bind_param($stmt2, 'sii', $hache, $user_id, $id);
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
		JOIN `'.DB_PREFIX.'bsl__droits` ON `'.DB_PREFIX.'bsl__droits`.`id_statut`=`'.DB_PREFIX.'bsl_utilisateur`.`id_statut`
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

//************ récupération des éléments de la page du formulaire
function get_formulaire_mesure(){
	
	global $conn;

	$query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_formulaire`.`nb_pages`, `'.DB_PREFIX.'bsl_formulaire__page`.`titre`, `'.DB_PREFIX.'bsl_formulaire__page`.`ordre` AS `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__page`.`aide`, 
		`'.DB_PREFIX.'bsl_formulaire__question`.`id_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`, 
		`'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`, `'.DB_PREFIX.'bsl_formulaire__question`.`taille`, 
		`'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`libelle` AS `libelle_reponse`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`defaut` 
		FROM `'.DB_PREFIX.'bsl_formulaire` 
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page` AND `'.DB_PREFIX.'bsl_formulaire__question`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` ON `'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__question`.`id_reponse`
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` ON `'.DB_PREFIX.'bsl_formulaire__valeur`.`id_reponse`=`'.DB_PREFIX.'bsl_formulaire__reponse`.`id_reponse` AND `'.DB_PREFIX.'bsl_formulaire__valeur`.`actif`=1
		WHERE `'.DB_PREFIX.'bsl_formulaire`.`actif`=1 AND `'.DB_PREFIX.'bsl_formulaire`.`type`="mesure"
		ORDER BY `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`ordre`';
	$stmt = mysqli_prepare($conn, $query);
	//mysqli_stmt_bind_param($stmt, 'si', $_SESSION['besoin'], $etape);
	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_formulaire, $nb_pages, $titre, $ordre_page, $aide, $id_question, $libelle_question, $html_name, $type, $taille, $obligatoire, $libelle_reponse, $valeur, $defaut);
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
		if ($libelle_question != $tmp_que) { //on récupère les questions
			$questions[] = array('id' => $id_question, 'libelle' => $libelle_question, 'name' => $html_name, 'type' => $type, 'taille' => $taille, 'obligatoire' => $obligatoire);
			$tmp_que = $libelle_question;
		}
		$reponses[$id_question][] = array('name' => $html_name, 'libelle' => $libelle_reponse, 'valeur' => $valeur, 'defaut' => $defaut);  //on récupère les réponses
	}
	mysqli_stmt_close($stmt);
	return [$meta, $questions, $reponses];
}

function archive($objet, $id, $etat = 0){

	global $conn;
	$updated = false;

	$table = '';
	switch($objet){
		case 'offre':
		case 'mesure':
		case 'utilisateur':
			$table = 'bsl_'.$objet;
			$champ_a = 'actif_'.$objet;
			$champ_i = 'id_'.$objet;
			break;
		case 'pro':
			$table = 'bsl_professionnel';
			$champ_a = 'actif_'.$objet;
			$champ_i = 'id_professionnel';
			break;
		case 'formulaire':
			$table = 'bsl_'.$objet;
			$champ_a = 'actif';
			$champ_i = 'id_'.$objet;
			break;
	}
	
	if($table){
		$query = 'UPDATE `'.DB_PREFIX.$table.'` 
			SET `'.$champ_a.'` = ?
			WHERE `'.$champ_i.'` = ?';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'ii', $etat, $id);
		check_mysql_error($conn);

		if (mysqli_stmt_execute($stmt)) {
			$updated = mysqli_stmt_affected_rows($stmt) > 0;
			mysqli_stmt_close($stmt);
		}
	}
	return $updated;
}

function get_liste_parametres($liste) {

	global $conn;
	$rows = null;
	$query = 'SELECT `id`, `libelle` FROM `'.DB_PREFIX.'bsl__parametres` WHERE liste = ? ';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 's', $liste);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}
	}
	return $rows;
}

function get_liste_droits() {

	global $conn;
	$rows = null;
	$query = 'SELECT `id_statut`, `libelle_statut`, `demande_r`, `demande_w`, `offre_r`, `offre_w`, `mesure_r`, `mesure_w`, `professionnel_r`, `professionnel_w`, `utilisateur_r`, `utilisateur_w`, `formulaire_r`, `formulaire_w`, `theme_r`, `theme_w`, `territoire_r`, `territoire_w` FROM `'.DB_PREFIX.'bsl__droits` WHERE 1 ORDER BY `demande_r` DESC, `id_statut`';
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

function create_formulaire($theme, $territoire, $id_p, $ordre_p, $titre_p, $id_q, $page_q, $ordre_q, $titre_q, $reponse_q, $type_q, $name_q, $user_id) {

	return null;
}

function update_formulaire($formulaire_id, $id_p, $ordre_p, $titre_p, $id_q, $page_q, $ordre_q, $titre_q, $reponse_q, $type_q, $name_q, $user_id) {

	global $conn;
	$updated = false;
	
	//update questions
	if (isset($id_q)) {
		foreach($id_q as $key_p=>$id_qp){
			foreach($id_qp as $key=>$id){
				
				$new_page_id = null;
				foreach($ordre_p as $k=>$o){ 
					if ($o==$page_q[$key_p][$key]){
						$new_page_id=$id_p[$k];
					}
				}
			
				//si on a un ordre et un titre, on met à jour
				if ($ordre_q[$key_p][$key] && $titre_q[$key_p][$key]){
					$query = 'UPDATE `'.DB_PREFIX.'bsl_formulaire__question` 
						SET `id_page`= ?, `ordre`= ?, `libelle` = ?, `id_reponse`= ?, `type` = ? 
						WHERE `id_question`= ? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'iisisi', $new_page_id, $ordre_q[$key_p][$key], $titre_q[$key_p][$key], $reponse_q[$key_p][$key], $type_q[$key_p][$key], $id);
				
					if (mysqli_stmt_execute($stmt)) {
						$updated += mysqli_stmt_affected_rows($stmt) > 0;
						mysqli_stmt_close($stmt);
					}
				}
				
				//si l'ordre et le titre ont été effacés, on supprime la question
				if (!$ordre_q[$key_p][$key] && !$titre_q[$key_p][$key]){
					//ajouter test pour vérifier qu'il n'y a plus de questions ?
					$query = 'DELETE FROM `'.DB_PREFIX.'bsl_formulaire__question` 
						WHERE `id_question`= ? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'i', $id);
					
					if (mysqli_stmt_execute($stmt)) {
						$updated += mysqli_stmt_affected_rows($stmt) > 0;
						mysqli_stmt_close($stmt);
					}
				}
				
				//si on n'a pas d'id mais toutes les infos, c'est une nouvelle question à créer
				if(!$id && $ordre_q[$key_p][$key] && $titre_q[$key_p][$key] && $name_q[$key_p][$key] && $reponse_q[$key_p][$key] && $type_q[$key_p][$key]){
					$query = 'INSERT INTO `'.DB_PREFIX.'bsl_formulaire__question`(`id_page`, `libelle`, `html_name`, `ordre`, `type`, `id_reponse`)
						VALUES (?, ?, ?, ?, ?, ?) ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'issisi', $new_page_id, $titre_q[$key_p][$key], $name_q[$key_p][$key], $ordre_q[$key_p][$key], $type_q[$key_p][$key], $reponse_q[$key_p][$key]);
				
					if (mysqli_stmt_execute($stmt)) {
						$updated += mysqli_stmt_affected_rows($stmt) > 0;
						mysqli_stmt_close($stmt);
					}
				}
			}
		}
	}

	//update et delete pages
	if (isset($id_p)) {
		foreach($id_p as $key=>$id){
			
			//si on a un ordre et un titre, on met à jour
			if ($ordre_p[$key] && $titre_p[$key]){
				$query = 'UPDATE `'.DB_PREFIX.'bsl_formulaire__page` 
					SET `ordre`= ?, `titre` = ?
					WHERE `id_page`= ? ';
				$stmt = mysqli_prepare($conn, $query);
				mysqli_stmt_bind_param($stmt, 'isi', $ordre_p[$key], $titre_p[$key], $id);
				
				if (mysqli_stmt_execute($stmt)) {
					$updated += mysqli_stmt_affected_rows($stmt) > 0;
					mysqli_stmt_close($stmt);
				}
			}
			
			//si l'ordre et le titre ont été effacés, on supprime la question
			if (!$ordre_p[$key] && !$titre_p[$key]){
				//ajouter test pour vérifier qu'il n'y a plus de questions ?
				$query = 'DELETE FROM `'.DB_PREFIX.'bsl_formulaire__page` 
					WHERE `id_page`= ? ';
				$stmt = mysqli_prepare($conn, $query);
				mysqli_stmt_bind_param($stmt, 'i', $id);
				
				if (mysqli_stmt_execute($stmt)) {
					$updated += mysqli_stmt_affected_rows($stmt) > 0;
					mysqli_stmt_close($stmt);
				}
			}
			
			//si pas d'id mais un titre et un ordre, on crée
			if ($titre_p[$key] && $ordre_p[$key] && !$id){
				$query = 'INSERT INTO `'.DB_PREFIX.'bsl_formulaire__page`(`id_formulaire`, `titre`, `ordre`)
					VALUES (?, ?, ?)';
				$stmt = mysqli_prepare($conn, $query);
				mysqli_stmt_bind_param($stmt, 'isi', $formulaire_id, $titre_p[$key], $ordre_p[$key]);
				
				if (mysqli_stmt_execute($stmt)) {
					$updated += mysqli_stmt_affected_rows($stmt) > 0;
					mysqli_stmt_close($stmt);
				}
			}
			
		}
	}
	
	return $updated;
}