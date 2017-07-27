<?php

include('../src/admin/bootstrap.php');

//********* verif des droits
checkLogin(PAGE_OFFRE_LISTE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
    $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]);
}
include('admin/select_territoires.inc.php');

//********page des offres actives ou désactivées ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

//******** liste des offres de service
$sql = "SELECT id_offre, nom_offre, DATE_FORMAT(`debut_offre`, '%d/%m/%Y') AS date_debut, DATE_FORMAT(`fin_offre`, '%d/%m/%Y') AS date_fin, `theme_pere`.libelle_theme_court, zone_selection_villes, nom_pro, `competence_geo`, `id_competence_geo`, nom_departement, nom_region, nom_territoire  
	FROM `bsl_offre` 
	JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_offre`.`id_professionnel`
	LEFT JOIN `bsl_theme` ON bsl_theme.id_theme=`bsl_offre`.`id_sous_theme`
	LEFT JOIN `bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`bsl_theme`.`id_theme_pere`
	LEFT JOIN `bsl__departement` ON `bsl_professionnel`.`competence_geo`=\"departemental\" AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `bsl__region` ON `bsl_professionnel`.`competence_geo`=\"regional\" AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
	LEFT JOIN `bsl_territoire` ON `bsl_professionnel`.`competence_geo`=\"territoire\" AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo`
	WHERE actif_offre='" . $flag_actif . "' ";
if (isset($_SESSION['territoire_id']) && $_SESSION['territoire_id']) {
    $sql .= "AND `competence_geo`=\"territoire\" AND `id_competence_geo`= " . $_SESSION['territoire_id'];
}
if (isset($_SESSION['user_pro_id'])) {
    $sql .= "AND `bsl_professionnel`.id_professionnel = " . $_SESSION['user_pro_id'];
}
$result = mysqli_query($conn, $sql);

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? "<a href=\"offre_liste.php?actif=non\">Liste des offres désactivées</a>" : "<a href=\"offre_liste.php\">Liste des offres actives</a>";

//view
require 'view/offre_liste.tpl.php';