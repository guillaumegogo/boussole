<?php

include('../src/admin/bootstrap.php');
secu_check_login();

//******** calcul du nb de demandes (todo : à adapter pour pros et animateurs)
$nb = get_nb_nouvelles_demandes();

if($is_authorized = secu_is_authorized('accueil')){
	//******* construction des listes de lien
	if (isset($is_authorized[DROIT_DEMANDE]) && $is_authorized[DROIT_DEMANDE]) {
		$activites[] = array('demande_liste.php', 'Demandes à traiter', '('.$nb.')');
	}
	if (secu_check_role(ROLE_ADMIN)) {
		$activites[] = array('recherche_liste.php', 'Liste des recherches effectuées', '');
	}

	//******* construction des listes de lien
	if (isset($is_authorized[DROIT_OFFRE]) && $is_authorized[DROIT_OFFRE]) {
		$offres[] = array('offre_liste.php', 'Offres de service', '');
	}
	if (isset($is_authorized[DROIT_MESURE]) && $is_authorized[DROIT_MESURE]) {
		$offres[] = array('mesure_liste.php', 'Mesurier', '<img src="img/help.png" height="16px" title="Les mesures sont à usage interne. Elles ne sont pas visibles sur le site web.">');
	}

	if (isset($is_authorized[DROIT_PROFESSIONNEL]) && $is_authorized[DROIT_PROFESSIONNEL]) {
		$acteurs[] = array('professionnel_liste.php', 'Professionnels', '');
	}
	if (isset($is_authorized[DROIT_UTILISATEUR]) && $is_authorized[DROIT_UTILISATEUR]) {
		$acteurs[] = array('utilisateur_liste.php', 'Utilisateurs', '');
	} else if (isset($_SESSION['user_id'])) { 
		$acteurs[] = array('utilisateur_detail.php?id=' . $_SESSION['user_id'], 'Profil utilisateur', ''); // même si on n'a pas les droits sur les utilisateurs, on doit pouvoir voir son propre profil - CA NE FONCTIONNE PAS POUR LE MOMENT. Voir secu_check_login
	}
	if (secu_check_role(ROLE_ADMIN)) {
		$acteurs[] = array('droits.php', 'Droits d\'accès', '');
	}

	if (isset($is_authorized[DROIT_TERRITOIRE]) && $is_authorized[DROIT_TERRITOIRE]) {
		$references[] = array('territoire_liste.php', 'Territoires', '');
	}
	if (isset($is_authorized[DROIT_THEME]) && $is_authorized[DROIT_THEME]) {
		$references[] = array('theme.php', 'Thèmes et sous-thèmes', '');
	}
	if (isset($is_authorized[DROIT_CRITERE]) && $is_authorized[DROIT_CRITERE]) {
		$references[] = array('formulaire_liste.php', 'Formulaires', '');
	}
}

//********** sélection territoire
if (isset($_POST['choix_territoire'])) {
	$_SESSION['perimetre'] = $_POST['choix_territoire'];
}

//view
require 'view/accueil.tpl.php';