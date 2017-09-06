<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_CRITERE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
	secu_set_territoire_id($_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page des formulaires actifs ou désactivés ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == "non") ? 0 : 1;

//******** liste de demandes
$territoire_id = secu_get_territoire_id();
$formulaires = get_liste_formulaires($flag_actif, $territoire_id);

//********** lien actifs/désactivés
$titre_page = ($flag_actif) ? "Liste des formulaires actifs" : "Liste des formulaires désactivés";
$lien_desactives = ($flag_actif) ? "<a href=\"?actif=non\">Liste des formulaires désactivés</a>" : "<a href=\"?actif=oui\">Liste des formulaires actifs</a>";

//view
require 'view/formulaire_liste.tpl.php';