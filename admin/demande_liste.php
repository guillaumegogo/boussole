<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_DEMANDE);

//********* territoire sélectionné
if (isset($_POST["choix_territoire"])) {
    $_SESSION['territoire_id'] = securite_bdd($conn, $_POST["choix_territoire"]);
}
include('../src/admin/select_territoires.inc.php');

//********page des demandes traitées ou à traiter ?
$flag_traite = (isset($_GET['etat']) && $_GET['etat'] == "traite") ? 1 : 0;

//******** liste de demandes
$sql = "SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, bsl_offre.nom_offre, bsl_offre.id_professionnel, bsl_professionnel.nom_pro   
    FROM `bsl_demande` JOIN bsl_offre ON bsl_offre.id_offre=bsl_demande.id_offre JOIN bsl_professionnel ON bsl_offre.id_professionnel=bsl_professionnel.id_professionnel
	WHERE 1 ";
if ($flag_traite) {
    $sql .= "AND date_traitement IS NOT NULL ";
} else {
    $sql .= "AND date_traitement IS NULL ";
}
if (isset($_SESSION['territoire_id']) && $_SESSION['territoire_id']) {
    $sql .= "AND bsl_professionnel.`competence_geo`=\"territoire\" AND bsl_professionnel.`id_competence_geo`= " . $_SESSION['territoire_id'];
}
if (isset($_SESSION['user_pro_id'])) {
    $sql .= "AND `bsl_offre`.id_professionnel = " . $_SESSION['user_pro_id'];
}
$sql .= " ORDER BY date_demande DESC";
$result = mysqli_query($conn, $sql);

//********** lien actifs/désactivés
$titre_page = ($flag_traite) ? "Liste des demandes traitées" : "Liste des demandes à traiter";
$lien_traites = ($flag_traite) ? "<a href=\"demande_liste.php\">Liste des demandes à traiter</a>" : "<a href=\"demande_liste.php?etat=traite\">Liste des demandes traitées</a>";

//view
require '../src/admin/view/demande_liste.tpl.php';