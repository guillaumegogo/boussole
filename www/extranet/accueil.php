<?php

include('../src/admin/bootstrap.php');
secu_check_login();


if($is_authorized = secu_is_authorized('accueil')){
	//******* construction des listes de lien
	if (isset($is_authorized[DROIT_DEMANDE]) && $is_authorized[DROIT_DEMANDE]) {
		
		//******** calcul du nb de demandes à traiter
		$nb_dmd = get_nb_nouvelles_demandes();
		$nb2 = get_nb_nouvelles_demandes('hors-delai');
		$nb_dmd .= ($nb2) ? ', <span style="color:red">dont '.$nb2.' hors-délai</span>' : '';
		
		$activites[] = array('demande_liste.php', 'Demandes à traiter', '('.$nb_dmd.')');
		$activites[] = array('demande_liste.php?etat=traite', 'Demandes traitées');
		$activites[] = array('statistiques.php', 'Statistiques');
		//$activites[] = array('http://statsbeta.mtsfp-vm-djepva-boussole.accelance.net/dashboard/db/boussole_beta?orgId=1&from=1507912332742&to=1515691932742&var-serverurl=beta.boussoledesdroits.fr&var-nombdd=Boussol%20Integ', 'Statistiques', '<img src="img/help.png" height="16px" title="Grafana extérieur.">');
	}
	
	if (isset($is_authorized[DROIT_DEMANDE]) && $is_authorized[DROIT_DEMANDE]) { //à voir pour les droits...
		$animation[] = array('https://collaboratif.jeunesse-vie-associative.gouv.fr/sites/DJEPVA/boussole-des-jeunes/default.aspx', 'Espace collaboratif', '↗');
		$animation[] = array('https://hub.boussoledesjeunes.fr', 'Hub', '↗');
		//$animation[] = array('#', 'Contact DJEPVA');
	}

	//******* construction des listes de lien
	if (isset($is_authorized[DROIT_OFFRE]) && $is_authorized[DROIT_OFFRE]) {
		$offres[] = array('offre_liste.php', 'Offres de service');
	}
	if (isset($is_authorized[DROIT_MESURE]) && $is_authorized[DROIT_MESURE]) {
		$offres[] = array('mesure_liste.php', 'Mesurier', '<img src="img/help.png" height="16px" title="Les mesures sont à usage interne. Elles ne sont pas visibles sur le site web.">');
	}

	if (isset($is_authorized[DROIT_PROFESSIONNEL]) && $is_authorized[DROIT_PROFESSIONNEL]) {
		$acteurs[] = array('professionnel_liste.php', 'Organismes');
	}
	if (isset($is_authorized[DROIT_UTILISATEUR]) && $is_authorized[DROIT_UTILISATEUR]) {
		$acteurs[] = array('utilisateur_liste.php', 'Utilisateurs');
	} else if (isset($_SESSION['admin']['user_id'])) { 
		$acteurs[] = array('utilisateur_detail.php?id=' . $_SESSION['admin']['user_id'], 'Profil utilisateur'); // même si on n'a pas les droits sur les utilisateurs, on doit pouvoir voir son propre profil
	}
	if (secu_check_role(ROLE_ADMIN)) {
		$acteurs[] = array('droits.php', 'Droits d\'accès', '<img src="img/help.png" height="16px" title="Pour information.">');
	}

	if (isset($is_authorized[DROIT_TERRITOIRE]) && $is_authorized[DROIT_TERRITOIRE]) {
		$references[] = array('territoire_liste.php', 'Territoires');
	}
	if (isset($is_authorized[DROIT_THEME]) && $is_authorized[DROIT_THEME]) {
		$references[] = array('theme_liste.php', 'Thèmes');
	}
	if (isset($is_authorized[DROIT_FORMULAIRE]) && $is_authorized[DROIT_FORMULAIRE]) {
		$references[] = array('formulaire_liste.php', 'Formulaires');
	}
	if (secu_check_role(ROLE_ADMIN)) {
		$references[] = array('reference_liste.php', 'Listes déroulantes');
	}
}

//********** sélection territoire
if (isset($_POST['choix_territoire'])) {
	$_SESSION['admin']['perimetre'] = $_POST['choix_territoire'];
}

//view
require 'view/accueil.tpl.php';