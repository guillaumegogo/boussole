<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_OFFRE);

/*todo
$sql = "SELECT `id_offre` FROM `bsl_offre` JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_offre`.id_professionnel 
WHERE id_offre=".$id_offre;
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	$row = mysqli_fetch_assoc($result);
		
if (isset($_SESSION['user_pro_id']) && isset($_GET["id"]) && $_SESSION['user_pro_id']!=$_GET["id"]) header('Location: accueil.php'); //si tu es un professionnel qui essaie de voir une autre fiche, tu retournes à l'accueil

if (isset($_SESSION['user_pro_id']) && $_SESSION['user_pro_id']!=$row['id_professionnel']) header('Location: accueil.php'); //si tu es un professionnel qui essaie de voir une offre qui n'est pas la tienne, tu retournes à l'accueil*/

//********* variables
$last_id = null;
$msg = "";
$criteres = array();
$liste_pro = "<option value=\"\" >A choisir</option>";
$geo = "";

//********** si post du formulaire interne
if (isset($_POST["maj_id"])) {

    //requête d'ajout (on récupère les données de contact du pro sélectionné)
    if (!$_POST["maj_id"]) {
        $req = "INSERT INTO `bsl_offre`(`nom_offre`, `description_offre`, `debut_offre`, `fin_offre`, `id_professionnel`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `code_insee_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `user_derniere_modif`) 
		SELECT \"" . $_POST["nom"] . "\",\"" . mysqli_real_escape_string($conn, $_POST["desc"]) . "\",\"" . date("Y-m-d", strtotime(str_replace('/', '-', $_POST["du"]))) . "\",\"" . date("Y-m-d", strtotime(str_replace('/', '-', $_POST["au"]))) . "\",\"" . $_POST["pro"] . "\",`adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`,`courriel_pro`, `telephone_pro`, `site_web_pro`, `delai_pro`,\"" . secu_get_current_user_id() . "\"
		FROM `bsl_professionnel`
		WHERE `bsl_professionnel`.id_professionnel = \"" . $_POST["pro"] . "\"";

        $result = mysqli_query($conn, $req);
        $last_id = mysqli_insert_id($conn);

        //requête de modification
    } else {
        $last_id = $_POST["maj_id"];
        $code_postal = substr($_POST["commune"], -5);
        $ville = substr($_POST["commune"], 0, -6);
        $code_insee = "";
        $sql = "SELECT code_insee FROM `bsl__ville` WHERE code_postal='" . $code_postal . "' AND nom_ville LIKE '" . $ville . "'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $code_insee = $row['code_insee'];
        }
        $url = $_POST["site"];
        if (substr($url, 0, 3) == 'www') {
            $url = 'http://' . $url;
        }
        $req = "UPDATE `bsl_offre` SET `nom_offre` = \"" . $_POST["nom"] . "\", `description_offre` = \"" . mysqli_real_escape_string($conn, $_POST["desc"]) . "\", `debut_offre` = \"" . date("Y-m-d", strtotime(str_replace('/', '-', $_POST["du"]))) . "\", `fin_offre` = \"" . date("Y-m-d", strtotime(str_replace('/', '-', $_POST["au"]))) . "\", `id_sous_theme` = \"" . $_POST["sous_theme"] . "\", `adresse_offre` = \"" . $_POST["adresse"] . "\",`code_postal_offre`=\"" . $code_postal . "\",`ville_offre`=\"" . $ville . "\",`code_insee_offre`=\"" . $code_insee . "\", `courriel_offre` = \"" . $_POST["courriel"] . "\", `telephone_offre` = \"" . $_POST["tel"] . "\", `site_web_offre` = \"" . $url . "\", `delai_offre` = \"" . $_POST["delai"] . "\", `zone_selection_villes` = \"" . $_POST["zone"] . "\", `actif_offre` = \"" . $_POST["actif"] . "\",`user_derniere_modif`=\"" . secu_get_current_user_id() . "\" WHERE `id_offre` = " . $last_id;
        $result = mysqli_query($conn, $req);

        if (isset($_POST["maj_criteres"]) && $_POST["maj_criteres"]) { //mise à jour des critères

            $reqd = "DELETE FROM `bsl_offre_criteres` WHERE `id_offre` = " . $last_id;
            mysqli_query($conn, $reqd);

            $req2 = "INSERT INTO `bsl_offre_criteres` (`id_offre`, `nom_critere`, `valeur_critere`) VALUES ";
            if (isset($_POST['list2'])) {
                foreach ($_POST['list2'] as $selected_option) {
                    $req2 .= "(" . $last_id . ", \"villes\", \"" . $selected_option . "\"), ";
                }
            }
            foreach ($_POST['critere'] as $name => $tab_critere) {
                foreach ($tab_critere as $key => $selected_option) {
                    $req2 .= "(" . $last_id . ", \"" . $name . "\", \"" . $selected_option . "\"), ";
                }
            }
            $req2 = substr($req2, 0, -2); //on enlève le dernier ", "
            $result2 = mysqli_query($conn, $req2);
        }
    }

    if ($result && (!isset($result2) || $result2)) {
        $msg = "Modification bien enregistrée.";
    } else {
        $msg = "Il y a eu un problème à l'enregistrement. Contactez l'administration centrale si le problème perdure.";
    }
}

//********** récupération de l'id de l'offre (soit celle en paramètre, soit celle qui vient d'être créée/mise à jour)
$id_offre = $last_id;
if (isset($_GET["id"])) {
    $id_offre = $_GET["id"];
}
//********** affichage de l'offre
if (isset($id_offre)) {
    $sql = "SELECT `id_offre`, `nom_offre`, `description_offre`, DATE_FORMAT(`debut_offre`, '%d/%m/%Y') AS date_debut, DATE_FORMAT(`fin_offre`, '%d/%m/%Y') AS date_fin, `id_sous_theme`, `adresse_offre`, `code_postal_offre`, `ville_offre`, `courriel_offre`, `telephone_offre`, `site_web_offre`, `delai_offre`, `zone_selection_villes`, `actif_offre`, `bsl_professionnel`.id_professionnel, `nom_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, competence_geo, id_theme_pere, nom_departement, nom_region, nom_territoire, id_competence_geo 
	FROM `bsl_offre` 
	JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_offre`.id_professionnel 
	LEFT JOIN `bsl_theme` ON bsl_theme.id_theme=`bsl_offre`.id_sous_theme
	LEFT JOIN `bsl__departement` ON `bsl_professionnel`.`competence_geo`=\"departemental\" AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `bsl__region` ON `bsl_professionnel`.`competence_geo`=\"regional\" AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `bsl_territoire` ON `bsl_professionnel`.`competence_geo`=\"territoire\" AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo`
	WHERE id_offre=" . $id_offre;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        //affichage de la compétence géo du pro
        switch ($row['competence_geo']) {
            case "territoire":
                $geo = $row['competence_geo'] . " " . $row['nom_territoire'];
                break;
            case "departemental":
                $geo = $row['competence_geo'] . " " . $row['nom_departement'];
                break;
            case "regional":
                $geo = $row['competence_geo'] . " " . $row['nom_region'];
                break;
            case "national":
                $geo = $row['competence_geo'];
                break;
        }

        //****************** new : formulaire dynamique...
        if (isset($row['id_theme_pere']) && $row['id_theme_pere']) {
            $t = get_criteres($id_offre, $row['id_theme_pere']);
            $questions = $t[0];
            $reponses = $t[1];
        }

        //affichage des critères de l'offre (selected dans listes déroulantes)
        /* **** old **** remplacé par requête précédente
        $sql2 = "SELECT * FROM `bsl_offre_criteres` where id_offre=".$id_offre;
        $result2 = mysqli_query($conn, $sql2);
        while ($row2 = mysqli_fetch_assoc($result2)) {
            $criteres[$row2['nom_critere']][$row2['valeur_critere']]=1;
        }
        */
    }

    //********* liste déroulante des thèmes / sous-thèmes du pro
    $select_theme = "";
    if (!$row['id_theme_pere']) {
        $select_theme = "<option value=\"\">A choisir</option>";
    }
    $select_sous_theme = "";
    if (!$row['id_sous_theme']) {
        $select_sous_theme = "<option value=\"\">A choisir</option>";
    }
    $tab_select_soustheme = array();

    $sqlt = "SELECT `bsl_theme`.`id_theme`, `libelle_theme`,`id_professionnel`, `id_theme_pere` 
		FROM `bsl_theme` 
		LEFT JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.`id_theme`=`bsl_theme`.`id_theme` 
		WHERE `actif_theme` = 1 AND (id_professionnel IS NULL OR id_professionnel= \"" . $row['id_professionnel'] . "\")";
    $result = mysqli_query($conn, $sqlt);
    while ($rowt = mysqli_fetch_assoc($result)) {
        //liste des thèmes
        if (!isset($rowt['id_theme_pere'])) {
            if ($rowt['id_professionnel'] == $row['id_professionnel']) {
                $select_theme .= "<option value=\"" . $rowt['id_theme'] . "\" ";
                if ($rowt['id_theme'] == $row['id_theme_pere']) {
                    $select_theme .= " selected ";
                }
                $select_theme .= ">" . $rowt['libelle_theme'] . "</option>";
            }
            $tab_select_soustheme[$rowt['id_theme']] = "";
            //liste des sous-thèmes (par défaut les sous-thèmes du thème-père sélectionné)
        } else {
            if ($rowt['id_theme_pere'] == $row['id_theme_pere']) {
                $select_sous_theme .= "<option value=\"" . $rowt['id_theme'] . "\" ";
                if ($rowt['id_theme'] == $row['id_sous_theme']) {
                    $select_sous_theme .= " selected ";
                }
                $select_sous_theme .= ">" . $rowt['libelle_theme'] . "</option>";
            }
            //tableau des listes pour fonction javascript ci-dessous
            if (isset($tab_select_soustheme[$rowt['id_theme_pere']])) {
                $tab_select_soustheme[$rowt['id_theme_pere']] .= "<option value='" . $rowt['id_theme'] . "'>" . $rowt['libelle_theme'] . "</option>";
            }
        }
    }

    //*********** liste des villes accessibles au pro
    $liste_villes_pro = "";
    $sqlv = "SELECT `bsl__ville`.`code_insee`, MIN(`bsl__ville`.`code_postal`) AS cp, `bsl__ville`.`nom_ville` 
				FROM `bsl__ville` ";
    switch ($row['competence_geo']) {
        case "territoire":
            $sqlv .= " JOIN `bsl_territoire_villes` ON `bsl_territoire_villes`.code_insee=`bsl__ville`.code_insee
			WHERE id_territoire=\"" . $row['id_competence_geo'] . "\"";
            break;
        case "departemental":
            $sqlv .= " WHERE SUBSTR(`bsl__ville`.code_insee,1,2)=\"" . $row['id_competence_geo'] . "\"";
            break;
        case "regional":
            $sqlv .= " JOIN `bsl__departement` ON SUBSTR(`bsl__ville`.code_insee,1,2)=`bsl__departement`.id_departement AND id_region=\"" . $row['id_competence_geo'] . "\"";
            break;
        case "national":
            $sqlv .= "";
            break;
    }
    $sqlv .= "GROUP BY `bsl__ville`.`code_insee`, `bsl__ville`.`nom_ville`
				ORDER BY nom_ville";
    $result = mysqli_query($conn, $sqlv);
    if (mysqli_num_rows($result) > 0) {
        while ($rowv = mysqli_fetch_assoc($result)) {
            $liste_villes_pro .= "<option value=\"" . $rowv['code_insee'] . "\">" . $rowv['nom_ville'] . " " . $rowv['cp'] . "</option>";
        }
    }

    //*********** liste des villes liées à l'offre
    $liste2 = "";
    if ($row['zone_selection_villes']) {
        $sqlv2 = "SELECT * FROM `bsl_offre_criteres` 
		JOIN bsl__ville ON valeur_critere=code_insee 
		WHERE `nom_critere` LIKE 'villes' AND id_offre=" . $id_offre . " 
		ORDER BY nom_ville";
        $result = mysqli_query($conn, $sqlv2);
        if (mysqli_num_rows($result) > 0) {
            while ($rowv2 = mysqli_fetch_assoc($result)) {
                $liste2 .= "<option value=\"" . $rowv2['code_insee'] . "\">" . $rowv2['nom_ville'] . " " . $rowv2['code_postal'] . "</option>";
            }
        }
    }

//********** sinon écran de création simple : récupération de la liste des professionnels (avec thème) en fonction des droits du user
} else {
    $sql = "SELECT `bsl_professionnel`.`id_professionnel`, `nom_pro` FROM `bsl_professionnel` 
		WHERE `actif_pro`=1 "; //todo limiter en fonction du user_statut
    //JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.`id_professionnel`=`bsl_professionnel`.`id_professionnel`
    if (isset($_SESSION['territoire_id']) && $_SESSION['territoire_id']) {
        $sql .= " AND `competence_geo`=\"territoire\" AND `id_competence_geo`=" . $_SESSION['territoire_id'];
    }
    if (isset($_SESSION['user_pro_id'])) {
        $sql .= " AND `bsl_professionnel`.id_professionnel = " . $_SESSION['user_pro_id'];
    }
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($rowp = mysqli_fetch_assoc($result)) {
            $liste_pro .= "<option value=\"" . $rowp['id_professionnel'] . "\"";
            if (isset($_SESSION['user_pro_id']) && $rowp['id_professionnel'] == $_SESSION['user_pro_id']) {
                $liste_pro .= " selected ";
            }
            $liste_pro .= ">" . $rowp['nom_pro'] . "</option>";
        }
    }
}

//view
require 'view/offre_detail.tpl.php';