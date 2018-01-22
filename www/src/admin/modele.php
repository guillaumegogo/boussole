<?php

function get_nb_nouvelles_demandes() {

	global $conn;
	$nb = 0;

	$query = 'SELECT COUNT(`id_demande`) AS `nb`
		FROM `'.DB_PREFIX.'bsl_demande` AS `d`
		JOIN `'.DB_PREFIX.'bsl_offre` AS `o` ON `o`.id_offre=`d`.id_offre ';
	
	if($pro_id = secu_get_user_pro_id()) {
		$query .= 'AND `o`.id_professionnel = ? 
		WHERE date_traitement IS NULL ';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'i', $pro_id);
	
	}else if($territoire_id = secu_get_territoire_id()) {
		$query .= 'JOIN `'.DB_PREFIX.'bsl_professionnel` as p ON `p`.id_professionnel=`o`.id_professionnel 
		AND `p`.competence_geo = "territoire" AND `p`.id_competence_geo = ? 
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

function get_criteres_offre($id_offre, $id_theme, $id_territoire=0){

	global $conn;

	$query = 'SELECT `f`.`id_formulaire`, `q`.`libelle` AS `libelle_question`, `q`.`html_name`, `q`.`type`, `q`.`taille`, `q`.`obligatoire`, 
		`v`.`libelle`, `v`.`valeur`,
		`oc`.`id_offre` FROM `'.DB_PREFIX.'bsl_formulaire` as f
		JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.`id_theme`=`f`.`id_theme`
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` AS `fp` ON `fp`.`id_formulaire`=`f`.`id_formulaire` AND `fp`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` AS `q` ON `q`.`id_page`=`fp`.`id_page` AND `q`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` AS `fr` ON `q`.`id_reponse`=`fr`.`id_reponse`
		JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` AS `v` ON `v`.`id_reponse`=`fr`.`id_reponse` AND `v`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_offre_criteres` AS `oc` ON `oc`.`nom_critere`=`q`.`html_name` AND `oc`.`valeur_critere`=`v`.`valeur` AND `oc`.`id_offre`= ?
		WHERE `f`.`type`="offre" AND `f`.`actif`=1 AND `t`.`id_theme`= ? /*AND (`f`.`id_territoire`=? OR `f`.`id_territoire`=0)*/
		ORDER BY `t`.`id_territoire` DESC, `fp`.`ordre`, `q`.`ordre`, `v`.`ordre`';

	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ii', $id_offre, $id_theme); //, $id_territoire
	check_mysql_error($conn);
	
if(DEBUG){
	$print_sql = $query;
	foreach(array( $id_offre, $id_theme, $id_territoire) as $term){
		$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
	}
	echo "<!--<pre>".$print_sql."</pre>-->";
}

	$questions = [];
	$reponses = [];
	$tmp_id = null;
	$tmp_que = '';
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_assoc($result)) {
			if(is_null($tmp_id)){
				$tmp_id = $row['id_formulaire'];
			}
			if($tmp_id == $row['id_formulaire']){
				//on récupère les questions
				if ($row['libelle_question'] != $tmp_que) { 
					$questions[] = array('libelle' => $row['libelle_question'], 'name' => $row['html_name'], 'type' => $row['type'], 'obligatoire' => $row['obligatoire']);
					$tmp_que = $row['libelle_question'];
				}
				//on récupère les réponses
				$reponses[$row['html_name']][] = array('libelle' => $row['libelle'], 'valeur' => $row['valeur'], 'selectionne' => ($row['id_offre']) ? 'selected' : '');
			}else{
				break; //au cas où 2 formulaires (territoire et national), on arrête après la lecture du formulaire territorial
			}
		}
		mysqli_stmt_close($stmt);
	}
	return [$questions, $reponses];
}

function get_criteres_mesure($id_mesure){

	global $conn;

	$query = 'SELECT `f`.`id_formulaire`, `q`.`libelle` AS `libelle_question`, `q`.`html_name`, `q`.`type`, `q`.`taille`, `q`.`obligatoire`,
		`v`.`libelle`, `v`.`valeur`,
		`mc`.`id_mesure` 
		FROM `'.DB_PREFIX.'bsl_formulaire` AS `f`
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` AS `fp` ON `fp`.`id_formulaire`=`f`.`id_formulaire` AND `fp`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` AS `q` ON `q`.`id_page`=`fp`.`id_page` AND `q`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` AS `fr` ON `q`.`id_reponse`=`fr`.`id_reponse`
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` AS `v` ON `v`.`id_reponse`=`fr`.`id_reponse` AND `v`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_mesure_criteres` AS `mc` ON `mc`.`nom_critere`=`q`.`html_name` AND `mc`.`valeur_critere`=`v`.`valeur` AND `mc`.`id_mesure`= ?
		WHERE `f`.`type`="mesure" AND `f`.`actif`=1 
		ORDER BY `fp`.`ordre`, `q`.`ordre`, `v`.`ordre`';

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
function get_demande_by_id($id, $type_id=null){

	global $conn;
	$demande = null;

	$query = 'SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `criteres` AS `profil`, `commentaire`,
		`o`.nom_offre, `p`.nom_pro
		FROM `'.DB_PREFIX.'bsl_demande` AS `d`
		LEFT JOIN `'.DB_PREFIX.'bsl_recherche` AS `r` ON `r`.id_recherche=`d`.id_recherche
		JOIN `'.DB_PREFIX.'bsl_offre` AS `o` ON `o`.id_offre=`d`.id_offre
		JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `o`.id_professionnel=`p`.id_professionnel ';
	if ($type_id=="hash"){
		$query .= 'WHERE id_hashe = ?';
	}else{
		$query .= 'WHERE id_demande = ?';
	}
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
	$query = 'SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `criteres` AS `profil`, `o`.nom_offre,
		`o`.id_professionnel, `p`.nom_pro
		FROM `'.DB_PREFIX.'bsl_demande` AS `d`
		LEFT JOIN `'.DB_PREFIX.'bsl_recherche` AS `r` ON `r`.id_recherche=`d`.id_recherche
		JOIN `'.DB_PREFIX.'bsl_offre` AS `o` ON `o`.id_offre = `d`.id_offre
		JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `o`.id_professionnel = `p`.id_professionnel
		WHERE date_traitement IS '.(($flag_traite) ? 'NOT' : '').' NULL ';
	if ((int) $territoire_id > 0) {
		$query .= 'AND `p`.`competence_geo`="territoire" AND `p`.`id_competence_geo`= ? ';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if ((int) $user_pro_id > 0) {
		$query .= 'AND `o`.id_professionnel = ? ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= ' ORDER BY date_demande DESC';
	
	$stmt = query_prepare($query,$params,$types);
	$demandes = query_get($stmt);
	return $demandes;
}

function get_liste_recherches(){

	global $conn;

	$recherches = null;
	$query = 'SELECT `r`.`id_recherche`, `date_recherche`, `code_insee`, `besoin`, `criteres`, `nb_offres`, GROUP_CONCAT(`id_demande` SEPARATOR "," ) as `demandes`
		FROM `'.DB_PREFIX.'bsl_recherche` AS `r`
		LEFT JOIN `'.DB_PREFIX.'bsl_demande` AS `d` ON `r`.id_recherche=`d`.id_recherche
		GROUP BY `id_recherche`, `date_recherche`, `code_insee`, `besoin`, `criteres`, `nb_offres`
		ORDER BY date_recherche DESC';
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_assoc($result)) {
		$recherches[] = $row;
	}
	return $recherches;
}

function update_demande($id, $commentaire){

	global $conn;
	$updated = false;
	$user_id=secu_get_current_user_id();

	$query = 'UPDATE `'.DB_PREFIX.'bsl_demande` AS `d`
		SET `date_traitement` = NOW(),
		`commentaire` = ?,
		`user_derniere_modif`= ?
		WHERE `d`.`id_demande` = ?';

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
		FROM `'.DB_PREFIX.'bsl_professionnel` AS `p`
		WHERE `p`.id_professionnel = ? ';

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

	$query1 = 'DELETE FROM `'.DB_PREFIX.'bsl_offre_criteres` WHERE `id_offre` = ? AND `nom_critere`!="villes" '; //les villes sont gérées dans update_offre
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
		`telephone_mesure`, `site_web_mesure`, `competence_geo`, `id_competence_geo`, `creation_date`, `creation_user_id`)
		SELECT ?, ?, ?, ?, ?,`adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`,`courriel_pro`,
		`telephone_pro`, `site_web_pro`, `competence_geo`, `id_competence_geo`, NOW(), ?
		FROM `'.DB_PREFIX.'bsl_professionnel` AS `p`
		WHERE `p`.id_professionnel = ? ';

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
		`competence_geo`= ?,`id_competence_geo`= ?, `last_edit_date` = NOW(), `last_edit_user_id` = ?
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

function get_villes_by_competence_geo($competence, $id) {

	global $conn;
	$row = null;

	$query = 'SELECT `v`.`code_insee`, MIN(`v`.`code_postal`) AS `code_postal`, `v`.`nom_ville`
		FROM `'.DB_PREFIX.'bsl__ville` AS `v` ';
	switch ($competence) {
		case "territoire":
			$query .= ' JOIN `'.DB_PREFIX.'bsl_territoire_villes` AS `tv` ON `tv`.code_insee=`v`.code_insee
			WHERE id_territoire= ? ';
			break;
		case "departemental":
			$query .= ' WHERE SUBSTR(`v`.code_insee,1,2)= ? ';
			break;
		case "regional":
			$query .= ' JOIN `'.DB_PREFIX.'bsl__departement` AS `dep` ON SUBSTR(`v`.code_insee,1,2)=`dep`.id_departement AND id_region= ? ';
			break;
		case "national":
			break;
	}
	$query .= 'GROUP BY `v`.`code_insee`, `v`.`nom_ville` ORDER BY `v`.`nom_ville`';

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

	$query = 'SELECT `nom_ville`, `v`.`code_insee`, `code_postal` 
		FROM `'.DB_PREFIX.'bsl__ville` AS `v`
		JOIN `'.DB_PREFIX.'bsl_professionnel_villes` AS `pv` ON `pv`.`code_insee`=`v`.`code_insee`
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

	$query = 'SELECT `t`.`id_territoire`, `nom_territoire`, `actif_territoire` AS `actif`, COUNT(`code_insee`) as `c`, GROUP_CONCAT(DISTINCT(LEFT(`code_insee`,2)) SEPARATOR ", ") AS `dep`
		FROM `'.DB_PREFIX.'bsl_territoire` AS `t`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` AS `tv` ON `tv`.`id_territoire`=`t`.`id_territoire`
		WHERE `nom_territoire` != "" ';
	$params = array();
	$types = '';
	if (isset($actif)) {
		$query .= ' AND `actif_territoire` = ? ';
		$params[] = (int) $actif;
		$types .= 'i';
	}
	if (isset($id) && $id > 0) {
		$query .= ' AND `t`.`id_territoire`= ? ';
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
		$query .= ' AND `editeur`= ? ';
		$params[] = 1;
		$types .= 'i';
	}
	if(isset($zone) && $zone) {
		$query .= ' AND `competence_geo`= ? ';
		$params[] = $zone;
		$types .= 's';
	}
	if(isset($zone_id) && $zone_id) {
		$query .= ' AND `id_competence_geo`= ? ';
		$params[] = (int) $zone_id;
		$types .= 'i';
	}
	if (isset($user_pro_id)) {
		$query .= ' AND id_professionnel = ? ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= ' ORDER BY `nom_pro` ';
	$stmt = query_prepare($query,$params,$types);
	$pros = query_get($stmt);
	return $pros;
}

function get_offre_by_id($id){

	global $conn;
	$offre = null;

	$query = 'SELECT `id_offre`, `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, "%d/%m/%Y") AS date_debut,
		DATE_FORMAT(`fin_offre`, "%d/%m/%Y") AS date_fin, `id_sous_theme`, `adresse_offre`, `code_postal_offre`, `ville_offre`,
		`courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `o`.`zone_selection_villes` AS `zone_offre`, `actif_offre`, 
		`p`.id_professionnel, `nom_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `competence_geo`, 
		t1.`libelle_theme` AS `libelle_sous_theme`, t1.`id_theme_pere`, t2.`libelle_theme_court` AS `libelle_theme_pere`, 
		`nom_departement`, `nom_region`, `nom_territoire`, `id_competence_geo`, `p`.`zone_selection_villes` AS `zone_pro` 
		FROM `'.DB_PREFIX.'bsl_offre` AS `o`
		JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `p`.id_professionnel=`o`.id_professionnel
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `t1` ON `t1`.id_theme=`o`.id_sous_theme
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `t2` ON `t2`.id_theme=`t1`.id_theme_pere 
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` AS `dep` ON `p`.`competence_geo`="departemental" AND `dep`.`id_departement`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` AS `r` ON `p`.`competence_geo`="regional" AND `r`.`id_region`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `p`.`competence_geo`="territoire" AND `tr`.`id_territoire`=`p`.`id_competence_geo`
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
		`o`.`zone_selection_villes`, `p`.`id_professionnel`, 
		`nom_pro`, `competence_geo`, `id_competence_geo`, `nom_departement`, `nom_region`, `nom_territoire`, `theme_pere`.`id_territoire`
		FROM `'.DB_PREFIX.'bsl_offre` AS `o`
		JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `p`.id_professionnel=`o`.`id_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.id_theme=`o`.`id_sous_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`t`.`id_theme_pere`
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` AS `dep` ON `p`.`competence_geo`="departemental" AND `dep`.`id_departement`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` AS `r` ON `p`.`competence_geo`="regional" AND `r`.`id_region`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `p`.`competence_geo`="territoire" AND `tr`.`id_territoire`=`p`.`id_competence_geo`
		WHERE actif_offre= ? ';
	$params[] = (int) $flag;
	$types = 'i';

	if (isset($territoire_id) && $territoire_id > 0) {
		$query .= 'AND `competence_geo`="territoire" AND `id_competence_geo`= ? ';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if (isset($user_pro_id) && (int) $user_pro_id > 0){
		$query .= 'AND `p`.id_professionnel = ? ';
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
	
	$query = 'SELECT DISTINCT `m`.id_mesure, nom_mesure, DATE_FORMAT(`debut_mesure`, "%d/%m/%Y") AS date_debut,
		DATE_FORMAT(`fin_mesure`, "%d/%m/%Y") AS date_fin, `theme_pere`.libelle_theme_court, `m`.zone_selection_villes,
		nom_pro, `m`.`competence_geo`, `m`.`id_competence_geo`, nom_departement, nom_region, nom_territoire
		FROM `'.DB_PREFIX.'bsl_mesure` AS `m`
		JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `p`.id_professionnel=`m`.`id_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.id_theme=`m`.`id_sous_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`t`.`id_theme_pere`
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` AS `dep` ON `p`.`competence_geo`="departemental" AND `dep`.`id_departement`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` AS `r` ON `p`.`competence_geo`="regional" AND `r`.`id_region`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `p`.`competence_geo`="territoire" AND `tr`.`id_territoire`=`p`.`id_competence_geo` 
		';
		
	if(isset($tab_criteres) && count($tab_criteres)){
		$query .= 'JOIN `'.DB_PREFIX.'bsl_mesure_criteres` AS `mc` ON `mc`.id_mesure=`m`.`id_mesure` ';
		foreach ($tab_criteres as $name => $selected_option) {
			if($selected_option != ''){
				$query .= ' AND `mc`.nom_critere= ? AND `mc`.valeur_critere= ? ';
				$params[] = $name;
				$params[] = $selected_option;
				$types .= 'ss';
			}
		}
	}
	$query .= 'WHERE actif_mesure= ? 
	ORDER BY `m`.id_mesure DESC';
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
		`courriel_mesure`, `telephone_mesure`, `site_web_mesure`, `m`.`competence_geo`, `m`.`id_competence_geo`, /*`m`.`zone_selection_villes` AS `zone_mesure`,*/ `actif_mesure`, `p`.id_professionnel, `nom_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `p`.`competence_geo` AS `competence_geo_pro`, `id_theme_pere`, `nom_departement`, `nom_region`, `nom_territoire`, `p`.`id_competence_geo` AS `id_competence_geo_pro`, `p`.`zone_selection_villes` AS `zone_pro` 
		FROM `'.DB_PREFIX.'bsl_mesure` AS `m`
		JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `p`.id_professionnel=`m`.id_professionnel
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.id_theme=`m`.id_sous_theme
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` AS `dep` ON `m`.`competence_geo`="departemental" AND `dep`.`id_departement`=`m`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` AS `r` ON `m`.`competence_geo`="regional" AND `r`.`id_region`=`m`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `m`.`competence_geo`="territoire" AND `tr`.`id_territoire`=`m`.`id_competence_geo`
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

	$query = 'SELECT `p`.`id_professionnel`, `nom_pro`, `type_pro`, `param_type`.`libelle` AS `type`, `ville_pro`, `code_postal_pro`, GROUP_CONCAT(libelle_theme_court SEPARATOR ", ") AS `themes`, `competence_geo`, `zone_selection_villes`, `nom_departement`, `nom_region`, `nom_territoire`, `p`.`id_competence_geo`, `t`.`id_territoire`
		FROM `'.DB_PREFIX.'bsl_professionnel` AS `p`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes` AS `pt` ON `pt`.`id_professionnel`=`p`.`id_professionnel`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.`id_theme`=`pt`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` AS `dep` ON `p`.`competence_geo`="departemental" AND `dep`.`id_departement`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__region` AS `r` ON `p`.`competence_geo`="regional" AND `r`.`id_region`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `p`.`competence_geo`="territoire" AND `tr`.`id_territoire`=`p`.`id_competence_geo`
		LEFT JOIN `'.DB_PREFIX.'bsl__parametres` AS `param_type` ON `param_type`.`id`=`p`.`type_id` AND `param_type`.`liste`="type_pro"
		WHERE `actif_pro`= ? ';
	$params[] = (int) $flag;
	$types = 'i';

	if (isset($territoire_id) && $territoire_id > 0) {
		$query .= 'AND `competence_geo`="territoire" AND `id_competence_geo`= ? ';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	if (isset($user_pro_id) && (int) $user_pro_id > 0){
		$query .= 'AND `p`.id_professionnel = ? ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= ' GROUP BY `p`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,competence_geo
	ORDER BY `p`.`id_professionnel` DESC';

	$stmt = query_prepare($query,$params,$types);
	$pros = query_get($stmt);
	
	return $pros;
}

function get_pro_by_id($id){

	global $conn;
	$pro = null;

	$query = 'SELECT `id_professionnel`, `nom_pro`, `type_pro`, `type_id`, `param_type`.`libelle` AS `type`, `statut_id`, `description_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`, `courriel_pro`, `telephone_pro`, `courriel_referent_boussole`, `telephone_referent_boussole`, `site_web_pro`, `visibilite_coordonnees`, `delai_pro`, `competence_geo`, `id_competence_geo`, `zone_selection_villes`, `editeur`, `actif_pro`, `last_edit_user_id` 
		FROM `'.DB_PREFIX.'bsl_professionnel` AS `p`
		LEFT JOIN `'.DB_PREFIX.'bsl__parametres` AS `param_type` ON `param_type`.`id`=`p`.`type_id` AND `param_type`.`liste`="type_pro"
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
	$info = null;
	
	/* on teste d'abord s'il n'existe pas */
	$test_pro = true;
	$query = 'SELECT `id_professionnel` FROM `'.DB_PREFIX.'bsl_professionnel` AS `p`
		WHERE `nom_pro`= ? AND `competence_geo` = ? AND `id_competence_geo` = ? ';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ssi', $nom, $competence_geo, $competence_geo_id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($result) > 0) {
			$info = mysqli_fetch_assoc($result);
			$test_pro = false;
			$info = 'Un organisme portant ce nom existe déjà sur cette zone (peut-être parmi les archivés)';
		}
		mysqli_stmt_close($stmt);
	}

	if($test_pro){
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
			
			$info = mysqli_insert_id($conn);
			$created2 = update_listes_pro($info, $themes, $zone, $liste_villes);
		}
	}
	
	return [$created, $info];
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
	$query_ot = 'SELECT `id_offre`, `nom_offre` 
		FROM `'.DB_PREFIX.'bsl_offre` 
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
	$query_ov = 'SELECT `o`.`id_offre`, `nom_offre` 
		FROM `'.DB_PREFIX.'bsl_offre` AS `o`
		JOIN `'.DB_PREFIX.'bsl_offre_criteres` AS `oc`
		ON `o`.`id_offre`=`oc`.`id_offre` AND `nom_critere` LIKE "villes"
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

	$query = 'SELECT `id_utilisateur`, `u`.`id_statut`, `nom_utilisateur`,
		`email`, `libelle_statut`, `nom_pro`, `nom_territoire`
		FROM `'.DB_PREFIX.'bsl_utilisateur` AS `u`
		JOIN `'.DB_PREFIX.'bsl__droits` AS `dr` ON `dr`.`id_statut`=`u`.`id_statut`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `tr`.`id_territoire`=`u`.`id_metier`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `p`.`id_professionnel`=`u`.`id_metier`
		WHERE `actif_utilisateur`= ? ';
	$params[] = (int) $flag;
	$types = 'i';

	if (isset($territoire_id) && $territoire_id > 0) {
		$query .= 'AND (`u`.`id_statut`=2 AND `id_metier`= ?)
			OR (`u`.`id_statut`=3 AND `p`.`competence_geo`="territoire"
				AND `id_competence_geo`= ?) ';
		$params[] = (int) $territoire_id;
		$params[] = (int) $territoire_id;
		$types .= 'ii';
	}
	if (isset($user_pro_id) && (int) $user_pro_id > 0){
		$query .= 'AND `p`.id_professionnel = ? AND `u`.`id_statut`=3 ';
		$params[] = (int) $user_pro_id;
		$types .= 'i';
	}
	$query .= ' ORDER BY `u`.`id_statut` ASC,`id_metier` ASC';
	$stmt = query_prepare($query,$params,$types);
	$users = query_get($stmt);
	
	return $users;
}

/* Formulaire */

function get_liste_formulaires($flag_actif = 1, $territoire_id = null) {

	global $conn;
	$formulaires= null;
	$types='';

	$query = 'SELECT `f`.`id_formulaire` AS `id`, `t`.`libelle_theme_court` AS `theme`, `tr`.`nom_territoire`,  `old_tr`.`nom_territoire` as `ancien_territoire`, COUNT(DISTINCT `fp`.`id_page`) AS `nb_pages`,  COUNT(DISTINCT `q`.`id_question`) AS `nb_questions`
		FROM `'.DB_PREFIX.'bsl_formulaire` AS `f`
		JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.`id_theme`=`f`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `tr`.`id_territoire`=`t`.`id_territoire` /*AND `tr`.`actif_territoire`=1*/ 
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `old_tr` ON `old_tr`.`id_territoire`=`f`.`id_territoire` /* `tr`.`id_territoire`=`f`.`id_territoire` */
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__page` AS `fp` ON `fp`.`id_formulaire`=`f`.`id_formulaire` AND `fp`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__question` AS `q` ON `q`.`id_page`=`fp`.`id_page` AND `q`.`actif`=1
		WHERE `f`.`actif`=? ';
	$params[] = (int) $flag_actif;
	$types .= 'i';
	if (isset($territoire_id) && $territoire_id > 0) {
		$query .= 'AND `tr`.`id_territoire`=? ';
		$params[] = (int) $territoire_id;
		$types .= 'i';
	}
	$query .= 'GROUP BY `f`.`id_formulaire`, `t`.`libelle_theme_court`, `tr`.`nom_territoire`
		ORDER BY `f`.`id_formulaire`, `fp`.`id_page`';

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

	$query = 'SELECT `f`.`id_formulaire`, `f`.`id_theme`, `t`.`libelle_theme_court`, `t`.`libelle_theme`, `tr`.`nom_territoire`, `f`.`nb_pages`, `f`.`actif`, `fp`.`id_page`, `fp`.`titre`, `fp`.`ordre` AS `ordre_page`, `fp`.`aide`, `q`.`id_question`, `q`.`ordre` AS `ordre_question`, `q`.`libelle` AS `libelle_question`, `q`.`html_name`, `q`.`type`, `q`.`taille`, `q`.`obligatoire`, `fr`.`id_reponse`, `fr`.`libelle` AS `reponse`
		FROM `'.DB_PREFIX.'bsl_formulaire` AS `f`
		JOIN `'.DB_PREFIX.'bsl_theme` AS `t` ON `t`.`id_theme`=`f`.`id_theme` 
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `tr`.`id_territoire`=`t`.`id_territoire` /* `tr`.`id_territoire`=`f`.`id_territoire` */
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__page` AS `fp` ON `fp`.`id_formulaire`=`f`.`id_formulaire` 
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__question` AS `q` ON `q`.`id_page`=`fp`.`id_page`
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` AS `fr` ON `fr`.`id_reponse`=`q`.`id_reponse`
		WHERE `f`.`id_formulaire` = ?
		ORDER BY `ordre_page`, `q`.`ordre`';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	check_mysql_error($conn);

	mysqli_stmt_bind_result($stmt, $id_formulaire, $id_theme, $libelle_theme_court, $libelle_theme, $nom_territoire,$nb_pages, $actif, $id_page, $titre, $ordre_page, $aide, $id_question, $ordre_question, $libelle_question, $html_name, $type, $taille, $obligatoire, $id_reponse, $reponse);
	while (mysqli_stmt_fetch($stmt)) {
		if ($id_formulaire != $tmp_id) { //données du formulaire
			$meta = array('id' => $id_formulaire, 'id_theme' => $id_theme, 'theme' => $libelle_theme_court, 'libelle' => $libelle_theme, 'territoire' => ($nom_territoire) ? $nom_territoire : 'national', 'actif' => $actif, 'nb' => $nb_pages);
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

	$query = 'SELECT `fr`.`libelle` AS `libelle_reponse`, `id_valeur`, `v`.`libelle` AS `libelle_valeur`, `valeur`, `ordre`, `defaut`, `actif`
		FROM `'.DB_PREFIX.'bsl_formulaire__reponse` AS `fr`
		JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` AS `v` ON `v`.`id_reponse`= `fr`.`id_reponse`
		WHERE `fr`.`id_reponse`= ?
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

function get_liste_themes($actif = null) {

	global $conn;
	$themes = null;
	$params = [];
	$types = '';

	$query = 'SELECT `t`.`id_theme`, `libelle_theme`, `libelle_theme_court`, `actif_theme`, `nom_territoire` 
		FROM `'.DB_PREFIX.'bsl_theme` AS `t` 
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `t`.`id_territoire`=`tr`.`id_territoire` 
		WHERE `id_theme_pere` IS NULL ';
	if(isset($actif)) {
		$query .= 'AND `actif_theme`= ? ';
		$params[] = (int) $actif;
		$types .= 'i';
	}
	$stmt = query_prepare($query,$params,$types);
	$themes = query_get($stmt);

	return $themes;
}

function get_theme_et_sous_themes_by_id($id) {

	global $conn;
	$themes = null;
	if (isset($id)) {
		$query = 'SELECT `id_theme`, `libelle_theme`, `id_theme_pere`, `ordre_theme`, `actif_theme`, `libelle_theme_court`, `t`.`id_territoire`, `nom_territoire` 
			FROM `'.DB_PREFIX.'bsl_theme` AS `t`
			LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `tr`.`id_territoire`=`t`.`id_territoire` 
			WHERE `id_theme`= ? OR `id_theme_pere`= ? 
			ORDER BY id_theme_pere, ordre_theme';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'ii', $id, $id);
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

function get_themes_by_pro($pro_id, $actif = null, $theme_pere_only = null) {

	global $conn;
	$themes = null;
	$params = [];
	$types = '';

	$query = 'SELECT `t`.`id_theme`, `libelle_theme`, `libelle_theme_court`, `actif_theme`, `t`.`id_territoire`, `id_professionnel`, `id_theme_pere`, `nom_territoire`
		FROM `'.DB_PREFIX.'bsl_theme` AS `t`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` 
			ON `tr`.`id_territoire`=`t`.`id_territoire` 
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes` AS `pt` 
			ON `pt`.`id_theme`=`t`.`id_theme` 
		WHERE `pt`.`id_professionnel`= ? ';
	$params[] = (int) $pro_id;
	$types .= 'i';
	if(isset($actif)) {
		$query .= 'AND `actif_theme`= ? ';
		$params[] = (int) $actif;
		$types .= 'i';
	}
	if(isset($theme_pere_only) && $theme_pere_only=="pere") {
		$query .= 'AND `id_theme_pere` IS NULL ';
	}
	$stmt = query_prepare($query,$params,$types);
	$themes = query_get($stmt);
	return $themes;
}

function get_themes_by_territoire($territoire_id = null, $theme_pere_only = null) {

	global $conn;
	$themes = null;
	$params = [];
	$types = '';

	$query = 'SELECT `t`.`id_theme`, `t`.`libelle_theme`, `t`.`libelle_theme_court`, `t`.`actif_theme`, `t`.`id_territoire`, `t`.`id_theme_pere`, `tr`.`nom_territoire` 
		FROM `'.DB_PREFIX.'bsl_theme` AS `t`
		LEFT JOIN `'.DB_PREFIX.'bsl_theme` AS `pere` ON `pere`.`id_theme`=`t`.`id_theme_pere` 
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `tr`.`id_territoire`=`t`.`id_territoire` 
		WHERE 1 ';
	if(isset($territoire_id) && $territoire_id) {
		$query .= ' AND `t`.`id_territoire`= ? OR `pere`.`id_territoire`= ? ';
		$params[] = (int) $territoire_id;
		$params[] = (int) $territoire_id;
		$types .= 'ii';
	}
	if(isset($theme_pere_only) && $theme_pere_only=="pere") {
		$query .= ' AND `t`.`id_theme_pere` IS NULL ';
	}
	/*if(isset($actif)) {
		$query .= 'AND `actif_theme`= ? ';
		$params[] = (int) $actif;
		$types .= 'i';
	}*/
	$stmt = query_prepare($query,$params,$types);
	$themes = query_get($stmt);

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
	$query = 'SELECT `v`.`code_insee`, `v`.`code_postal`, `v`.`nom_ville` 
		FROM `'.DB_PREFIX.'bsl__ville` AS `v`
		JOIN `'.DB_PREFIX.'bsl_territoire_villes` AS `tv` ON `tv`.`code_insee`=`v`.`code_insee` 
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
function create_theme($theme, $territoire, $libelle, $actif){

	global $conn;
	$created = false;
	$msg = null;
	$user_id=secu_get_current_user_id();

	//on vérifie d'abord si le thème n'a pas déjà été décliné sur le territoire en question 
	if (isset($theme) && isset($territoire)) {
		
		$query = 'SELECT COUNT(*) as `nb` 
			FROM `'.DB_PREFIX.'bsl_theme` 
			WHERE `libelle_theme_court` LIKE ? AND `id_territoire` = ? AND `actif_theme` = 1';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'si', $theme, $territoire);
		check_mysql_error($conn);
		if (mysqli_stmt_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) === 1) {
				$row = mysqli_fetch_assoc($result);
				$nb = (int)$row['nb'];
			}
			mysqli_stmt_close($stmt);
		}
		
		if($nb>0){
			$msg = 'Ce thème a déjà été décliné sur ce territoire.';
			
		}else{
			$query = 'INSERT INTO `'.DB_PREFIX.'bsl_theme`(`libelle_theme`, `id_theme_pere`, `actif_theme`, `ordre_theme`, `libelle_theme_court`, `id_territoire`) VALUES (?, NULL, ?, NULL, ?, ?)';
			$stmt = mysqli_prepare($conn, $query);
			mysqli_stmt_bind_param($stmt, 'sisi', $libelle, $actif, $theme, $territoire);
			check_mysql_error($conn);
			if (mysqli_stmt_execute($stmt)) {
				$created = mysqli_stmt_affected_rows($stmt) > 0;
				mysqli_stmt_close($stmt);
			}
		}
	}
	
	return [$created,$msg];
}

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

function update_sous_themes($sous_themes, $id_theme_pere, $theme){

	global $conn;
	$updated = 0;
	
	if(isset($sous_themes)){
		foreach ($sous_themes as $row) {
			if($row[0]){
				$query = 'UPDATE `'.DB_PREFIX.'bsl_theme` 
					SET `libelle_theme`= ? , `ordre_theme`= ? , `actif_theme`= ? 
					WHERE `id_theme`= ?';
				$stmt = mysqli_prepare($conn, $query);
				mysqli_stmt_bind_param($stmt, 'siii', $row[1], $row[2], $row[3], $row[0]);
			}else{
				$query = 'INSERT INTO `'.DB_PREFIX.'bsl_theme` 
					(`libelle_theme`, `id_theme_pere`, `ordre_theme`, `actif_theme`, `libelle_theme_court`) 
					VALUES (?,?,?,?,?)';
				$stmt = mysqli_prepare($conn, $query);
				mysqli_stmt_bind_param($stmt, 'siiii', $row[1], $id_theme_pere, $row[2], $row[3], $theme);
				
				if(DEBUG){
					$print_sql = $query;
					foreach(array( $row[1], $id_theme_pere, $row[2], $row[3], $row[0], $theme) as $term){
						$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
					}
					echo "<!--<pre>".$print_sql."</pre>-->";
				}

			}
			check_mysql_error($conn);
			if (mysqli_stmt_execute($stmt)) {
				$updated += mysqli_stmt_affected_rows($stmt) > 0;
				mysqli_stmt_close($stmt);
			}
		}
	}
	return $updated;
}
/*
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
}*/

/* Utilisateurs */
function create_user($nom_utilisateur, $courriel, $statut, $attache) {

	global $conn;
	$created = false;
	$user_id = secu_get_current_user_id();
	$mdp = null; //secu_user_checksum($user_id, 'temporaire@boussole.fr', date("Y-m-d H:i:s")); //mot de passe temporaire pour éviter les connections fortuites ?

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
	mysqli_stmt_bind_param($stmt, 'ssii', $nom, $courriel, $user_id, $id);
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$updated = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}

	return $updated;
}

function update_motdepasse($id, $motdepasseactuel, $nouveaumotdepasse){

	global $conn;
	$updated = false;
	
	$query = 'SELECT `motdepasse` FROM `'.DB_PREFIX.'bsl_utilisateur` WHERE `id_utilisateur`= ?';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'i', $id);
	check_mysql_error($conn);
	
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		$row = mysqli_fetch_assoc($result);
		
		if (password_verify(SALT_BOUSSOLE . $motdepasseactuel, $row['motdepasse'])) {
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
				$msg = 'Mot de passe modifié.';
				mysqli_stmt_close($stmt2);
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

	$query = 'SELECT `u`.`id_statut`, `nom_utilisateur`, `email`, `date_inscription`, `actif_utilisateur`, `id_professionnel`, `nom_pro`, `id_territoire` , `nom_territoire`
		FROM `'.DB_PREFIX.'bsl_utilisateur` AS `u`
		JOIN `'.DB_PREFIX.'bsl__droits` AS `dr` ON `dr`.`id_statut`=`u`.`id_statut`
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` AS `tr` ON `tr`.`id_territoire`=`u`.`id_metier`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` AS `p` ON `p`.`id_professionnel`=`u`.`id_metier`
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

	$query = 'SELECT `f`.`id_formulaire`, `f`.`nb_pages`, `fp`.`titre`, `fp`.`ordre` AS `ordre_page`, `fp`.`aide`, 
		`q`.`id_question`, `q`.`libelle` AS `libelle_question`, 
		`q`.`html_name`, `q`.`type`, `q`.`taille`, 
		`q`.`obligatoire`, `v`.`libelle` AS `libelle_reponse`, `v`.`valeur`, `v`.`defaut` 
		FROM `'.DB_PREFIX.'bsl_formulaire` AS `f`
		JOIN `'.DB_PREFIX.'bsl_formulaire__page` AS `fp` ON `fp`.`id_formulaire`=`f`.`id_formulaire` AND `fp`.`actif`=1
		JOIN `'.DB_PREFIX.'bsl_formulaire__question` AS `q` ON `q`.`id_page`=`fp`.`id_page` AND `q`.`actif`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__reponse` AS `fr` ON `fr`.`id_reponse`=`q`.`id_reponse`
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` AS `v` ON `v`.`id_reponse`=`fr`.`id_reponse` AND `v`.`actif`=1
		WHERE `f`.`actif`=1 AND `f`.`type`="mesure"
		ORDER BY `ordre_page`, `q`.`ordre`, `v`.`ordre`';
	$stmt = mysqli_prepare($conn, $query);
	//mysqli_stmt_bind_param($stmt, 'si', $_SESSION['admin']['besoin'], $etape);
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
	$query = 'SELECT `id_statut`, `libelle_statut`, `demande_r`, `demande_w`, `offre_r`, `offre_w`, `mesure_r`, `mesure_w`, `professionnel_r`, `professionnel_w`, `utilisateur_r`, `utilisateur_w`, `formulaire_r`, `formulaire_w`, `theme_r`, `theme_w`, `territoire_r`, `territoire_w` 
		FROM `'.DB_PREFIX.'bsl__droits` 
		WHERE 1 ORDER BY `demande_r` DESC, `id_statut`';
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

function create_formulaire($theme, $territoire=null) {

	global $conn;
	$created = false;
	$msg = null;
	$user_id=secu_get_current_user_id();
	
	//on crée le formulaire sur le thème donné, s'il n'existe pas déjà 
	if (isset($theme)) {
		
		$query = 'SELECT COUNT(*) as `nb` 
			FROM `'.DB_PREFIX.'bsl_formulaire` 
			WHERE id_theme = ? AND actif = 1';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'i', $theme);
		check_mysql_error($conn);
		if (mysqli_stmt_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) === 1) {
				$row = mysqli_fetch_assoc($result);
				$nb = (int)$row['nb'];
			}
			mysqli_stmt_close($stmt);
		}
		
		if($nb>0){
			$msg = 'Le formulaire existe déjà pour ce thème sur ce territoire.';
			
		}else{
			$query = 'INSERT INTO `'.DB_PREFIX.'bsl_formulaire`(`type`, `id_theme`, `actif`)
				VALUES ("offre", ?, 1) ';
			$stmt = mysqli_prepare($conn, $query);
			mysqli_stmt_bind_param($stmt, 'i', $theme);
		
			if (mysqli_stmt_execute($stmt)) {
				$created += mysqli_stmt_affected_rows($stmt) > 0;
				mysqli_stmt_close($stmt);
			}
		}
	}
	
	return [$created,$msg];
}

function update_formulaire($formulaire_id, $theme, $id_p, $ordre_p, $titre_p, $id_q, $page_q, $ordre_q, $titre_q, $reponse_q, $type_q, $name_q, $requis) { 

	global $conn;
	$updated = false;
	$user_id=secu_get_current_user_id();
	$nbpages_a_mettre_a_jour = false;
	
	if (isset($formulaire_id) && isset($theme) && $theme) {
		$query = 'UPDATE `'.DB_PREFIX.'bsl_formulaire` 
			SET `id_theme`= ?, `id_territoire` = NULL
			WHERE `id_formulaire`= ? ';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'ii', $theme, $formulaire_id);
		
		if (mysqli_stmt_execute($stmt)) {
			$updated += mysqli_stmt_affected_rows($stmt) > 0;
			mysqli_stmt_close($stmt);
		}
	}
	
	if (isset($id_p) && isset($id_q)) {
		
		//update pages
		foreach($id_p as $key=>$id){
			if ($id){
				 //si on a un titre de page, on met à jour la page
				 if($titre_p[$key]){
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
			}else{
				//si pas d'id mais un titre, on crée
				if ($titre_p[$key]){
					$query = 'INSERT INTO `'.DB_PREFIX.'bsl_formulaire__page`(`id_formulaire`, `titre`, `ordre`)
						VALUES (?, ?, ?)';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'isi', $formulaire_id, $titre_p[$key], $ordre_p[$key]);
					
					if (mysqli_stmt_execute($stmt)) {
						$id_p[$key] = mysqli_insert_id($conn); //sert pour l'insert des questions
						$updated += mysqli_stmt_affected_rows($stmt) > 0;
						$nbpages_a_mettre_a_jour = true;
						mysqli_stmt_close($stmt);
					}
				}
			}
		}
		
		//si on a créé ou supprimé une page, il faut mettre à jour le compteur
		if($nbpages_a_mettre_a_jour){
			update_nbpages_formulaire($formulaire_id);
		}
		
		//update questions
		foreach($id_q as $key_p=>$id_qp){
			foreach($id_qp as $key=>$id){
				if($id){
					//si on a un titre, une réponse et un type, on met à jour
					if ($titre_q[$key_p][$key] && $reponse_q[$key_p][$key] && $type_q[$key_p][$key]){
						
						$query = 'UPDATE `'.DB_PREFIX.'bsl_formulaire__question` 
							SET `id_page`= ?, `ordre`= ?, `libelle` = ?, `id_reponse`= ?, `type` = ?, `obligatoire` = ? 
							WHERE `id_question`= ? ';
						$stmt = mysqli_prepare($conn, $query);
						$required = (isset($requis[$key_p][$key])) ? 1:0;
						mysqli_stmt_bind_param($stmt, 'iisisii', $id_p[$key_p], $ordre_q[$key_p][$key], $titre_q[$key_p][$key], $reponse_q[$key_p][$key], $type_q[$key_p][$key], $required, $id);
					
						if (mysqli_stmt_execute($stmt)) {
							$updated += mysqli_stmt_affected_rows($stmt) > 0;
							mysqli_stmt_close($stmt);
						}
					}
				
				}else{
					//si on n'a pas d'id mais toutes les infos, c'est une nouvelle question à créer
					if($titre_q[$key_p][$key] && $name_q[$key_p][$key] && $reponse_q[$key_p][$key] && $type_q[$key_p][$key]){
						$query = 'INSERT INTO `'.DB_PREFIX.'bsl_formulaire__question`
							(`id_page`, `libelle`, `html_name`, `ordre`, `type`, `obligatoire`, `id_reponse`)
							VALUES (?, ?, ?, ?, ?, ?, ?) ';
						$stmt = mysqli_prepare($conn, $query);
						$required = (isset($requis[$key_p][$key])) ? 1:0;
						mysqli_stmt_bind_param($stmt, 'issisii', $id_p[$key_p], $titre_q[$key_p][$key], $name_q[$key_p][$key], $ordre_q[$key_p][$key], $type_q[$key_p][$key], $required, $reponse_q[$key_p][$key]);
					
						if (mysqli_stmt_execute($stmt)) {
							$updated += mysqli_stmt_affected_rows($stmt) > 0;
							mysqli_stmt_close($stmt);
						}
					}
				}
			}
		}
	}
	
	return $updated;
}

function delete_page_formulaire($id_p, $id_f){
	
	global $conn;
	$updated = false;
	//si pas de titre + aucune question, on supprime la page
	if( empty(array_filter($titre_q[$ordre_p[$key]])) ){ 
		$query = 'DELETE FROM `'.DB_PREFIX.'bsl_formulaire__page` 
			WHERE `id_page`= ? AND `id_formulaire`= ? ';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 'ii', $id_p, $id_f);
		
		if (mysqli_stmt_execute($stmt)) {
			$updated += mysqli_stmt_affected_rows($stmt) > 0;
			$nbpages_a_mettre_a_jour = true; 
			mysqli_stmt_close($stmt);
		}
	}
	return $updated;
}

function delete_question_formulaire($id_q, $id_f){

	global $conn;
	$updated = false;
	$query = 'DELETE `fq` FROM `'.DB_PREFIX.'bsl_formulaire__question` AS `fq`
		LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__page` AS `fp` ON `fq`.`id_page`=`fp`.`id_page`
		WHERE `fq`.`id_question`= ? AND `fp`.`id_formulaire`= ? ';
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ii', $id_q, $id_f);
	
	if (mysqli_stmt_execute($stmt)) {
		$updated += mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}
	return $updated;
}

function update_nbpages_formulaire($id){
	
	global $conn;
	$updated = false;
	$query = 'UPDATE `'.DB_PREFIX.'bsl_formulaire` AS `f` 
		SET `nb_pages` =
			(SELECT COUNT(DISTINCT `p`.`id_page`) as `nb_pages`
			FROM (SELECT * FROM `'.DB_PREFIX.'bsl_formulaire`) AS `f2`
			LEFT JOIN `'.DB_PREFIX.'bsl_formulaire__page` AS `p` ON `p`.`id_formulaire`=`f2`.`id_formulaire` AND `p`.`actif`=1
			WHERE `f2`.`actif`=1 AND `f2`.`id_formulaire` = ?)
		WHERE `f`.`id_formulaire` = ?';
		
	$stmt = mysqli_prepare($conn, $query);
	mysqli_stmt_bind_param($stmt, 'ii', $id, $id);
	
	if (mysqli_stmt_execute($stmt)) {
		$updated += mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}
	return $updated;
}

function create_reponse($libelle, $id_v, $libelle_v, $valeur_v, $ordre_v, $actif) {

	global $conn;
	$created = false;
	$reponse_id = null;
	$defaut = 0;
	$user_id=secu_get_current_user_id();
	
	if (isset($libelle)) {
		$query = 'INSERT INTO `'.DB_PREFIX.'bsl_formulaire__reponse`(`libelle`)
			VALUES (?)';
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, 's', $libelle);
		if (mysqli_stmt_execute($stmt)) {
			$reponse_id = mysqli_insert_id($conn);
			$created += mysqli_stmt_affected_rows($stmt) > 0;
			mysqli_stmt_close($stmt);
		}
	}
	
	//insert valeurs
	if (isset($libelle_v)) {
		foreach($libelle_v as $key=>$libelle_valeur){
			if($libelle_valeur){
				$thisactif = (isset($actif[$key])) ? '1':'0';
				
				$query = 'INSERT INTO `'.DB_PREFIX.'bsl_formulaire__valeur`(`id_reponse`, `libelle`, `valeur`, `ordre`, `defaut`, `actif`)
					VALUES (?, ?, ?, ?, ?, ?)';
				$stmt = mysqli_prepare($conn, $query);
				mysqli_stmt_bind_param($stmt, 'issiii', $reponse_id, $libelle_valeur, $valeur_v[$key], $ordre_v[$key], $defaut, $thisactif);

				if (mysqli_stmt_execute($stmt)) {
					$created += mysqli_stmt_affected_rows($stmt) > 0;
					mysqli_stmt_close($stmt);
				}
			}
		}
	}
	
	return $reponse_id;
}

function update_reponse($reponse_id, $libelle, $id_v, $libelle_v, $valeur_v, $ordre_v, $actif, $defaut_id) {

	global $conn;
	$updated = false;
	$user_id=secu_get_current_user_id();
	
	//update valeurs
	if (isset($libelle_v)) {
		foreach($libelle_v as $key=>$libelle_valeur){
			if($libelle_valeur){
				$defaut = (isset($defaut_id) && ($id_v[$key] == $defaut_id)) ? '1':'0';
				$thisactif = (isset($actif[$key])) ? '1':'0';				
				
				if(isset($id_v[$key]) && $id_v[$key]){
					$query = 'UPDATE `'.DB_PREFIX.'bsl_formulaire__valeur` 
						SET `libelle`= ? ,`valeur`= ? ,`ordre`= ? ,`defaut`= ? ,`actif`= ? 
						WHERE `id_valeur`= ? ';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'ssiiii', $libelle_valeur, $valeur_v[$key], $ordre_v[$key], $defaut, $thisactif, $id_v[$key]);
					
					if (mysqli_stmt_execute($stmt)) {
						$updated += mysqli_stmt_affected_rows($stmt) > 0;
						mysqli_stmt_close($stmt);
					}
				}else{
					$query = 'INSERT INTO `'.DB_PREFIX.'bsl_formulaire__valeur`(`id_reponse`, `libelle`, `valeur`, `ordre`, `defaut`, `actif`)
						VALUES (?, ?, ?, ?, ?, ?)';
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, 'issiii', $reponse_id, $libelle_valeur, $valeur_v[$key], $ordre_v[$key], $defaut, $thisactif);
										
					if (mysqli_stmt_execute($stmt)) {
						$updated += mysqli_stmt_affected_rows($stmt) > 0;
						mysqli_stmt_close($stmt);
					}
				}
			}
		}
	}
	
	return $updated;
}