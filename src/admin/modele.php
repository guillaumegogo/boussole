<?php

function get_nb_nouvelles_demandes()
{
    global $conn;

    $nb = 0;
    $query = 'SELECT count(`id_demande`) AS nb 
            FROM `bsl_demande` 
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
    $query = 'SELECT `bsl_formulaire`.`id_formulaire`, `bsl_formulaire__question`.`libelle` AS `libelle_question`, `bsl_formulaire__question`.`html_name`, `bsl_formulaire__question`.`type`, `bsl_formulaire__question`.`taille`, `bsl_formulaire__question`.`obligatoire`, `bsl_formulaire__valeur`.`libelle`, `bsl_formulaire__valeur`.`valeur`, `bsl_offre_criteres`.`id_offre` FROM `bsl_formulaire` 
	JOIN `bsl_theme` ON `bsl_theme`.`id_theme`=`bsl_formulaire`.`id_theme`
	JOIN `bsl_formulaire__page` ON `bsl_formulaire__page`.`id_formulaire`=`bsl_formulaire`.`id_formulaire` AND `bsl_formulaire__page`.`actif`=1
	JOIN `bsl_formulaire__question` ON `bsl_formulaire__question`.`id_page`=`bsl_formulaire__page`.`id_page` AND `bsl_formulaire__question`.`actif`=1
	JOIN `bsl_formulaire__valeur` ON `bsl_formulaire__valeur`.`id_question`=`bsl_formulaire__question`.`id_question` AND `bsl_formulaire__valeur`.`actif`=1
	LEFT JOIN `bsl_offre_criteres` ON `bsl_offre_criteres`.`nom_critere`=`bsl_formulaire__question`.`html_name` AND `bsl_offre_criteres`.`valeur_critere`=`bsl_formulaire__valeur`.`valeur` AND `bsl_offre_criteres`.`id_offre`= ?
	WHERE `bsl_formulaire`.`actif`=1 AND `bsl_theme`.`id_theme`= ? 
	ORDER BY `bsl_formulaire__page`.`ordre`, `bsl_formulaire__question`.`ordre`, `bsl_formulaire__valeur`.`ordre`';
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
    $query = "SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, `commentaire`, bsl_offre.nom_offre, bsl_professionnel.nom_pro   
            FROM `bsl_demande`
            JOIN bsl_offre ON bsl_offre.id_offre=bsl_demande.id_offre 
            JOIN bsl_professionnel ON bsl_offre.id_professionnel=bsl_professionnel.id_professionnel  
            WHERE id_demande = ?";
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
    $query = "SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, bsl_offre.nom_offre, bsl_offre.id_professionnel, bsl_professionnel.nom_pro   
            FROM `bsl_demande` 
            JOIN bsl_offre ON bsl_offre.id_offre = bsl_demande.id_offre 
            JOIN bsl_professionnel ON bsl_offre.id_professionnel = bsl_professionnel.id_professionnel
	        WHERE 1 ";

    if ($flag_traite) {
        $query .= "AND date_traitement IS NOT NULL ";
    } else {
        $query .= "AND date_traitement IS NULL ";
    }

    if ((int) $territoire_id > 0) {
        $query .= "AND bsl_professionnel.`competence_geo`=\"territoire\" AND bsl_professionnel.`id_competence_geo`= ?";
        $params[] = (int) $territoire_id;
        $types .= 'i';
    }

    if ((int) $user_pro_id > 0) {
        $query .= "AND `bsl_offre`.id_professionnel = ? ";
        $params[] = (int) $user_pro_id;
        $types .= 'i';
    }
    $query .= " ORDER BY date_demande DESC";

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
    $query = "UPDATE `bsl_demande` 
              SET `date_traitement` = NOW(), 
              `commentaire` = ?, 
              `user_derniere_modif`= ? 
	          WHERE `bsl_demande`.`id_demande` = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sii', $commentaire, $user_id, $id);
    check_mysql_error($conn);
    if (mysqli_stmt_execute($stmt)) {
        $updated = mysqli_stmt_affected_rows($stmt) > 0;
    }

    return $updated;
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