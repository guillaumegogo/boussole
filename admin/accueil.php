<?php

include('../src/admin/bootstrap.php');
secu_check_login();

//******** calcul du nb de demandes (todo : à adapter pour pros et animateurs)
$nb = '';
if (secu_check_role(ROLE_ADMIN)) {
	$nb = '('.get_nb_nouvelles_demandes().')';
}

//******* construction des listes de lien
if (secu_is_authorized(DROIT_DEMANDE)) {
	$activites[] = array('demande_liste.php', 'Demandes à traiter', $nb);
}

//******* construction des listes de lien
if (secu_is_authorized(DROIT_OFFRE)) {
	$offres[] = array('offre_liste.php', 'Offres de service', '');
}
if (secu_is_authorized(DROIT_MESURE)) {
	$offres[] = array('mesure_liste.php', 'Mesurier', '<img src="img/help.png" height="16px" title="Les mesures sont à usage interne. Elles ne sont pas visibles sur le site web.">');
}

$liens_admin = '';
if (secu_is_authorized(DROIT_PROFESSIONNEL)) {
	$acteurs[] = array('professionnel_liste.php', 'Professionnels', '');
} else if (isset($_SESSION['user_pro_id'])) {
	$acteurs[] = array('professionnel_detail.php?id=' . $_SESSION['user_pro_id'], 'Professionnels', '');
}
if (secu_is_authorized(DROIT_UTILISATEUR)) {
	$acteurs[] = array('utilisateur_liste.php', 'Utilisateurs', '');
} else if (isset($_SESSION['user_pro_id'])) {
	$acteurs[] = array('professionnel_detail.php?id=' . $_SESSION['user_pro_id'], 'Utilisateurs', '');
}

$liens_reference = '';
if (secu_is_authorized(DROIT_TERRITOIRE)) {
	$references[] = array('territoire.php', 'Territoires', '');
}
if (secu_is_authorized(DROIT_THEME)) {
	$references[] = array('theme.php', 'Thèmes et sous-thèmes', '');
}
if (secu_is_authorized(DROIT_CRITERE)) {
	$references[] = array('formulaire_liste.php', 'Formulaires', '');
}

//********** sélection territoire
if (isset($_POST['choix_territoire'])) {
	$_SESSION['territoire_id'] = $_POST['choix_territoire'];
}

include('../src/admin/select_territoires.inc.php');

//view
require 'view/accueil.tpl.php';