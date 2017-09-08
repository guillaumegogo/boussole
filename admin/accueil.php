<?php

include('../src/admin/bootstrap.php');
secu_check_login();

//******** calcul du nb de demandes (todo : à adapter pour pros et animateurs)
$nb_dmd = '';
if (secu_check_role(ROLE_ADMIN)) {
	$nb = get_nb_nouvelles_demandes();
	if ($nb == 1) {
		$nb_dmd = '(' . $nb . ' nouvelle)';
	} else if ($nb > 1) {
		$nb_dmd = '(' . $nb . ' nouvelles)';
	}
}

//******* construction des listes de lien
$liens_activite = '';
if (secu_is_authorized(DROIT_OFFRE)) {
	$liens_activite .= '<li><a href=\'offre_liste.php\'>Offres de service</a></li>';
}
if (secu_is_authorized(DROIT_OFFRE)) {
	$liens_activite .= '<li>Mesurier</li>';
}
if (secu_is_authorized(DROIT_DEMANDE)) {
	$liens_activite .= '<li><a href=\'demande_liste.php\'>Demandes reçues</a> ' . $nb_dmd . '</li>';
}

$liens_admin = '';
if (secu_is_authorized(DROIT_PROFESSIONNEL)) {
	$liens_admin .= '<li><a href=\'professionnel_liste.php\'>Professionnels</a></li>';
} else if (isset($_SESSION['user_pro_id'])) {
	$liens_admin .= '<li><a href=\'professionnel_detail.php?id=' . $_SESSION['user_pro_id'] . '\'>Détails de mon organisation</a></li>'; //lien professionnel_detail des utilisateurs 'professionnels'
}
if (secu_is_authorized(DROIT_UTILISATEUR)) {
	$liens_admin .= '<li><a href=\'utilisateur_liste.php\'>Utilisateurs</a></li>';
} else if (isset($_SESSION['user_pro_id'])) {
	$liens_admin .= '<li><a href=\'professionnel_detail.php?id=' . $_SESSION['user_pro_id'] . '\'>Mon compte</a></li>'; //lien professionnel_detail des utilisateurs 'professionnels'
}

$liens_reference = '';
if (secu_is_authorized(DROIT_TERRITOIRE)) {
	$liens_reference .= '<li><a href=\'territoire.php\'>Territoires</a></li>';
}
if (secu_is_authorized(DROIT_THEME)) {
	$liens_reference .= '<li><a href=\'theme.php\'>Thèmes et sous-thèmes</a></li>';
}
if (secu_is_authorized(DROIT_CRITERE)) {
	$liens_reference .= '<li><a href=\'formulaire_liste.php\'>Formulaires</a></li>';
}

//********** sélection territoire
if (isset($_POST['choix_territoire'])) {
	$_SESSION['territoire_id'] = $_POST['choix_territoire'];
}

include('../src/admin/select_territoires.inc.php');

//view
require 'view/accueil.tpl.php';