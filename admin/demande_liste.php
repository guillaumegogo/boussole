<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_DEMANDE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
    secu_set_territoire_id($_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page des demandes traitées ou à traiter ?
$flag_traite = (isset($_GET['etat']) && $_GET['etat'] == "traite") ? 1 : 0;

//******** liste de demandes
$territoire_id = secu_get_territoire_id();
$user_pro_id = secu_get_user_pro_id();
$demandes = get_liste_demandes($flag_traite, $territoire_id, $user_pro_id);

//********** lien actifs/désactivés
$titre_page = ($flag_traite) ? "Liste des demandes traitées" : "Liste des demandes à traiter";
$lien_traites = ($flag_traite) ? "<a href=\"demande_liste.php\">Liste des demandes à traiter</a>" : "<a href=\"demande_liste.php?etat=traite\">Liste des demandes traitées</a>";

//view
require 'view/demande_liste.tpl.php';