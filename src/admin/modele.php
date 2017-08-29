<?php

function get_nb_nouvelles_demandes()
{
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

function get_criteres($id_offre, $id_theme)
{
    global $conn;
    $query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`, `'.DB_PREFIX.'bsl_formulaire__question`.`taille`, `'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`libelle`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur`, `'.DB_PREFIX.'bsl_offre_criteres`.`id_offre` FROM `'.DB_PREFIX.'bsl_formulaire` 
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
function get_demande_by_id($id)
{
    global $conn;

    $demande = null;
    $query = 'SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, `commentaire`, `'.DB_PREFIX.'bsl_offre`.nom_offre, `'.DB_PREFIX.'bsl_professionnel`.nom_pro   
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

function get_liste_demandes($flag_traite, $territoire_id = null, $user_pro_id = null)
{
    global $conn;

    $demandes = [];

    $params = [];
    $types = '';
    $query = 'SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, `'.DB_PREFIX.'bsl_offre`.nom_offre, `'.DB_PREFIX.'bsl_offre`.id_professionnel, `'.DB_PREFIX.'bsl_professionnel`.nom_pro   
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
    if(count($params) > 0)
    {
        $query_params = [];
        $query_params[] = &$types;
        foreach ($params as $param) {
            $query_params[] = &$param;
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

function update_demande($id, $commentaire, $user_id)
{
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
    }

    return $updated;
}

/* Formulaire */
function get_liste_formulaires($flag_actif = 1, $territoire_id = null)
{
    global $conn;

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
    $formulaires = [];
    while (mysqli_stmt_fetch($stmt)) {
		$formulaires[] = array('id' => $id_formulaire, 'libelle' => $libelle, 'territoire' => ($territoire) ? $territoire : 'national');
    }
    mysqli_stmt_close($stmt);
    return $formulaires;
}

function get_formulaire_by_id($id)
{
    global $conn;

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
    $tmp_id = 0;
	$tmp_p = 0;
	$meta = [];
    $pages = [];
    $questions = [];
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

function get_question_by_id($id)
{
    global $conn;

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
    $tmp_q = 0;
    $question = [];
    $reponses = [];
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