<?php

include('../src/admin/bootstrap.php');

//********* verif des droits
checkLogin(PAGE_PROFESSIONNEL_LISTE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
    $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]);
}
include('admin/select_territoires.inc.php');

//********page actif ou désactivés ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

//********* affichage liste résultats 
//tous les professionnel actifs, du territoire si choisi
$sql = "SELECT `bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,GROUP_CONCAT(libelle_theme_court SEPARATOR ', ') AS themes, competence_geo,  nom_departement, nom_region, nom_territoire  
FROM `bsl_professionnel` 
LEFT JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.`id_professionnel`=`bsl_professionnel`.`id_professionnel`
LEFT JOIN `bsl_theme` ON `bsl_theme`.`id_theme`=`bsl_professionnel_themes`.`id_theme`
LEFT JOIN `bsl__departement` ON `bsl_professionnel`.`competence_geo`=\"departemental\" AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
LEFT JOIN `bsl__region` ON `bsl_professionnel`.`competence_geo`=\"regional\" AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
LEFT JOIN `bsl_territoire` ON `bsl_professionnel`.`competence_geo`=\"territoire\" AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo`
WHERE `actif_pro`='" . $flag_actif . "' ";
if ($_SESSION['territoire_id']) {
    $sql .= "AND `competence_geo`=\"territoire\" AND `id_competence_geo`= " . $_SESSION['territoire_id'];
}
$sql .= " GROUP BY `bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,competence_geo";
$result = mysqli_query($conn, $sql);

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? "<a href=\"professionnel_liste.php?actif=non\">Liste des professionnels désactivés</a>" : "<a href=\"professionnel_liste.php\">Liste des professionnels actifs</a>";

//view
require 'view/professionnel_liste.tpl.php';