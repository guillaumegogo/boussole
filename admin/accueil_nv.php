<?php

include('../src/admin/bootstrap.php');
secu_check_login();

$liens = null;
$nb = get_nb_nouvelles_demandes(); //nombre de demandes à traiter

//********** sélection territoire
if (isset($_POST['choix_territoire'])) {
	$_SESSION['admin']['perimetre'] = $_POST['choix_territoire'];
}

if($is_authorized = secu_is_authorized('accueil')){
	
	if (isset($is_authorized[DROIT_DEMANDE]) && $is_authorized[DROIT_DEMANDE]) {
		$liens['demandes'] = array('demande_liste.php', 'Demandes à traiter', '('.$nb.')');
		$liens['demandes_traitees'] = array('demande_liste.php?etat=traite', 'Demandes traitées', '');
	}
	if (secu_check_role(ROLE_ADMIN)) {
		$liens['recherches'] = array('recherche_liste.php', 'Recherches effectuées', '');
	}

	if (isset($is_authorized[DROIT_OFFRE]) && $is_authorized[DROIT_OFFRE]) {
		$liens['offres'] = array('offre_liste.php', 'Offres de service', '');
	}
	if (isset($is_authorized[DROIT_MESURE]) && $is_authorized[DROIT_MESURE]) {
		$liens['mesures'] = array('mesure_liste.php', 'Mesurier', '<img src="img/help.png" height="16px" title="Les mesures sont à usage interne. Elles ne sont pas visibles sur le site web.">');
	}

	if (isset($is_authorized[DROIT_PROFESSIONNEL]) && $is_authorized[DROIT_PROFESSIONNEL]) {
		$liens['organismes'] = array('professionnel_liste.php', 'Organismes', '');
	}
	if (isset($is_authorized[DROIT_UTILISATEUR]) && $is_authorized[DROIT_UTILISATEUR]) {
		$liens['utilisateurs'] = array('utilisateur_liste.php', 'Utilisateurs', '');
	} else if (isset($_SESSION['admin']['user_id'])) { 
		$liens['utilisateurs'] = array('utilisateur_detail.php?id=' . $_SESSION['admin']['user_id'], 'Profil utilisateur', ''); // même si on n'a pas les droits sur les utilisateurs, on doit pouvoir voir son propre profil
	}
	if (secu_check_role(ROLE_ADMIN)) {
		$liens['droits'] = array('droits.php', 'Droits d\'accès', '<img src="img/help.png" height="16px" title="Pour information.">');
		$liens['stats'] = array('#', 'Statistiques', '<img src="img/help.png" height="16px" title="Non disponible actuellement.">');
	}

	if (isset($is_authorized[DROIT_TERRITOIRE]) && $is_authorized[DROIT_TERRITOIRE]) {
		$liens['territoires'] = array('territoire_liste.php', 'Territoires', '');
	}
	if (isset($is_authorized[DROIT_THEME]) && $is_authorized[DROIT_THEME]) {
		$liens['themes'] = array('theme_liste.php', 'Thèmes et sous-thèmes', '');
	}
	if (isset($is_authorized[DROIT_FORMULAIRE]) && $is_authorized[DROIT_FORMULAIRE]) {
		$liens['formulaires'] = array('formulaire_liste.php', 'Formulaires', '');
	}

	//view
	require 'view/accueil_nv.tpl.php';
}