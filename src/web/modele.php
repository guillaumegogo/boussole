<?php

//********* affichage des thèmes disponibles en fonction de la ville choisie 
//todo : la requête fait la vérification des thèmes des pros autorisés à travailler sur une zone géographique englobant la zone indiquée : pays, région, département ou territoire. il faudra probablement descendre au niveau des offres pour une meilleure granularité. 
/* on pourrait descendre à la granularité de l'offre, mais la requête serait encore plus complexe :
(...) JOIN `bsl_offre` ON `bsl_offre`.id_professionnel=`bsl_professionnel`.id_professionnel
JOIN `bsl_theme` as theme_offre ON bsl_offre.id_sous_theme=theme_offre.id_theme
WHERE actif_offre=1 AND debut_offre <= CURDATE() AND fin_offre >= CURDATE() (...)*/
function get_themes()
{
    global $conn;

    $query = "SELECT DISTINCT `bsl_theme`.id_theme, `bsl_theme`.`libelle_theme`, `bsl_theme`.`actif_theme`
		FROM `bsl_theme`
		JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.id_theme=`bsl_theme`.id_theme
		JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_professionnel_themes`.id_professionnel
		LEFT JOIN `bsl_territoire` ON `bsl_professionnel`.competence_geo=\"territoire\" AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo` 
		LEFT JOIN `bsl_territoire_villes` ON `bsl_territoire_villes`.`id_territoire`=`bsl_territoire`.`id_territoire`
		LEFT JOIN `bsl__departement` ON `bsl_professionnel`.competence_geo=\"departemental\" AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `bsl__region` ON `bsl_professionnel`.competence_geo=\"regional\" AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
		LEFT JOIN `bsl__departement` AS `bsl__departement_region` ON `bsl__departement_region`.`id_region`=`bsl__region`.`id_region`
		WHERE `bsl_theme`.actif_theme=1 AND `bsl_professionnel`.actif_pro=1 AND (`bsl_professionnel`.competence_geo=\"national\" OR code_insee=? OR `bsl__departement`.id_departement=SUBSTR(?,1,2) OR `bsl__departement_region`.id_departement=SUBSTR(?,1,2))
		UNION
		SELECT DISTINCT `bsl_theme`.id_theme, `bsl_theme`.`libelle_theme`, `bsl_theme`.`actif_theme`
		FROM `bsl_theme`
		WHERE `id_theme_pere` IS NULL AND `actif_theme`=0";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['code_insee']);

    mysqli_stmt_execute($stmt);
    if (mysqli_error($conn)) {
        echo mysqli_error($conn);
        exit;
    }
    mysqli_stmt_bind_result($stmt, $id_theme, $libelle_theme, $actif_theme);

    $themes = [];
    while (mysqli_stmt_fetch($stmt)) {
        $themes[] = array('id' => $id_theme, 'libelle' => $libelle_theme, 'actif' => $actif_theme);
    }
    mysqli_stmt_close($stmt);
    return $themes;
}

//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville 
function get_ville($saisie)
{
    global $conn;

    //test si saisie avec le autocomplete (auquel cas ça se termine par des chiffres)
    if (is_numeric(substr($saisie, -3))) {
        $query = "SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ', ') AS `codes_postaux` 
			FROM `bsl__ville` 
			WHERE `nom_ville` LIKE ? AND `code_postal` LIKE ?
			GROUP BY `nom_ville`, `code_insee`";
        $stmt = mysqli_prepare($conn, $query);
        $ville = substr($saisie, 0, -6);
        $cp = substr($saisie, -5);
        mysqli_stmt_bind_param($stmt, 'ss', $ville, $cp);
    } else {
        $query = "SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ', ') AS `codes_postaux` 
			FROM `bsl__ville` 
			WHERE `nom_ville` LIKE ?
			GROUP BY `nom_ville`, `code_insee`";
        $stmt = mysqli_prepare($conn, $query);
        $saisie_insee = format_insee($saisie) . '%';
        mysqli_stmt_bind_param($stmt, 's', $saisie_insee);
    }

    mysqli_stmt_execute($stmt);
    check_mysql_error($conn);

    mysqli_stmt_bind_result($stmt, $nom_ville, $code_insee, $codes_postaux);
    while (mysqli_stmt_fetch($stmt)) {
        $row[] = array('nom_ville' => $nom_ville, 'code_insee' => $code_insee, 'codes_postaux' => $codes_postaux);
    }
    mysqli_stmt_close($stmt);
    return $row;
}

//************ récupération des éléments de la page du formulaire
function get_formulaire($etape)
{
    global $conn;

    $query = 'SELECT `bsl_formulaire`.`id_formulaire`, `bsl_formulaire`.`nb_pages`, `bsl_formulaire__page`.`titre`, `bsl_formulaire__page`.`ordre` AS `ordre_page`, `bsl_formulaire__page`.`aide`, `bsl_formulaire__question`.`id_question`, `bsl_formulaire__question`.`libelle` AS `libelle_question`, `bsl_formulaire__question`.`html_name`, `bsl_formulaire__question`.`type`, `bsl_formulaire__question`.`taille`, `bsl_formulaire__question`.`obligatoire`, `bsl_formulaire__valeur`.`libelle`, `bsl_formulaire__valeur`.`valeur`, `bsl_formulaire__valeur`.`defaut` FROM `bsl_formulaire` 
	JOIN `bsl_theme` ON `bsl_theme`.`id_theme`=`bsl_formulaire`.`id_theme`
	JOIN `bsl_formulaire__page` ON `bsl_formulaire__page`.`id_formulaire`=`bsl_formulaire`.`id_formulaire` AND `bsl_formulaire__page`.`actif`=1
	JOIN `bsl_formulaire__question` ON `bsl_formulaire__question`.`id_page`=`bsl_formulaire__page`.`id_page` AND `bsl_formulaire__question`.`actif`=1
	JOIN `bsl_formulaire__valeur` ON `bsl_formulaire__valeur`.`id_question`=`bsl_formulaire__question`.`id_question` AND `bsl_formulaire__valeur`.`actif`=1
	WHERE `bsl_formulaire`.`actif`=1 AND `bsl_theme`.`libelle_theme`= ? AND `bsl_formulaire__page`.`ordre` = ?
	ORDER BY `ordre_page`, `bsl_formulaire__question`.`ordre`, `bsl_formulaire__valeur`.`ordre`';
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $_SESSION['besoin'], $etape);

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
    return [$meta, $questions, $reponses];
}

//************ construction de LA requête
/*todo : il va falloir d'abord faire une requête pour lister les inputs (name/type) du formulaire - cf le get_formulaire - et construire en fonction la requête select */
function get_liste_offres()
{
    global $conn;

    $query = "SELECT `id_offre`, `nom_offre`, `description_offre`, `t`.`id_sous_theme`, `bsl_theme`.`libelle_theme` AS `sous_theme_offre`, `nom_pro` /*`t`.*, `bsl_theme`.`libelle_theme` AS `sous_theme_offre`, `theme_pere`.`libelle_theme` AS `theme_offre` */
	FROM ( SELECT `bsl_offre`.*,   /* on construit ici la liste des critères */
		GROUP_CONCAT( if(nom_critere= 'age_min', valeur_critere, NULL ) SEPARATOR '|') `age_min`, 
		GROUP_CONCAT( if(nom_critere= 'age_max', valeur_critere, NULL ) SEPARATOR '|') `age_max`, 
		GROUP_CONCAT( if(nom_critere= 'villes', valeur_critere, NULL ) SEPARATOR '|') `villes` ";
    foreach ($_SESSION['critere'] as $cle => $valeur) { //on va chercher les critères saisis dans le formulaire
        $c_cle = securite_bdd($conn, $cle);
        $query .= ", GROUP_CONCAT( if(nom_critere= '" . $c_cle . "', valeur_critere, NULL ) separator '|') `" . $c_cle . "`";
    }
    $query .= " FROM `bsl_offre_criteres`
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
		)))";
    //AND `t`.`age_min` <= ? AND `t`.`age_max` >= ?
    $terms = array($_SESSION['besoin'], '%' . $_SESSION['code_insee'] . '%', $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['code_insee']);
    //$terms = array ( $_SESSION['besoin'], '%'.$_SESSION['code_insee'].'%', $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['critere']['age'], $_SESSION['critere']['age'] );
    $terms_type = "sssss";
    //$terms_type = "sssssii";

    //foreach sur $_SESSION['critere'], en fonction du type
    foreach ($_SESSION['critere'] as $cle => $valeur) {
        //if($cle!="age"){
        $c_cle = securite_bdd($conn, $cle);
        if (isset($_SESSION['type'][$cle])) {
            switch ($_SESSION['type'][$cle]) {
                case 'select':
                case 'radio':
                    $query .= " AND `t`.`$c_cle` LIKE ? ";
                    $terms[] = '%' . $valeur . '%';
                    $terms_type .= "s";
                    break;
                case 'multiple':
                case 'checkbox':
                    $sql = "";
                    foreach ($_SESSION['critere'][$cle] as $selected_option) {
                        $sql .= " `t`.`$c_cle` LIKE ? OR";
                        $terms[] = '%' . $selected_option . '%';
                        $terms_type .= "s";
                    }
                    $query .= " AND (" . $sql . " FALSE)";
                    break;
            }
        }
        //}
    }
    $query .= " ORDER BY `bsl_theme`.`ordre_theme`";
    //todo : rendre dynamique les terms_type... (s ou i)

    if ($stmt = mysqli_prepare($conn, $query)) {

        // petite manip pour gérer le nombre variable de paramètres dans la requête
        $query_params = array();
        $query_params[] = $terms_type;
        foreach ($terms as $id => $term) {
            $query_params[] = &$terms[$id];
        }
        call_user_func_array(array($stmt, 'bind_param'), $query_params);
        // fin de la manip...

        $sous_themes = [];
        $offres = [];
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $id_offre, $nom_offre, $description_offre, $id_sous_theme, $sous_theme_offre, $nom_pro);
            while (mysqli_stmt_fetch($stmt)) {
                if (isset($sous_themes[$id_sous_theme])) {
                    $sous_themes[$id_sous_theme]['nb']++;
                } else {
                    $sous_themes[$id_sous_theme] = array('id' => $id_sous_theme, 'titre' => $sous_theme_offre, 'nb' => 1);
                }
                $offres[] = array('id' => $id_offre, 'titre' => $nom_offre, 'description' => $description_offre, 'sous_theme' => $id_sous_theme, 'nom_pro' => $nom_pro);
            }
        }
    } else {
        echo "L'application a rencontré un problème technique. Merci de contacter l'administrateur du site via le formulaire avec le message d'erreur suivant : " . mysqli_error($conn);
        exit;
    }
    mysqli_stmt_close($stmt);
    return [$sous_themes, $offres];
}

function get_offre($id)
{

    global $conn;
    $query = "SELECT `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, '%d/%m/%Y') AS date_debut, DATE_FORMAT(`fin_offre`, '%d/%m/%Y') AS date_fin, `theme_pere`.libelle_theme AS `theme_offre`, `theme_fils`.libelle_theme AS `sous_theme_offre`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `bsl_offre`.`visibilite_coordonnees`, `delai_offre`, `zone_selection_villes`, `nom_pro`  
		FROM `bsl_offre` 
		JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_offre`.id_professionnel 
		JOIN `bsl_theme` AS `theme_fils` ON `theme_fils`.id_theme=`bsl_offre`.id_sous_theme
		JOIN `bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`theme_fils`.id_theme_pere
		WHERE `actif_offre` = 1 AND `id_offre`= ? ";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    mysqli_stmt_execute($stmt);
    check_mysql_error($conn);

    $row = [];
    mysqli_stmt_bind_result($stmt, $row['nom_offre'], $row['description_offre'], $row['date_debut'], $row['date_fin'], $row['theme_offre'], $row['sous_theme_offre'], $row['adresse_offre'], $row['code_postal_offre'], $row['ville_offre'], $row['code_insee_offre'], $row['courriel_offre'], $row['telephone_offre'], $row['site_web_offre'], $row['visibilite_coordonnees'], $row['delai_offre'], $row['zone_selection_villes'], $row['nom_pro']);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $row;
}

function create_demande($id_offre, $coord)
{

    global $conn;
    $query = "INSERT INTO `bsl_demande`(`id_demande`, `date_demande`, `id_offre`, `contact_jeune`, `code_insee_jeune`, `profil`) VALUES (NULL, NOW(), ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    $liste = liste_criteres(',');
    mysqli_stmt_bind_param($stmt, 'isss', $id_offre, $coord, $_SESSION['code_insee'], $liste);

    mysqli_stmt_execute($stmt);
    check_mysql_error($conn);
    $id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);
    return $id;
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