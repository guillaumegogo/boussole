<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_PROFESSIONNEL);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
    $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page actif ou désactivés ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

//********* affichage liste résultats 
//tous les professionnel actifs, du territoire si choisi
$sql = 'SELECT `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,GROUP_CONCAT(libelle_theme_court SEPARATOR ", ") AS themes, competence_geo,  nom_departement, nom_region, nom_territoire  
FROM `'.DB_PREFIX.'bsl_professionnel` 
LEFT JOIN `'.DB_PREFIX.'bsl_professionnel_themes` ON `'.DB_PREFIX.'bsl_professionnel_themes`.`id_professionnel`=`'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`
LEFT JOIN `'.DB_PREFIX.'bsl_theme` ON `'.DB_PREFIX.'bsl_theme`.`id_theme`=`'.DB_PREFIX.'bsl_professionnel_themes`.`id_theme`
LEFT JOIN `'.DB_PREFIX.'bsl__departement` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="departemental" AND `'.DB_PREFIX.'bsl__departement`.`id_departement`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
LEFT JOIN `'.DB_PREFIX.'bsl__region` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="regional" AND `'.DB_PREFIX.'bsl__region`.`id_region`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
LEFT JOIN `'.DB_PREFIX.'bsl_territoire` ON `'.DB_PREFIX.'bsl_professionnel`.`competence_geo`="territoire" AND `'.DB_PREFIX.'bsl_territoire`.`id_territoire`=`'.DB_PREFIX.'bsl_professionnel`.`id_competence_geo`
WHERE `actif_pro`="' . $flag_actif . '" ';
if ($_SESSION['territoire_id']) {
    $sql .= 'AND `competence_geo`="territoire" AND `id_competence_geo`= ' . $_SESSION['territoire_id'];
}
$sql .= ' GROUP BY `'.DB_PREFIX.'bsl_professionnel`.`id_professionnel`,nom_pro,type_pro,ville_pro,code_postal_pro,competence_geo';
$result = mysqli_query($conn, $sql);

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? "<a href=\"professionnel_liste.php?actif=non\">Liste des professionnels désactivés</a>" : "<a href=\"professionnel_liste.php\">Liste des professionnels actifs</a>";

//view
require 'view/professionnel_liste.tpl.php';