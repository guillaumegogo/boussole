<?php

//********* affichage des thèmes disponibles en fonction de la ville choisie 
//todo : la requête fait la vérification des thèmes des pros autorisés à travailler sur une zone géographique englobant la zone indiquée : pays, région, département ou territoire. il faudra probablement descendre au niveau des offres pour une meilleure granularité. 
/* on pourrait descendre à la granularité de l'offre, mais la requête serait encore plus complexe :
(...) JOIN `'.DB_PREFIX.'bsl_offre` ON `'.DB_PREFIX.'bsl_offre`.id_professionnel=`'.DB_PREFIX.'bsl_professionnel`.id_professionnel
JOIN `'.DB_PREFIX.'bsl_theme` as theme_offre ON `'.DB_PREFIX.'bsl_offre`.id_sous_theme=theme_offre.id_theme
WHERE actif_offre=1 AND debut_offre <= CURDATE() AND fin_offre >= CURDATE() (...)*/
function get_themes()
{
    global $conn;

	$query = 'SELECT `id_theme`, `libelle_theme`, `actif_theme`, MAX(`c`) as `nb` FROM (
		SELECT DISTINCT `'.DB_PREFIX.'bsl_theme`.`id_theme`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme`, `'.DB_PREFIX.'bsl_theme`.`actif_theme` , COUNT(`'.DB_PREFIX.'bsl_professionnel`.id_professionnel) as `c`
		FROM `'.DB_PREFIX.'bsl_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes` ON `'.DB_PREFIX.'bsl_professionnel_themes`.`id_theme`=`'.DB_PREFIX.'bsl_theme`.`id_theme`
		LEFT JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`=`'.DB_PREFIX.'bsl_professionnel_themes`.`id_professionnel` AND `'.DB_PREFIX.'bsl_professionnel`.`actif_pro`=1
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` ON `'.DB_PREFIX.'bsl_territoire_villes`.`id_territoire`=`'.DB_PREFIX.'bsl_territoire`.`id_territoire` 
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
		LEFT JOIN `'.DB_PREFIX.'bsl__departement` as `'.DB_PREFIX.'bsl__departement_region` ON `'.DB_PREFIX.'bsl__departement_region`.`id_region`=`'.DB_PREFIX.'bsl__region`.`id_region` 
		WHERE `id_theme_pere` IS NULL AND (`'.DB_PREFIX.'bsl_professionnel`.competence_geo="national" OR `'.DB_PREFIX.'bsl_territoire_villes`.`code_insee`=? OR `'.DB_PREFIX.'bsl__departement_region`.`id_departement`=SUBSTR(?,1,2) OR `'.DB_PREFIX.'bsl__departement`.`id_departement`=SUBSTR(?,1,2)) 
		GROUP BY `'.DB_PREFIX.'bsl_theme`.`id_theme`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme`, `'.DB_PREFIX.'bsl_theme`.`actif_theme` 
		UNION
		SELECT DISTINCT `'.DB_PREFIX.'bsl_theme`.id_theme, `'.DB_PREFIX.'bsl_theme`.`libelle_theme`, `'.DB_PREFIX.'bsl_theme`.`actif_theme`, 0 as `c`
		FROM `'.DB_PREFIX.'bsl_theme`
		WHERE `id_theme_pere` IS NULL) as `t`
	GROUP BY `id_theme`, `libelle_theme`, `actif_theme`';

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['code_insee']);

    mysqli_stmt_execute($stmt);
    if (mysqli_error($conn)) {
        echo mysqli_error($conn);
        exit;
    }
    mysqli_stmt_bind_result($stmt, $id_theme, $libelle_theme, $actif_theme, $nb);

    $themes = [];
    while (mysqli_stmt_fetch($stmt)) {
        $themes[] = array('id' => $id_theme, 'libelle' => $libelle_theme, 'actif' => $actif_theme*$nb); //si le thème est désactivé nationalement, ou s'il n'y a pas d'offre sur le territoire, alors il est considéré comme désactivé
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

    $query = 'SELECT `'.DB_PREFIX.'bsl_formulaire`.`id_formulaire`, `'.DB_PREFIX.'bsl_formulaire`.`nb_pages`, `'.DB_PREFIX.'bsl_formulaire__page`.`titre`, `'.DB_PREFIX.'bsl_formulaire__page`.`ordre` AS `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__page`.`aide`, `'.DB_PREFIX.'bsl_formulaire__question`.`id_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`libelle` AS `libelle_question`, `'.DB_PREFIX.'bsl_formulaire__question`.`html_name`, `'.DB_PREFIX.'bsl_formulaire__question`.`type`, `'.DB_PREFIX.'bsl_formulaire__question`.`taille`, `'.DB_PREFIX.'bsl_formulaire__question`.`obligatoire`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`libelle`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`valeur`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`defaut` FROM `'.DB_PREFIX.'bsl_formulaire` 
	JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_formulaire`.`id_theme`
	JOIN `'.DB_PREFIX.'bsl_formulaire__page` ON `'.DB_PREFIX.'bsl_formulaire__page`.`id_formulaire`=`'.DB_PREFIX.'bsl_formulaire`.`id_formulaire` AND `'.DB_PREFIX.'bsl_formulaire__page`.`actif`=1
	JOIN `'.DB_PREFIX.'bsl_formulaire__question` ON `'.DB_PREFIX.'bsl_formulaire__question`.`id_page`=`'.DB_PREFIX.'bsl_formulaire__page`.`id_page` AND `'.DB_PREFIX.'bsl_formulaire__question`.`actif`=1
	JOIN `'.DB_PREFIX.'bsl_formulaire__valeur` ON `'.DB_PREFIX.'bsl_formulaire__valeur`.`id_question`=`'.DB_PREFIX.'bsl_formulaire__question`.`id_question` AND `'.DB_PREFIX.'bsl_formulaire__valeur`.`actif`=1
	WHERE `'.DB_PREFIX.'bsl_formulaire`.`actif`=1 AND `'.DB_PREFIX.'bsl_theme`.`libelle_theme`= ? AND `'.DB_PREFIX.'bsl_formulaire__page`.`ordre` = ?
	ORDER BY `ordre_page`, `'.DB_PREFIX.'bsl_formulaire__question`.`ordre`, `'.DB_PREFIX.'bsl_formulaire__valeur`.`ordre`';
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
function get_liste_offres()
{
    global $conn;

    $query = 'SELECT `id_offre`, `nom_offre`, `description_offre`, `t`.`id_sous_theme`, `'.DB_PREFIX.'bsl_theme`.`libelle_theme` AS `sous_theme_offre`, `nom_pro` /*`t`.*, `'.DB_PREFIX.'bsl_theme`.`libelle_theme` AS `sous_theme_offre`, `theme_pere`.`libelle_theme` AS `theme_offre` */
	FROM ( SELECT `'.DB_PREFIX.'bsl_offre`.*,   /* on construit ici la liste des critères */
		GROUP_CONCAT( if(nom_critere= "age_min", valeur_critere, NULL ) SEPARATOR "|") `age_min`, 
		GROUP_CONCAT( if(nom_critere= "age_max", valeur_critere, NULL ) SEPARATOR "|") `age_max`, 
		GROUP_CONCAT( if(nom_critere= "villes", valeur_critere, NULL ) SEPARATOR "|") `villes` ';
    foreach ($_SESSION['critere'] as $cle => $valeur) { //on va chercher les critères saisis dans le formulaire
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
	LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `t`.`zone_selection_villes`=0 AND `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo` 
	LEFT JOIN `'.DB_PREFIX.'bsl_territoire_villes` ON `'.DB_PREFIX.'bsl_territoire_villes`.`id_territoire`=`'.DB_PREFIX.'bsl_territoire`.`id_territoire`
	LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `'.DB_PREFIX.'bsl__departement` as `'.DB_PREFIX.'bsl__departement_region` ON `'.DB_PREFIX.'bsl__departement_region`.`id_region`=`'.DB_PREFIX.'bsl__region`.`id_region`

	WHERE `t`.`debut_offre` <= CURDATE() AND `t`.`fin_offre` >= CURDATE() 
	AND `'.DB_PREFIX.'bsl_professionnel`.`actif_pro` = 1
	AND `theme_pere`.`libelle_theme` = ? 
	AND ((`t`.`zone_selection_villes`=1 AND `t`.`villes` LIKE ?) /* soit il y a une liste de villes au niveau de l offre */
		OR (`t`.`zone_selection_villes`=0 AND ( /* sinon il faut chercher dans la zone de compétence du pro */
			`'.DB_PREFIX.'bsl_professionnel`.`competence_geo` = "national"
			OR `'.DB_PREFIX.'bsl_territoire_villes`.`code_insee` = ?
			OR `'.DB_PREFIX.'bsl__departement`.`id_departement` = SUBSTR(?,1,2) 
			OR `'.DB_PREFIX.'bsl__departement_region`.`id_departement` = SUBSTR(?,1,2)
		)))';
    $terms = array($_SESSION['besoin'], '%' . $_SESSION['code_insee'] . '%', $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['code_insee']);
    $terms_type = "sssss";

    //foreach sur $_SESSION['critere'], en fonction du type on continue de construire la requete
    foreach ($_SESSION['critere'] as $cle => $valeur) {
        $c_cle = securite_bdd($conn, $cle);
        if (isset($_SESSION['type'][$cle])) {
            switch ($_SESSION['type'][$cle]) {
                case 'select':
                case 'radio':
                    $query .= ' AND `t`.`'.$c_cle.'` LIKE ? ';
                    $terms[] = '%' . $valeur . '%';
                    $terms_type .= "s";
                    break;
                case 'multiple':
                case 'checkbox':
                    $sql = '';
                    foreach ($_SESSION['critere'][$cle] as $selected_option) {
                        $sql .= ' `t`.`'.$c_cle.'` LIKE ? OR';
                        $terms[] = '%' . $selected_option . '%';
                        $terms_type .= "s";
                    }
                    $query .= ' AND (' . $sql . ' FALSE)';
                    break;
            }
        }
    }
    $query .= ' ORDER BY `'.DB_PREFIX.'bsl_theme`.`ordre_theme`';
    //todo : rendre dynamique les terms_type... (mettre des i à la place des s quand c'est pertinent)

//****** debug : impression d'une requête
/*echo "<pre>"; 
$print_sql = $query;
foreach($terms as $term){
$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
}
echo $print_sql;
echo "<br/><br/>";
print_r($_SESSION);
echo "</pre>"; */
 
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
    $query = 'SELECT `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, "%d/%m/%Y") AS date_debut, DATE_FORMAT(`fin_offre`, "%d/%m/%Y") AS date_fin, `theme_pere`.libelle_theme AS `theme_offre`, `theme_fils`.libelle_theme AS `sous_theme_offre`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `'.DB_PREFIX.'bsl_offre`.`visibilite_coordonnees`, `delai_offre`, `zone_selection_villes`, `nom_pro`  
		FROM `'.DB_PREFIX.'bsl_offre` 
		JOIN `'.DB_PREFIX.'bsl_professionnel` ON `'.DB_PREFIX.'bsl_professionnel`.id_professionnel=`'.DB_PREFIX.'bsl_offre`.id_professionnel 
		JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_fils` ON `theme_fils`.id_theme=`'.DB_PREFIX.'bsl_offre`.id_sous_theme
		JOIN `'.DB_PREFIX.'bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`theme_fils`.id_theme_pere
		WHERE `actif_offre` = 1 AND `id_offre`= ? ';
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
    $query = 'INSERT INTO `'.DB_PREFIX.'bsl_demande`(`id_demande`, `date_demande`, `id_offre`, `contact_jeune`, `code_insee_jeune`, `profil`) VALUES (NULL, NOW(), ?, ?, ?, ?)';
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
 *
 * //****** impression d'une requête
 * $print_sql = $query;
 * foreach($terms as $term){
 * 	$print_sql = preg_replace('/\?/', '"'.$term.'"', $print_sql, 1);
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