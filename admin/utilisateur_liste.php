<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_UTILISATEUR);

//********* territoire sélectionné
if (isset($_POST['choix_territoire'])) {
	secu_set_territoire_id($_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page actif ou désactivés ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == 'non') ? 0 : 1;

//********* affichage liste résultats 
$territoire_id = secu_get_territoire_id();
$users = get_liste_users($flag_actif, $territoire_id); //tous les utilisateurs actifs, du territoire le cas échéant

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? '<a href=\'utilisateur_liste.php?actif=non\'>Liste des utilisateurs désactivés</a>' : '<a href=\'utilisateur_liste.php\'>Liste des utilisateurs actifs</a>';

//view
require 'view/utilisateur_liste.tpl.php';