<?php

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
    mysqli_stmt_execute($stmt);
    check_mysql_error($conn);
    mysqli_stmt_bind_param($stmt, 'ii', $id_offre, $id_theme);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_formulaire, $libelle_question, $html_name, $type, $taille, $obligatoire, $libelle, $valeur, $id_o);
    $tmp_que = '';
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

/**
 * Fonction de gestion des erreurs mysqli
 * @param mysqli $conn
 * @throws Exception
 */
function check_mysql_error(mysqli $conn)
{
    if (mysqli_error($conn))
        throw new Exception('MySQL error : ' . mysqli_error($conn));
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
 * if (mysqli_error($conn)) {
 * echo mysqli_error($conn);
 * exit;
 * }
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