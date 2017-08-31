<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_PROFESSIONNEL);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
    secu_set_territoire_id($_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page actif ou désactivés ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

//********* affichage liste résultats 
$territoire_id = secu_get_territoire_id();
$pros = get_liste_pros($flag_actif, $territoire_id); //tous les professionnel actifs, du territoire si choisi

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? "<a href=\"professionnel_liste.php?actif=non\">Liste des professionnels désactivés</a>" : "<a href=\"professionnel_liste.php\">Liste des professionnels actifs</a>";

//view
require 'view/professionnel_liste.tpl.php';